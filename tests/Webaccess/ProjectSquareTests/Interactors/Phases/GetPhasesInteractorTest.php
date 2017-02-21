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
        $this->interactor = new GetPhasesInteractor($this->phaseRepository);
    }

    public function testPhases0()
    {
        $project = $this->createSampleProject();
        $phases = $this->interactor->execute(new GetPhasesRequest([
            'projectID' => $project->id,
        ]));

        $this->assertCount(0, $phases);
    }

    public function testPhases()
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
}
