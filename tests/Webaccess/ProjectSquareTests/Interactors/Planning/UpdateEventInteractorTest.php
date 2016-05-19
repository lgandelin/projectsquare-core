<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Planning\UpdateEventEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Interactors\Planning\UpdateEventInteractor;
use Webaccess\ProjectSquare\Requests\Planning\UpdateEventRequest;
use Webaccess\ProjectSquare\Responses\Planning\UpdateEventResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class UpdateEventInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdateEventInteractor($this->eventRepository);
    }

    /*
     * @expectedException Webaccess\ProjectSquare\Exceptions\Events\EventUpdateNotAuthorizedException
     */
    /*public function testUpdateEventWithoutPermission()
    {
        $user1 = $this->createSampleUser();
        $user2 = $this->createSampleUser();
        $event = $this->createSampleEvent($user1->id);

        $this->interactor->execute(new UpdateEventRequest([
            'eventID' => $event->id,
            'startTime' => new \DateTime('2016-03-16 14:30:00'),
            'requesterUserID' => $user2->id
        ]));
    }*/

    /**
     * @expectedException Exception
     */
    public function testUpdateEventWithInvalidDates()
    {
        $user = $this->createSampleUser();
        $event = $this->createSampleEvent($user->id);

        $this->interactor->execute(new UpdateEventRequest([
            'eventID' => $event->id,
            'startTime' => null,
            'endTime' => 'invalid date',
            'requesterUserID' => $user->id
        ]));
    }

    public function testUpdateEvent()
    {
        $user = $this->createSampleUser();
        $event = $this->createSampleEvent($user->id);

        $response = $this->interactor->execute(new UpdateEventRequest([
            'eventID' => $event->id,
            'startTime' => new \DateTime('2016-03-16 14:30:00'),
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(UpdateEventResponse::class, $response);

        //Check update
        $this->assertCount(1, $this->eventRepository->objects);
        $this->assertEquals(new \DateTime('2016-03-16 14:30:00'), $response->event->startTime);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_EVENT,
            Mockery::type(UpdateEventEvent::class)
        );
    }
}