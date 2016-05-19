<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Event;
use Webaccess\ProjectSquare\Events\Events\DeleteEventEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Interactors\Events\CreateEventInteractor;
use Webaccess\ProjectSquare\Interactors\Events\DeleteEventInteractor;
use Webaccess\ProjectSquare\Requests\Events\CreateEventRequest;
use Webaccess\ProjectSquare\Requests\Events\DeleteEventRequest;
use Webaccess\ProjectSquare\Responses\Events\DeleteEventResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class DeleteEventInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DeleteEventInteractor($this->eventRepository, $this->notificationRepository);
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

    public function testDeleteEvent()
    {
        $user = $this->createSampleUser();
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

    public function testDeleteAnotherUserEvent()
    {
        $user = $this->createSampleUser();

        $response = (new CreateEventInteractor(
            $this->eventRepository,
            $this->notificationRepository,
            $this->ticketRepository,
            $this->projectRepository
        ))->execute(new CreateEventRequest([
            'name' => 'Sample event',
            'startTime' => new \DateTime('2016-03-15 10:30:00'),
            'endTime' => new \DateTime('2016-03-15 18:30:00'),
            'userID' => $user->id,
            'requesterUserID' => 2,
        ]));

        $response = $this->interactor->execute(new DeleteEventRequest([
            'eventID' => $response->event->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(DeleteEventResponse::class, $response);
        $this->assertInstanceOf(Event::class, $response->event);
        $this->assertEquals($response->event->id, $response->event->id);

        //Check deletion
        $this->assertCount(0, $this->eventRepository->objects);

        //Check that notifications are deleted
        $this->assertEquals(0, count($this->notificationRepository->getNotifications($user->id)));
    }
}