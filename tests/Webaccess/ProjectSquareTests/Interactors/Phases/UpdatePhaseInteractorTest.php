<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Phases\UpdatePhaseEvent;
use Webaccess\ProjectSquare\Interactors\Phases\UpdatePhaseInteractor;
use Webaccess\ProjectSquare\Requests\Phases\UpdatePhaseRequest;
use Webaccess\ProjectSquare\Responses\Phases\UpdatePhaseResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class UpdatePhaseInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdatePhaseInteractor($this->phaseRepository, $this->projectRepository, $this->userRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateNonExistingPhase()
    {
        $user = $this->createSampleUser();
        $this->interactor->execute(new UpdatePhaseRequest([
            'phaseID' => 1,
            'name' => 'New phase',
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testUpdatePhaseWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $phase = $this->createSamplePhase($project->id);
        $this->interactor->execute(new UpdatePhaseRequest([
            'phaseID' => $phase->id,
            'name' => 'New phase',
            'requesterUserID' => $user->id
        ]));
    }

    public function testUpdatePhase()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser(true);
        $phase = $this->createSamplePhase($project->id);
        $response = $this->interactor->execute(new UpdatePhaseRequest([
            'phaseID' => $phase->id,
            'name' => 'New phase',
            'order' => 8,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(UpdatePhaseResponse::class, $response);
        $this->assertEquals($phase->id, $response->phase->id);
        $this->assertEquals('New phase', $response->phase->name);
        $this->assertEquals(8, $response->phase->order);

        //Check update
        $phase = $this->phaseRepository->getPhase($phase->id);
        $this->assertEquals('New phase', $phase->name);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_PHASE,
            Mockery::type(UpdatePhaseEvent::class)
        );
    }
}