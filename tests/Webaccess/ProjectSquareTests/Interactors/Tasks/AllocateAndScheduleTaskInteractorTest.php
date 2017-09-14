<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Planning\CreateEventEvent;
use Webaccess\ProjectSquare\Interactors\Tasks\AllocateAndScheduleTaskInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\AllocateAndScheduleTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\AllocateAndScheduleTaskResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class AllocateAndScheduleTaskInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new AllocateAndScheduleTaskInteractor($this->eventRepository, $this->taskRepository, $this->userRepository, $this->notificationRepository, $this->ticketRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testAllocateUnknownTask()
    {
        $user = $this->createSampleUser();

        $this->interactor->execute(new AllocateAndScheduleTaskRequest([
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
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
    	$task = $this->createSampleTask($project->id);

        $this->interactor->execute(new AllocateAndScheduleTaskRequest([
        	'userID' => 8,
        	'day' => new \DateTime('2017-03-01'),
        	'taskID' => $task->id,
            'requesterUserID' => $user->id,
        ]));
    }

    public function testAllocateTask()
    {
        $project = $this->createSampleProject();
    	$user = $this->createSampleUser();
    	$task = $this->createSampleTask($project->id, null, 2);

    	$response = $this->interactor->execute(new AllocateAndScheduleTaskRequest([
        	'userID' => $user->id,
        	'day' => new \DateTime('2017-03-01'),
        	'taskID' => $task->id,
            'requesterUserID' => $user->id,
        ]));

        //Check response
        $this->assertInstanceOf(AllocateAndScheduleTaskResponse::class, $response);
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

    public function testAllocateTaskToAUserNonBelongingToTheProject()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $task = $this->createSampleTask($project->id, null, 2);
        $this->assertFalse($this->projectRepository->isUserInProject($project->id, $user->id));

        $this->interactor->execute(new AllocateAndScheduleTaskRequest([
            'userID' => $user->id,
            'day' => new \DateTime('2017-03-01'),
            'taskID' => $task->id,
            'requesterUserID' => $user->id,
        ]));

        $this->assertTrue($this->projectRepository->isUserInProject($project->id, $user->id));
    }
}