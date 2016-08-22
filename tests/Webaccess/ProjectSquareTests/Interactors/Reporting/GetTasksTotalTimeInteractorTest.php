<?php

use Webaccess\ProjectSquare\Interactors\Reporting\GetTasksTotalTimeInteractor;
use Webaccess\ProjectSquare\Interactors\Tasks\CreateTaskInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\CreateTaskRequest;
use Webaccess\ProjectSquare\Responses\Reporting\GetTasksTotalTimeResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetTasksTotalTimeInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetTasksTotalTimeInteractor($this->taskRepository);
    }

    public function testGetTotalEstimatedTimeWithoutTask()
    {
        $project = $this->createSampleProject();

        $this->assertEquals(new GetTasksTotalTimeResponse(['days' => 0, 'hours' => 0]), $this->interactor->getTasksTotalEstimatedTime($project->id));
    }

    public function testGetTotalEstimatedTimeWithOneTask()
    {
        $project = $this->createSampleProject();
        $this->createTask([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'estimatedTimeDays' => 2.5
        ]);

        $this->assertEquals(new GetTasksTotalTimeResponse(['days' => 2.5, 'hours' => 0]), $this->interactor->getTasksTotalEstimatedTime($project->id));
    }

    public function testGetTotalEstimatedTimeWithDaysAndHours()
    {
        $project = $this->createSampleProject();
        $this->createTask([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'estimatedTimeDays' => 3.5,
            'estimatedTimeHours' => 6
        ]);

        $this->assertEquals(new GetTasksTotalTimeResponse(['days' => 3.5, 'hours' => 6]), $this->interactor->getTasksTotalEstimatedTime($project->id));
    }

    public function testGetTotalEstimatedTimeWithHoursModulo()
    {
        $project = $this->createSampleProject();
        $this->createTask([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'estimatedTimeDays' => 3,
            'estimatedTimeHours' => 7
        ]);

        $this->assertEquals(new GetTasksTotalTimeResponse(['days' => 4, 'hours' => 0]), $this->interactor->getTasksTotalEstimatedTime($project->id));
    }

    public function testGetTotalEstimatedTimeWithTwoTasks()
    {
        $project = $this->createSampleProject();
        $this->createTask([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'estimatedTimeDays' => 2,
            'estimatedTimeHours' => 4
        ]);

        $this->createTask([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'estimatedTimeDays' => 3,
            'estimatedTimeHours' => 6
        ]);

        $this->assertEquals(new GetTasksTotalTimeResponse(['days' => 6, 'hours' => 3]), $this->interactor->getTasksTotalEstimatedTime($project->id));
    }

    public function testGetTotalSpentTimeWithoutTask()
    {
        $project = $this->createSampleProject();

        $this->assertEquals(new GetTasksTotalTimeResponse(['days' => 0, 'hours' => 0]), $this->interactor->getTasksTotalSpentTime($project->id));
    }

    public function testGetTotalSpentTimeWithOneTask()
    {
        $project = $this->createSampleProject();
        $this->createTask([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'spentTimeDays' => 2.5
        ]);

        $this->assertEquals(new GetTasksTotalTimeResponse(['days' => 2.5, 'hours' => 0]), $this->interactor->getTasksTotalSpentTime($project->id));
    }

    public function testGetTotalSpentTimeWithDaysAndHours()
    {
        $project = $this->createSampleProject();
        $this->createTask([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'spentTimeDays' => 3.5,
            'spentTimeHours' => 6
        ]);

        $this->assertEquals(new GetTasksTotalTimeResponse(['days' => 3.5, 'hours' => 6]), $this->interactor->getTasksTotalSpentTime($project->id));
    }

    public function testGetTotalSpentTimeWithHoursModulo()
    {
        $project = $this->createSampleProject();
        $this->createTask([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'spentTimeDays' => 3,
            'spentTimeHours' => 7
        ]);

        $this->assertEquals(new GetTasksTotalTimeResponse(['days' => 4, 'hours' => 0]), $this->interactor->getTasksTotalSpentTime($project->id));
    }

    public function testGetTotalSpentTimeWithTwoTasks()
    {
        $project = $this->createSampleProject();
        $this->createTask([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'spentTimeDays' => 2,
            'spentTimeHours' => 4
        ]);

        $this->createTask([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'spentTimeDays' => 3,
            'spentTimeHours' => 6
        ]);

        $this->assertEquals(new GetTasksTotalTimeResponse(['days' => 6, 'hours' => 3]), $this->interactor->getTasksTotalSpentTime($project->id));
    }

    private function createTask($parameters)
    {
        (new CreateTaskInteractor($this->taskRepository, $this->projectRepository, $this->userRepository, $this->notificationRepository))->execute(new CreateTaskRequest($parameters));
    }
}