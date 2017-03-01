<?php

use Webaccess\ProjectSquare\Entities\Phase;
use Webaccess\ProjectSquare\Interactors\Phases\GetPhasesInteractor;
use Webaccess\ProjectSquare\Requests\Phases\GetPhasesRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetPhasesInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetPhasesInteractor($this->phaseRepository, $this->taskRepository);
    }

    public function testGetPhasesEmptyProject()
    {
        $project = $this->createSampleProject();
        $phases = $this->interactor->execute(new GetPhasesRequest([
            'projectID' => $project->id,
        ]));

        $this->assertCount(0, $phases);
    }

    public function testGetPhases()
    {
        $project = $this->createSampleProject();
        $this->createSamplePhase($project->id);
        $this->createSamplePhase($project->id);
        $phases = $this->interactor->execute(new GetPhasesRequest([
            'projectID' => $project->id,
        ]));

        $this->assertCount(2, $phases);
        $this->assertInstanceOf(Phase::class, $phases[0]);
        $this->assertEquals('Sample phase', $phases[0]->name);
    }

    public function testGetPhasesWithTasks()
    {

        $project = $this->createSampleProject();
        $phase1 = $this->createSamplePhase($project->id);
        $phase2 = $this->createSamplePhase($project->id);
        $this->createSampleTask($project->id, $phase1->id);
        $this->createSampleTask($project->id, $phase1->id);
        $this->createSampleTask($project->id, $phase2->id);
        $phases = $this->interactor->execute(new GetPhasesRequest([
            'projectID' => $project->id,
        ]));

        $this->assertCount(2, $phases);
        $this->assertInstanceOf(Phase::class, $phases[0]);
        $this->assertEquals('Sample phase', $phases[0]->name);
        $this->assertCount(2, $phases[0]->tasks);
        $this->assertCount(1, $phases[1]->tasks);
    }
}
