<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Calendar\UpdateEventEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Interactors\Calendar\UpdateEventInteractor;
use Webaccess\ProjectSquare\Requests\Calendar\UpdateEventRequest;
use Webaccess\ProjectSquare\Responses\Calendar\UpdateEventResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class UpdateEventInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdateEventInteractor($this->eventRepository);
    }

    public function testUpdateEvent()
    {
        $user = $this->createSampleUser();
        $event = $this->createSampleEvent($user->id);

        $response = $this->interactor->execute(new UpdateEventRequest([
            'eventID' => $event->id,
            'startTime' => new \DateTime('2016-03-16 14:30:00'),
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