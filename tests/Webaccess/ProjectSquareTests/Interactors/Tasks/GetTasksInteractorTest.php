<?php

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Tasks\GetTasksInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\GetTasksRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetTasksInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetTasksInteractor($this->taskRepository);
    }

    public function tearDown()
    {
        $this->taskRepository->objects = [];
    }

    public function testGetTasks()
    {
        $project1 = $this->createSampleProject();
        $project2 = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project1->id, $user->id, null);
        $this->projectRepository->addUserToProject($project2->id, $user->id, null);

        $task1 = new Task();
        $task1->projectID = $project1->id;

        $task2 = new Task();
        $task2->projectID = $project2->id;

        $this->taskRepository->objects = [
            $task1,
            $task2
        ];

        $this->assertCount(1, $this->interactor->execute(new GetTasksRequest([
            'userID' => $user->id,
            'projectID' => $project1->id
        ])));

        $this->assertCount(2, $this->interactor->execute(new GetTasksRequest()));
    }

    public function testGetTasksByStatus()
    {
        $task1 = new Task();
        $task1->projectID = 1;
        $task1->statusID = 1;

        $task2 = new Task();
        $task2->projectID = 1;
        $task2->statusID = 1;

        $task3 = new Task();
        $task3->projectID = 1;
        $task3->statusID = 2;

        $task4 = new Task();
        $task4->projectID = 1;
        $task4->statusID = 3;

        $this->taskRepository->objects = [
            $task1,
            $task2,
            $task3,
            $task4
        ];

        $this->assertCount(2, $this->interactor->execute(new GetTasksRequest([
            'projectID' => 1,
            'statusID' => 1,
        ])));
    }

    public function testGetTasksByPhase()
    {
        $project = $this->createSampleProject();
        $phase = $this->createSamplePhase($project->id);
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project->id, $user->id, null);

        $task1 = new Task();
        $task1->projectID = $project->id;
        $task1->phaseID = $phase->id;

        $task2 = new Task();
        $task2->projectID = $project->id;

        $this->taskRepository->objects = [
            $task1,
            $task2
        ];

        $this->assertCount(1, $this->interactor->getTasksByPhaseID(new GetTasksRequest([
            'userID' => $user->id,
            'projectID' => $project->id,
            'phaseID' => $phase->id
        ])));
    }
}