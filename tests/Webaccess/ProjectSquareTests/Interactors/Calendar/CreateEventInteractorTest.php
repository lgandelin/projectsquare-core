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
        $this->interactor = new CreateEventInteractor($this->eventRepository);
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
}