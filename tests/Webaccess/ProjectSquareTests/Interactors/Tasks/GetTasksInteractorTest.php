<?php

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Tasks\GetTasksInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\GetTasksRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetTasksInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetTasksInteractor($this->taskRepository);
    }

    public function testGetTasks()
    {
        $task1 = new Task();
        $task1->projectID = 1;

        $task2 = new Task();
        $task2->projectID = 2;

        $this->taskRepository->objects = [
            $task1,
            $task2
        ];

        $this->assertCount(1, $this->interactor->execute(new GetTasksRequest([
            'projectID' => 1
        ])));

        $this->assertCount(2, $this->interactor->execute(new GetTasksRequest()));
    }
}