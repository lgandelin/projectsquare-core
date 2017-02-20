<?php

use Webaccess\ProjectSquare\Entities\Phase;
use Webaccess\ProjectSquare\Interactors\Phases\GetPhaseInteractor;
use Webaccess\ProjectSquare\Requests\Phases\GetPhaseRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetPhaseInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetPhaseInteractor($this->phaseRepository);
    }

    public function testGetNonExistingPhase()
    {
        $user = $this->createSampleUser();
        $this->assertFalse($this->interactor->execute(new GetPhaseRequest([
            'phaseID' => 1,
            'requesterUserID' => $user->id
        ])));
    }

    public function testGetPhase()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->createSamplePhase($project->id);
        $phase = $this->interactor->execute(new GetPhaseRequest([
            'phaseID' => 1,
            'requesterUserID' => $user->id
        ]));

        $this->assertInstanceOf(Phase::class, $phase);
        $this->assertEquals('Sample phase', $phase->name);
    }
}