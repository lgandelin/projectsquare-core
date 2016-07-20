<?php


use Webaccess\ProjectSquare\Interactors\Tasks\UpdateTaskInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\UpdateTaskRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class UpdateTaskInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdateTaskInteractor($this->taskRepository, $this->projectRepository);
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
}