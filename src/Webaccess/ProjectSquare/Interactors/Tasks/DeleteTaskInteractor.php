<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Planning\DeleteEventInteractor;
use Webaccess\ProjectSquare\Interactors\Planning\GetEventsInteractor;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Planning\DeleteEventRequest;
use Webaccess\ProjectSquare\Requests\Planning\GetEventsRequest;
use Webaccess\ProjectSquare\Requests\Tasks\DeleteTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\DeleteTaskResponse;

class DeleteTaskInteractor
{
    public function __construct(TaskRepository $taskRepository, ProjectRepository $projectRepository, UserRepository $userRepository, EventRepository $eventRepository, NotificationRepository $notificationRepository)
    {
        $this->repository = $taskRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->eventRepository = $eventRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute(DeleteTaskRequest $request)
    {
        $task = $this->getTask($request->taskID);
        $this->validateRequest($request, $task);
        $this->deleteLinkedNotifications($task->id);
        $this->deleteLinkedEvents($task->id);
        $this->deleteTask($task);

        return new DeleteTaskResponse([
            'task' => $task,
        ]);
    }

    private function validateRequest(DeleteTaskRequest $request, Task $task)
    {
        $this->validateRequesterPermissions($request, $task);
    }

    private function getTask($taskID)
    {
        if (!$task = $this->repository->getTask($taskID)) {
            throw new \Exception(Context::get('translator')->translate('tasks.task_not_found'));
        }

        return $task;
    }

    private function deleteTask(Task $task)
    {
        $this->repository->deleteTask($task->id);
    }

    private function validateRequesterPermissions(DeleteTaskRequest $request, Task $task)
    {
        if (!$this->isUserAuthorizedToDeleteTask($request->requesterUserID, $task)) {
            throw new \Exception(Context::get('translator')->translate('users.task_deletion_not_allowed'));
        }
    }

    private function isUserAuthorizedToDeleteTask($userID, Task $task)
    {
        $user = $this->userRepository->getUser($userID);

        return $this->projectRepository->isUserInProject($task->projectID, $userID) || $user->isAdministrator;
    }

    private function deleteLinkedNotifications($taskID)
    {
        $this->notificationRepository->removeNotificationsByTypeAndEntityID('TASK_CREATED', $taskID);
    }

    private function deleteLinkedEvents($taskID)
    {
        $events = (new GetEventsInteractor($this->eventRepository))->execute(new GetEventsRequest([
            'taskID' => $taskID
        ]));

        if (is_array($events) && sizeof($events) > 0) {
            foreach ($events as $event) {
                (new DeleteEventInteractor($this->eventRepository, $this->notificationRepository))->execute(new DeleteEventRequest([
                    'eventID' => $event->id
                ]));
            }
        }
    }
}