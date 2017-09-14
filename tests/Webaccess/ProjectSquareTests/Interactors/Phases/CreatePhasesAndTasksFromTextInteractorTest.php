<?php

use Webaccess\ProjectSquare\Interactors\Phases\CreatePhasesAndTasksFromTextInteractor;
use Webaccess\ProjectSquare\Requests\Phases\CreatePhasesAndTasksFromTextRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class CreatePhasesAndTasksFromTextInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreatePhasesAndTasksFromTextInteractor($this->phaseRepository, $this->taskRepository, $this->projectRepository, $this->userRepository, $this->notificationRepository);
    }

    public function testCreatePhasesAndTasksFromText0()
    {
        $project = $this->createSampleProject();

        $this->assertCount(0, $this->phaseRepository->objects);
        $this->assertCount(0, $this->taskRepository->objects);

        $this->interactor->execute(new CreatePhasesAndTasksFromTextRequest([
            'text' => '',
            'projectID' => $project->id
        ]));

        $this->assertCount(0, $this->phaseRepository->objects);
        $this->assertCount(0, $this->taskRepository->objects);
    }

    public function testCreatePhasesAndTasksFromText1()
    {
        $project = $this->createSampleProject();
        $this->assertCount(0, $this->phaseRepository->objects);
        $this->assertCount(0, $this->taskRepository->objects);

        $this->interactor->execute(new CreatePhasesAndTasksFromTextRequest([
            'text' => '# Phase 1

Tâche 1;0.5

# Phase 2

Tâche 2;3
Tâche 3;5.5',
            'projectID' => $project->id
        ]));

        //Assert phases and tasks inserted
        $this->assertCount(2, $this->phaseRepository->objects);
        $this->assertCount(3, $this->taskRepository->objects);

        //Phase 1
        $phase1 = $this->phaseRepository->getPhase(1);
        $this->assertEquals($project->id, $phase1->projectID);
        $this->assertEquals('Phase 1', $phase1->name);

        $task1 = $this->taskRepository->getTask(1);
        $this->assertEquals('Tâche 1', $task1->title);
        $this->assertEquals(0.5, $task1->estimatedTimeDays);
        $this->assertEquals($phase1->id, $task1->phaseID);

        //Phase 2
        $phase2 = $this->phaseRepository->getPhase(2);
        $this->assertEquals($project->id, $phase2->projectID);
        $this->assertEquals('Phase 2', $phase2->name);

        $task2 = $this->taskRepository->getTask(2);
        $this->assertEquals('Tâche 2', $task2->title);
        $this->assertEquals(3, $task2->estimatedTimeDays);
        $this->assertEquals($phase2->id, $task2->phaseID);

        $task3 = $this->taskRepository->getTask(3);
        $this->assertEquals('Tâche 3', $task3->title);
        $this->assertEquals(5.5, $task3->estimatedTimeDays);
        $this->assertEquals($phase2->id, $task3->phaseID);
    }

    public function testCreatePhasesAndTasksFromTextWithCommaInDuration()
    {
        $project = $this->createSampleProject();
        $this->assertCount(0, $this->phaseRepository->objects);
        $this->assertCount(0, $this->taskRepository->objects);

        $this->interactor->execute(new CreatePhasesAndTasksFromTextRequest([
            'text' => '# Phase 1

Tâche 1;0,5

# Phase 2

Tâche 2;3
Tâche 3;5,5',
            'projectID' => $project->id
        ]));

        //Assert phases and tasks inserted
        $this->assertCount(2, $this->phaseRepository->objects);
        $this->assertCount(3, $this->taskRepository->objects);

        //Phase 1
        $phase1 = $this->phaseRepository->getPhase(1);
        $this->assertEquals($project->id, $phase1->projectID);
        $this->assertEquals('Phase 1', $phase1->name);

        $task1 = $this->taskRepository->getTask(1);
        $this->assertEquals('Tâche 1', $task1->title);
        $this->assertEquals(0.5, $task1->estimatedTimeDays);
        $this->assertEquals($phase1->id, $task1->phaseID);

        //Phase 2
        $phase2 = $this->phaseRepository->getPhase(2);
        $this->assertEquals($project->id, $phase2->projectID);
        $this->assertEquals('Phase 2', $phase2->name);

        $task2 = $this->taskRepository->getTask(2);
        $this->assertEquals('Tâche 2', $task2->title);
        $this->assertEquals(3, $task2->estimatedTimeDays);
        $this->assertEquals($phase2->id, $task2->phaseID);

        $task3 = $this->taskRepository->getTask(3);
        $this->assertEquals('Tâche 3', $task3->title);
        $this->assertEquals(5.5, $task3->estimatedTimeDays);
        $this->assertEquals($phase2->id, $task3->phaseID);
    }
}