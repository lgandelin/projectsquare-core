<?php

namespace Webaccess\ProjectSquare\Responses\Progress;

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Progress\GetProgressIndicatorsInteractor;
use Webaccess\ProjectSquare\Interactors\Tasks\CreateTaskInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\CreateTaskRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetProgressIndicatorsTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetProgressIndicatorsInteractor($this->taskRepository);
    }

    public function testGetProgressIndicatorWithZeroTasks()
    {
        $project = $this->createSampleProject();

        $this->assertEquals(0, $this->interactor->getProgressPercentage($project->id));
    }

    public function testGetProgressIndicator()
    {
        $project = $this->createSampleProject();

        (new CreateTaskInteractor($this->taskRepository, $this->projectRepository))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'statusID' => Task::TODO
        ]));

        (new CreateTaskInteractor($this->taskRepository, $this->projectRepository))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'statusID' => Task::COMPLETED
        ]));

        (new CreateTaskInteractor($this->taskRepository, $this->projectRepository))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'statusID' => Task::COMPLETED
        ]));

        $this->assertEquals(66, $this->interactor->getProgressPercentage($project->id));
    }

    public function testGetProfitabilityIndicator()
    {
        $spentTime = new \StdClass();
        $spentTime->days = 13.5;
        $spentTime->hours = 0;

        $this->assertEquals(0, $this->interactor->getProfitabilityPercentage(13.5, $spentTime));
    }

    public function testGetProfitabilityIndicator2()
    {
        $spentTime = new \StdClass();
        $spentTime->days = 4;
        $spentTime->hours = 0;

        $this->assertEquals(-100, $this->interactor->getProfitabilityPercentage(2, $spentTime));
    }

    public function testGetProfitabilityIndicator3()
    {
        $spentTime = new \StdClass();
        $spentTime->days = 8;
        $spentTime->hours = 0;

        $this->assertEquals(20, $this->interactor->getProfitabilityPercentage(10, $spentTime));
    }
}