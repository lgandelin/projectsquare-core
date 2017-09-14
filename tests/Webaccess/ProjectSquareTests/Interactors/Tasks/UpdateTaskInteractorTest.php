<?php


use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tasks\UpdateTaskEvent;
use Webaccess\ProjectSquare\Interactors\Tasks\UpdateTaskInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\UpdateTaskRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class UpdateTaskInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdateTaskInteractor($this->taskRepository, $this->projectRepository, $this->userRepository, $this->notificationRepository);
    }

    public function testUpdateTask()
    {
        $project = $this->createSampleProject();
        $task = $this->createSampleTask($project->id);

        $this->interactor->execute(new UpdateTaskRequest([
            'taskID' => $task->id,
            'title' => 'Tâche modifiée',
        ]));

        $this->assertEquals('Tâche modifiée', $this->taskRepository->objects[$task->id]->title);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_TASK,
            Mockery::type(UpdateTaskEvent::class)
        );
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateWithNonExistingTask()
    {
        $project = $this->createSampleProject();
        $task = $this->createSampleTask($project->id);

        $this->interactor->execute(new UpdateTaskRequest([
            'taskID' => $task->id,
            'projectID' => 2,
        ]));
    }

    public function testUpdateTaskCheckNotifications()
    {
        $project = $this->createSampleProject();
        $user1 = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project->id, $user1->id, null);
        $user2 = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project->id, $user2->id, null);

        $task = $this->createSampleTask($project->id);

        $this->interactor->execute(new UpdateTaskRequest([
            'taskID' => $task->id,
            'title' => 'Tâche modifiée',
            'allocatedUserID' => $user2->id,
            'requesterUserID' => $user1->id,
        ]));

        $this->assertCount(1, $this->notificationRepository->objects);
    }
}