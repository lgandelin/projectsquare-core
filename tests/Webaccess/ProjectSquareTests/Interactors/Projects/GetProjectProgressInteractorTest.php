<?php

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Phases\GetPhasesInteractor;
use Webaccess\ProjectSquare\Interactors\Projects\GetProjectProgressInteractor;
use Webaccess\ProjectSquare\Requests\Phases\GetPhasesRequest;
use Webaccess\ProjectSquare\Requests\Projects\GetProjectProgressRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetProjectProgressInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetProjectProgressInteractor($this->projectRepository);
    }

    public function testGetProjectProgress()
    {
        $project = $this->createSampleProject();
        $phase1 = $this->createSamplePhase($project->id);
        $phase2 = $this->createSamplePhase($project->id);
        $this->createSampleTask($project->id, $phase1->id, 8, Task::COMPLETED);
        $this->createSampleTask($project->id, $phase1->id, 4);

        $this->createSampleTask($project->id, $phase2->id, 2, Task::COMPLETED);
        $this->createSampleTask($project->id, $phase2->id, 3);

        $phases = (new GetPhasesInteractor($this->phaseRepository, $this->taskRepository))->execute(new GetPhasesRequest([
            'projectID' => $project->id
        ]));

        $progress = $this->interactor->execute(new GetProjectProgressRequest([
            'projectID' => $project->id,
            'phases' => $phases
        ]));

        $this->assertEquals(59, $progress);
    }
}
