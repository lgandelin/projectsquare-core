<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Event;
use Webaccess\ProjectSquare\Events\Calendar\DeleteEventEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Interactors\Calendar\DeleteEventInteractor;
use Webaccess\ProjectSquare\Requests\Calendar\DeleteEventRequest;
use Webaccess\ProjectSquare\Responses\Calendar\DeleteEventResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class DeleteEventInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DeleteEventInteractor($this->eventRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteNonExistingEvent()
    {
        $this->interactor->execute(new DeleteEventRequest([
            'eventID' => 1,
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteEventWithoutPermission()
    {
        $user = $this->createSampleUser();
        $event = $this->createSampleEvent($user->id);
        $this->interactor->execute(new DeleteEventRequest([
            'eventID' => $event->id,
            'requesterUserID' => 2
        ]));
    }

    public function testDeleteEvent()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $event = $this->createSampleEvent($user->id);
        $response = $this->interactor->execute(new DeleteEventRequest([
            'eventID' => $event->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(DeleteEventResponse::class, $response);
        $this->assertInstanceOf(Event::class, $response->event);
        $this->assertEquals($event->id, $response->event->id);

        //Check deletion
        $this->assertCount(0, $this->eventRepository->objects);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::DELETE_EVENT,
            Mockery::type(DeleteEventEvent::class)
        );
    }
}