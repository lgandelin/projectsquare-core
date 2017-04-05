<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tasks\DeleteTaskEvent;
use Webaccess\ProjectSquare\Interactors\Planning\CreateEventInteractor;
use Webaccess\ProjectSquare\Interactors\Tasks\CreateTaskInteractor;
use Webaccess\ProjectSquare\Interactors\Tasks\DeleteTaskInteractor;
use Webaccess\ProjectSquare\Requests\Planning\CreateEventRequest;
use Webaccess\ProjectSquare\Requests\Tasks\CreateTaskRequest;
use Webaccess\ProjectSquare\Requests\Tasks\DeleteTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\DeleteTaskResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class DeleteTaskInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DeleteTaskInteractor($this->taskRepository, $this->projectRepository, $this->userRepository, $this->eventRepository, $this->notificationRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteNonExistingTask()
    {
        $this->interactor->execute(new DeleteTaskRequest([
            'taskID' => 1,
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteTaskWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $task = $this->createSampleTask($project->id);
        $this->interactor->execute(new DeleteTaskRequest([
            'taskID' => $task->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testDeleteTask()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->projectRepository->addUserToProject($project->id, $user->id, null);
        $task = $this->createSampleTask($project->id);
        $response = $this->interactor->execute(new DeleteTaskRequest([
            'taskID' => $task->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(DeleteTaskResponse::class, $response);
        $this->assertInstanceOf(Task::class, $response->task);
        $this->assertEquals($task->id, $response->task->id);

        //Check deletion
        $this->assertCount(0, $this->taskRepository->objects);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::DELETE_TASK,
            Mockery::type(DeleteTaskEvent::class)
        );
    }

    public function testDeleteTaskAlongWithNotifications()
    {
        $user1 = $this->createSampleUser();
        $user2 = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->projectRepository->addUserToProject($project->id, $user1->id, null);
        $this->projectRepository->addUserToProject($project->id, $user2->id, null);

        $response = (new CreateTaskInteractor(
            $this->taskRepository,
            $this->projectRepository,
            $this->phaseRepository,
            $this->userRepository,
            $this->notificationRepository
        ))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'allocatedUserID' => $user2->id,
            'requesterUserID' => $user1->id
        ]));

        $task = $response->task;

        $this->assertCount(1, $this->notificationRepository->objects);

        $this->interactor->execute(new DeleteTaskRequest([
            'taskID' => $task->id,
            'requesterUserID' => $user1->id
        ]));

        $this->assertCount(0, $this->notificationRepository->objects);
    }

    public function testDeleteTaskAlongWithEvents()
    {
        $user1 = $this->createSampleUser();
        $user2 = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->projectRepository->addUserToProject($project->id, $user1->id, null);
        $this->projectRepository->addUserToProject($project->id, $user2->id, null);

        $response = (new CreateTaskInteractor(
            $this->taskRepository,
            $this->projectRepository,
            $this->phaseRepository,
            $this->userRepository,
            $this->notificationRepository
        ))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'allocatedUserID' => $user2->id,
            'requesterUserID' => $user1->id
        ]));

        $task = $response->task;

        (new CreateEventInteractor(
            $this->eventRepository,
            $this->notificationRepository,
            $this->ticketRepository,
            $this->projectRepository,
            $this->taskRepository,
            $this->userRepository
        ))->execute(new CreateEventRequest([
            'name' => 'Sample event',
            'startTime' => new \DateTime('2016-03-15 10:30:00'),
            'endTime' => new \DateTime('2016-03-15 18:30:00'),
            'userID' => $user2->id,
            'taskID' => $task->id,
            'requesterUserID' => $user1->id,
        ]));

        $this->assertCount(1, $this->eventRepository->objects);

        $this->interactor->execute(new DeleteTaskRequest([
            'taskID' => $task->id,
            'requesterUserID' => $user1->id
        ]));

        $this->assertCount(0, $this->eventRepository->objects);
    }
}