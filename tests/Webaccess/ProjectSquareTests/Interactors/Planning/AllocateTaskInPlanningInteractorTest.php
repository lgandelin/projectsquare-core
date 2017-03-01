<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Planning\CreateEventEvent;
use Webaccess\ProjectSquare\Interactors\Planning\AllocateTaskInPlanningInteractor;
use Webaccess\ProjectSquare\Requests\Planning\AllocateTaskInPlanningRequest;
use Webaccess\ProjectSquare\Responses\Planning\AllocateTaskInPlanningResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class AllocateTaskInPlanningInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new AllocateTaskInPlanningInteractor($this->eventRepository, $this->taskRepository, $this->userRepository, $this->notificationRepository, $this->ticketRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testAllocateUnknownTask()
    {
        $user = $this->createSampleUser();

        $this->interactor->execute(new AllocateTaskInPlanningRequest([
            'userID' => $user->id,
        	'day' => new \DateTime('2017-03-01'),
        	'taskID' => 5,
            'requesterUserID' => $user->id,
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testAllocateTaskWithUnknownuser()
    {
        $user = $this->createSampleUser();
    	$task = $this->createSampleTask();

        $this->interactor->execute(new AllocateTaskInPlanningRequest([
        	'userID' => 8,
        	'day' => new \DateTime('2017-03-01'),
        	'taskID' => $task->id,
            'requesterUserID' => $user->id,
        ]));
    }

    public function testAllocateTask()
    {
    	$user = $this->createSampleUser();
    	$task = $this->createSampleTask(null, null, 2);

    	$response = $this->interactor->execute(new AllocateTaskInPlanningRequest([
        	'userID' => $user->id,
        	'day' => new \DateTime('2017-03-01'),
        	'taskID' => $task->id,
            'requesterUserID' => $user->id,
        ]));

        //Check response
        $this->assertInstanceOf(AllocateTaskInPlanningResponse::class, $response);
        $this->assertEquals('Sample task', $response->event->name);
        $this->assertEquals(new DateTime('2017-03-01 09:00:00'), $response->event->startTime);
        $this->assertEquals(new DateTime('2017-03-02 17:00:00'), $response->event->endTime);
        $this->assertEquals($user->id, $response->event->userID);
        $this->assertEquals($task->id, $response->event->taskID);

        //Check update
        $this->assertCount(1, $this->eventRepository->objects);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_EVENT,
            Mockery::type(CreateEventEvent::class)
        );
    }
}