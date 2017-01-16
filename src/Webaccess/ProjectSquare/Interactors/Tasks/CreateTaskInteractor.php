<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tasks\CreateTaskEvent;
use Webaccess\ProjectSquare\Interactors\Notifications\CreateNotificationInteractor;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Notifications\CreateNotificationRequest;
use Webaccess\ProjectSquare\Requests\Tasks\CreateTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\CreateTaskResponse;

class CreateTaskInteractor
{
    public function __construct(TaskRepository $taskRepository, ProjectRepository $projectRepository, UserRepository $userRepository, NotificationRepository $notificationRepository)
    {
        $this->repository = $taskRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute(CreateTaskRequest $request)
    {
        $this->validateRequest($request);
        $task = $this->createTicket($request);
        $this->createNotifications($request, $task);
        $this->dispatchEvent($task->id);

        return new CreateTaskResponse([
            'task' => $task,
        ]);
    }

    private function createTicket(CreateTaskRequest $request)
    {
        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->estimatedTimeDays = $request->estimatedTimeDays;
        $task->estimatedTimeHours = $request->estimatedTimeHours;
        $task->spentTimeDays = $request->spentTimeDays;
        $task->spentTimeHours = $request->spentTimeHours;
        $task->statusID = $request->statusID;
        $task->allocatedUserID = $request->allocatedUserID;

        if ($request->projectID) {
            $this->validateProject($request->projectID);
            $task->projectID = $request->projectID;
        }

        return $this->repository->persistTask($task);
    }

    private function validateRequest(CreateTaskRequest $request)
    {
        $this->validateTitle($request);

    }

    private function validateTitle(CreateTaskRequest $request)
    {
        if (!$request->title) {
            throw new \Exception(Context::get('translator')->translate('tasks.title_required'));
        }
    }

    private function validateProject($projectID)
    {
        if (!$project = $this->projectRepository->getProject($projectID)) {
            throw new \Exception(Context::get('translator')->translate('projects.project_not_found'));
        }
    }

    private function createNotifications(CreateTaskRequest $request, Task $task)
    {
        //Agency users
        foreach ($this->userRepository->getUsersByProject($task->projectID) as $user) {
            if ($user->id != $request->requesterUserID) {
                $this->notifyUserIfRequired($task, $user);
            }
        }
    }

    private function notifyUserIfRequired($task, $user)
    {
        (new CreateNotificationInteractor($this->notificationRepository))->execute(new CreateNotificationRequest([
            'userID' => $user->id,
            'entityID' => $task->id,
            'type' => 'TASK_CREATED',
        ]));
    }

    private function dispatchEvent($taskID)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::CREATE_TASK,
            new CreateTaskEvent($taskID)
        );
    }
}