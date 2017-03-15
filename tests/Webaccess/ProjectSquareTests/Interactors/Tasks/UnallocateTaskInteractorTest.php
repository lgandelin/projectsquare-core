<?php


use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Planning\CreateEventInteractor;
use Webaccess\ProjectSquare\Interactors\Tasks\UnallocateTaskInteractor;
use Webaccess\ProjectSquare\Requests\Planning\CreateEventRequest;
use Webaccess\ProjectSquare\Requests\Tasks\UnallocateTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\UnallocateTaskResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class UnallocateTaskInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UnallocateTaskInteractor($this->taskRepository, $this->projectRepository, $this->eventRepository, $this->notificationRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testUnallocateUnknownTask()
    {
        $user = $this->createSampleUser();

        $this->interactor->execute(new UnallocateTaskRequest([
            'taskID' => $user->id,
            'requesterUserID' => $user->id,
        ]));
    }

    public function testUnallocateTask()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();

        //Create task
        $task = $this->createSampleTask($project->id, null, null, Task::TODO, $user->id);

        //Create linked event
        (new CreateEventInteractor(
            $this->eventRepository,
            $this->notificationRepository,
            $this->ticketRepository,
            $this->projectRepository,
            $this->taskRepository
        ))->execute(new CreateEventRequest([
            'name' => 'Sample event',
            'startTime' => new \DateTime('2016-03-15 10:30:00'),
            'endTime' => new \DateTime('2016-03-15 18:30:00'),
            'userID' => $user->id,
            'taskID' => $task->id,
            'requesterUserID' => $user->id,
        ]));

        $this->assertCount(1, $this->eventRepository->objects);

        $response = $this->interactor->execute(new UnallocateTaskRequest([
            'taskID' => $task->id,
            'requesterUserID' => $user->id,
        ]));

        $task = $this->taskRepository->getTask($task->id);
        $this->assertInstanceOf(UnallocateTaskResponse::class, $response);
        $this->assertEquals(null, $task->allocatedUserID);
        $this->assertTrue($response->success);

        //Check that events have been deleted
        $this->assertCount(0, $this->eventRepository->objects);
    }
}