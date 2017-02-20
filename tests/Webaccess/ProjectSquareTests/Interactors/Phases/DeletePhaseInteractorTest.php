<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Phases\DeletePhaseEvent;
use Webaccess\ProjectSquare\Interactors\Phases\DeletePhaseInteractor;
use Webaccess\ProjectSquare\Requests\Phases\DeletePhaseRequest;
use Webaccess\ProjectSquare\Responses\Phases\DeletePhaseResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class DeletePhaseInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DeletePhaseInteractor($this->phaseRepository, $this->userRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteNonExistingPhase()
    {
        $user = $this->createSampleUser();
        $this->interactor->execute(new DeletePhaseRequest([
            'phaseID' => 2,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testDeletePhaseWithoutPermission()
    {
        $phase = $this->createSamplePhase();
        $user = $this->createSampleUser();
        $this->interactor->execute(new DeletePhaseRequest([
            'phaseID' => $phase->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testDeletePhase()
    {
        $phase = $this->createSamplePhase();
        $user = $this->createSampleUser(true);
        $response = $this->interactor->execute(new DeletePhaseRequest([
            'phaseID' => $phase->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(DeletePhaseResponse::class, $response);
        $this->assertEquals($phase->id, $response->phaseID);

        //Check deletion
        $this->assertCount(0, $this->phaseRepository->objects);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::DELETE_PHASE,
            Mockery::type(DeletePhaseEvent::class)
        );
    }

}