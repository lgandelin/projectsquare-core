<?php

namespace Webaccess\ProjectSquare\Responses\Progress;

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Reporting\GetReportingIndicatorsInteractor;
use Webaccess\ProjectSquare\Interactors\Tasks\CreateTaskInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\CreateTaskRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetReportingIndicatorsTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetReportingIndicatorsInteractor($this->taskRepository);
    }

    public function testGetReportingIndicatorWithZeroTasks()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);

        $this->assertEquals(0, $this->interactor->getProgressPercentage($user->id, $project->id, 10));
    }

    public function testGetReportingIndicator()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject(10);
        $this->projectRepository->addUserToProject($project, $user, null);

        (new CreateTaskInteractor($this->taskRepository, $this->projectRepository, $this->userRepository, $this->notificationRepository))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'statusID' => Task::TODO,
            'estimatedTimeDays' => 4,
        ]));

        (new CreateTaskInteractor($this->taskRepository, $this->projectRepository, $this->userRepository, $this->notificationRepository))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'statusID' => Task::COMPLETED,
            'estimatedTimeDays' => 1,
        ]));

        (new CreateTaskInteractor($this->taskRepository, $this->projectRepository, $this->userRepository, $this->notificationRepository))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $project->id,
            'statusID' => Task::COMPLETED,
            'estimatedTimeDays' => 5,
        ]));

        $this->assertEquals(60, $this->interactor->getProgressPercentage($user->id, $project->id, 10));
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