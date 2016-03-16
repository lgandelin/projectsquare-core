<?php

use Webaccess\ProjectSquare\Interactors\Calendar\GetUserEventsInteractor;
use Webaccess\ProjectSquare\Requests\Calendar\GetEventsRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetEventsInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetUserEventsInteractor($this->eventRepository);
    }

    public function testGetEvents0()
    {
        $user = $this->createSampleUser();
        $events = $this->interactor->execute(new GetEventsRequest([
            'userID' => $user->id,
        ]));

        $this->assertCount(0, $events);
    }

    public function testGetEvents1()
    {
        $user = $this->createSampleUser();
        $this->createSampleEvent($user->id);
        $this->createSampleEvent($user->id);

        $events = $this->interactor->execute(new GetEventsRequest([
            'userID' => $user->id,
        ]));

        $this->assertCount(2, $events);
    }
}