<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Interactors\Planning\CreateEventInteractor;
use Webaccess\ProjectSquare\Interactors\Users\AddUserToProjectInteractor;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\TicketRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Requests\Planning\CreateEventRequest;
use Webaccess\ProjectSquare\Requests\Tasks\AllocateAndScheduleTaskRequest;
use Webaccess\ProjectSquare\Requests\Users\AddUserToProjectRequest;
use Webaccess\ProjectSquare\Responses\Tasks\AllocateAndScheduleTaskResponse;

class AllocateAndScheduleTaskInteractor
{
    const HOURS_IN_DAY = 8;

    protected $repository;
    protected $taskRepository;
    protected $userRepository;
    protected $notificationRepository;
    protected $ticketRepository;
    protected $projectRepository;

    public function __construct(EventRepository $repository, TaskRepository $taskRepository, UserRepository $userRepository, NotificationRepository $notificationRepository, TicketRepository $ticketRepository, ProjectRepository $projectRepository)
    {
        $this->repository = $repository;
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
        $this->notificationRepository = $notificationRepository;
        $this->ticketRepository = $ticketRepository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(AllocateAndScheduleTaskRequest $request)
    {
    	$this->validateRequest($request);
        $task = $this->taskRepository->getTask($request->taskID);
        $this->addUserToProjectIfRequired($request, $task->projectID);
        list($startTime, $endTime) = $this->getStartAndEndDateTimes($request->day, $task->estimatedTimeDays);

    	$response = (new CreateEventInteractor(
    		$this->repository,
    		$this->notificationRepository,
    		$this->ticketRepository,
    		$this->projectRepository,
    		$this->taskRepository,
            $this->userRepository)
    	)->execute(new CreateEventRequest([
            'name' => $task->title,
            'userID' => $request->userID,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'projectID' => $task->projectID,
            'taskID' => $task->id,
            'requesterUserID' => $request->requesterUserID
        ]));

        return new AllocateAndScheduleTaskResponse([
            'event' => $response->event,
        ]);
    }

    private function validateRequest(AllocateAndScheduleTaskRequest $request)
    {
        $this->validateUser($request);
        $this->validateTask($request);
    }

    private function validateTask(AllocateAndScheduleTaskRequest $request)
    {
        if (!$task = $this->taskRepository->getTask($request->taskID)) {
            throw new \Exception(Context::get('translator')->translate('tasks.task_not_found'));
        }
    }

    private function validateUser(AllocateAndScheduleTaskRequest $request)
    {
        if (!$user = $this->userRepository->getUser($request->userID)) {
            throw new \Exception(Context::get('translator')->translate('users.user_not_found'));
        }
    }

    private function getStartAndEndDateTimes($date, $taskDurationInDays = null)
    {
        $startTime = $date->setTime(9, 0, 0);
        $endTime = clone $startTime;

        if ($taskDurationInDays == 0) {
            $taskDurationInDays = 1;
        }

        $i = 0;
        while($i < $taskDurationInDays * self::HOURS_IN_DAY) {
            $endTime->add(new \DateInterval ('PT1H'));
            if ($endTime->format('H') <= 17 && $endTime->format('H') > 9 && $endTime->format('w') != 6 && $endTime->format('w') != 0) {
                $i++;
            }
        }

        return array($startTime, $endTime);
    }

    private function addUserToProjectIfRequired(AllocateAndScheduleTaskRequest $request, $projectID)
    {
        if ($projectID && !$this->projectRepository->isUserInProject($projectID, $request->userID)) {
            (new AddUserToProjectInteractor($this->userRepository, $this->projectRepository))->execute(new AddUserToProjectRequest([
                'userID' => $request->userID,
                'projectID' => $projectID,
                'requesterUserID' => $request->requesterUserID
            ]));
        }
    }
}
