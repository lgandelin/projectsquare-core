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
            'taskID' => 1,
            'title' => 'Tâche modifiée',
        ]));

        $this->assertEquals('Tâche modifiée', $this->taskRepository->objects[1]->title);
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateWithNonExistingTask()
    {
        $project = $this->createSampleProject();
        $task = $this->createSampleTask($project->id);

        $this->interactor->execute(new UpdateTaskRequest([
            'taskID' => 1,
            'title' => 'Tâche modifiée',
            'projectID' => 2,
        ]));

        $this->assertEquals('Tâche modifiée', $this->taskRepository->objects[1]->title);
    }
}