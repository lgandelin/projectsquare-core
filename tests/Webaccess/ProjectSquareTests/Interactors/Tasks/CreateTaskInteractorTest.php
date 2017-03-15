<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tasks\CreateTaskEvent;
use Webaccess\ProjectSquare\Interactors\Tasks\CreateTaskInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\CreateTaskRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class CreateTaskInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateTaskInteractor($this->taskRepository, $this->projectRepository, $this->phaseRepository, $this->userRepository, $this->notificationRepository);
    }

    public function testCreateTask()
    {
        $project = $this->createSampleProject();
        $this->assertCount(0, $this->taskRepository->objects);

        $this->interactor->execute(new CreateTaskRequest([
            'title' => 'Nouvelle tâche',
            'status' => 1,
            'projectID' => $project->id,
        ]));

        $this->assertCount(1, $this->taskRepository->objects);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_TASK,
            Mockery::type(CreateTaskEvent::class)
        );
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTaskWithNonExistingProject()
    {
        $this->interactor->execute(new CreateTaskRequest([
            'title' => 'Nouvelle tâche',
            'status' => 1,
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTaskWithoutTitle()
    { 
        $this->interactor->execute(new CreateTaskRequest([
            'title' => '',
        ]));
    }
}