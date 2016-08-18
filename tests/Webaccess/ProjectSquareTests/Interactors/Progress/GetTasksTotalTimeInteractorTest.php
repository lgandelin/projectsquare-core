<?php

use Webaccess\ProjectSquare\Interactors\Progress\GetTasksTotalTimeInteractor;
use Webaccess\ProjectSquare\Interactors\Tasks\CreateTaskInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\CreateTaskRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetTasksTotalTimeInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetTasksTotalTimeInteractor($this->taskRepository, $this->projectRepository);
    }

    public function testGetTotalTimeWithoutTask()
    {
        $project = $this->createSampleProject();

        $this->assertEquals([0, 0], $this->interactor->getTasksTotalEstimatedTime($project->id));
    }

    public function testGetTotalTimeWithOneTask()
    {
        $project = $this->createSampleProject();
        (new CreateTaskInteractor($this->taskRepository, $this->projectRepository))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'estimatedTimeDays' => 2.5
        ]));

        $this->assertEquals([2.5, 0], $this->interactor->getTasksTotalEstimatedTime($project->id));
    }

    public function testGetTotalTimeWithDaysAndHours()
    {
        $project = $this->createSampleProject();
        (new CreateTaskInteractor($this->taskRepository, $this->projectRepository))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'estimatedTimeDays' => 3.5,
            'estimatedTimeHours' => 6
        ]));

        $this->assertEquals([3.5, 6], $this->interactor->getTasksTotalEstimatedTime($project->id));
    }

    public function testGetTotalTimeWithHoursModulo()
    {
        $project = $this->createSampleProject();
        (new CreateTaskInteractor($this->taskRepository, $this->projectRepository))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'estimatedTimeDays' => 3,
            'estimatedTimeHours' => 7
        ]));

        $this->assertEquals([4, 0], $this->interactor->getTasksTotalEstimatedTime($project->id));
    }

    public function testGetTotalTimeWithTwoTasks()
    {
        $project = $this->createSampleProject();
        (new CreateTaskInteractor($this->taskRepository, $this->projectRepository))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'estimatedTimeDays' => 2,
            'estimatedTimeHours' => 4
        ]));

        (new CreateTaskInteractor($this->taskRepository, $this->projectRepository))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'estimatedTimeDays' => 3,
            'estimatedTimeHours' => 6
        ]));

        $this->assertEquals([6, 3], $this->interactor->getTasksTotalEstimatedTime($project->id));
    }
}