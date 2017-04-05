<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tasks\UpdateTaskEvent;
use Webaccess\ProjectSquare\Interactors\Notifications\CreateNotificationInteractor;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Notifications\CreateNotificationRequest;
use Webaccess\ProjectSquare\Requests\Tasks\UpdateTaskRequest;

class UpdateTaskInteractor
{
    public function __construct(TaskRepository $taskRepository, ProjectRepository $projectRepository, UserRepository $userRepository, NotificationRepository $notificationRepository)
    {
        $this->repository = $taskRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute(UpdateTaskRequest $request)
    {
        $task = $this->getTask($request->taskID);
        $this->updateTask($request, $task);
        $this->createNotifications($request, $task);
        $this->dispatchEvent($task->id);
    }

    private function getTask($taskID)
    {
        if (!$task = $this->repository->getTask($taskID)) {
            throw new \Exception(Context::get('translator')->translate('tasks.task_not_found'));
        }

        return $task;
    }

    private function validateProject($projectID)
    {
        if (!$project = $this->projectRepository->getProject($projectID)) {
            throw new \Exception(Context::get('translator')->translate('projects.project_not_found'));
        }
    }

    /**
     * @param UpdateTaskRequest $request
     * @param $task
     * @throws \Exception
     */
    private function updateTask(UpdateTaskRequest $request, $task)
    {
        if ($request->title !== null) $task->title = $request->title;
        if ($request->description !== null) $task->description = $request->description;
        if ($request->estimatedTimeDays !== null) $task->estimatedTimeDays = $request->estimatedTimeDays;
        if ($request->spentTimeDays !== null) $task->spentTimeDays = $request->spentTimeDays;
        if ($request->statusID !== null) $task->statusID = $request->statusID;
        if ($request->allocatedUserID !== 0) $task->allocatedUserID = $request->allocatedUserID;
        if ($request->projectID) {
            $this->validateProject($request->projectID);
            $task->projectID = $request->projectID;
        }
        if ($request->order !== null) $task->order = $request->order;
        $this->repository->persistTask($task);
    }

    /**
     * @param UpdateTaskRequest $request
     * @param $task
     */
    private function createNotifications(UpdateTaskRequest $request, $task)
    {
        if ($request->allocatedUserID != $request->requesterUserID) {
            if ($allocatedUser = $this->userRepository->getUser($request->allocatedUserID)) {
                $this->notifyUser($task, $allocatedUser);
            }
        }
    }

    /**
     * @param $task
     * @param $user
     */
    private function notifyUser($task, $user)
    {
        (new CreateNotificationInteractor($this->notificationRepository))->execute(new CreateNotificationRequest([
            'userID' => $user->id,
            'entityID' => $task->id,
            'type' => 'TASK_UPDATED',
        ]));
    }


    private function dispatchEvent($taskID)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::UPDATE_TASK,
            new UpdateTaskEvent($taskID)
        );
    }
}