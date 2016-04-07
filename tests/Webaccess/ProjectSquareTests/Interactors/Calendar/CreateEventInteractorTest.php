<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Calendar\CreateEventEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Interactors\Calendar\CreateEventInteractor;
use Webaccess\ProjectSquare\Requests\Calendar\CreateEventRequest;
use Webaccess\ProjectSquare\Responses\Calendar\CreateEventResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class CreateEventInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateEventInteractor($this->eventRepository, $this->notificationRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testCreateEventWithInvalidDates()
    {
        $user = $this->createSampleUser();

        $this->interactor->execute(new CreateEventRequest([
            'name' => 'Sample event',
            'startTime' => null,
            'endTime' => 'invalid',
            'userID' => $user->id,
            'requesterUserID' => $user->id,
        ]));
    }

    public function testCreateEvent()
    {
        $user = $this->createSampleUser();

        $response = $this->interactor->execute(new CreateEventRequest([
            'name' => 'Sample event',
            'startTime' => new \DateTime('2016-03-15 10:30:00'),
            'endTime' => new \DateTime('2016-03-15 18:30:00'),
            'userID' => $user->id,
            'requesterUserID' => $user->id,
        ]));

        //Check response
        $this->assertInstanceOf(CreateEventResponse::class, $response);
        $this->assertEquals('Sample event', $response->event->name);

        //Check insertion
        $this->assertCount(1, $this->eventRepository->objects);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_EVENT,
            Mockery::type(CreateEventEvent::class)
        );
    }

    public function testCreateEventByAnotherUser()
    {
        $user1 = $this->createSampleUser();
        $user2 = $this->createSampleUser();

        $response = $this->interactor->execute(new CreateEventRequest([
            'name' => 'Sample event',
            'startTime' => new \DateTime('2016-03-15 10:30:00'),
            'endTime' => new \DateTime('2016-03-15 18:30:00'),
            'userID' => $user1->id,
            'requesterUserID' => $user2->id,
        ]));

        //Check insertion
        $this->assertCount(1, $this->eventRepository->objects);

        //Check notification
        $this->assertCount(1, $this->notificationRepository->objects);
        $notification = $this->notificationRepository->objects[1];
        $this->assertEquals('EVENT_CREATED', $notification->type);
        $this->assertEquals($response->event->id, $notification->entityID);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_EVENT,
            Mockery::type(CreateEventEvent::class)
        );
    }
}