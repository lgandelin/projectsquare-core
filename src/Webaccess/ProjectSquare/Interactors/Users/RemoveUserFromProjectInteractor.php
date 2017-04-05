<?php

namespace Webaccess\ProjectSquare\Interactors\Users;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Interactors\Tasks\GetTasksInteractor;
use Webaccess\ProjectSquare\Interactors\Tasks\UnallocateTaskInteractor;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Tasks\GetTasksRequest;
use Webaccess\ProjectSquare\Requests\Tasks\UnallocateTaskRequest;
use Webaccess\ProjectSquare\Requests\Users\RemoveUserFromProjectRequest;
use Webaccess\ProjectSquare\Responses\Users\RemoveUserFromProjectResponse;

class RemoveUserFromProjectInteractor
{
    public function __construct(UserRepository $userRepository, ProjectRepository $projectRepository, TaskRepository $taskRepository, EventRepository $eventRepository, NotificationRepository $notificationRepository)
    {
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
        $this->taskRepository = $taskRepository;
        $this->eventRepository = $eventRepository;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @param RemoveUserFromProjectRequest $request
     * @return RemoveUserFromProjectResponse
     */
    public function execute(RemoveUserFromProjectRequest $request)
    {
        $this->validateRequest($request);
        $this->unallocateUserTasks($request);
        $this->projectRepository->removeUserFromProject($request->projectID, $request->userID);

        return new RemoveUserFromProjectResponse([
            'success' => true,
        ]);
    }

    /**
     * @param RemoveUserFromProjectRequest $request
     * @throws \Exception
     */
    private function validateRequest(RemoveUserFromProjectRequest $request)
    {
        $this->validateUser($request);
        $this->validateProject($request);
    }

    /**
     * @param RemoveUserFromProjectRequest $request
     * @throws \Exception
     */
    private function validateUser(RemoveUserFromProjectRequest $request)
    {
        if (!$user = $this->userRepository->getUser($request->userID)) {
            throw new \Exception(Context::get('translator')->translate('users.user_not_found'));
        }
    }

    /**
     * @param RemoveUserFromProjectRequest $request
     * @throws \Exception
     */
    private function validateProject(RemoveUserFromProjectRequest $request)
    {
        if (!$project = $this->projectRepository->getProject($request->projectID)) {
            throw new \Exception(Context::get('translator')->translate('projects.project_not_found'));
        }
    }

    /**
     * @param RemoveUserFromProjectRequest $request
     */
    private function unallocateUserTasks(RemoveUserFromProjectRequest $request)
    {
        $userTasks = (new GetTasksInteractor($this->taskRepository))->execute(new GetTasksRequest([
            'userID' => $request->userID,
            'projectID' => $request->projectID,
            'allocatedUserID' => $request->userID,
        ]));

        foreach ($userTasks as $task) {
            (new UnallocateTaskInteractor($this->taskRepository, $this->projectRepository, $this->eventRepository, $this->notificationRepository, $this->userRepository))->execute(new UnallocateTaskRequest([
                'taskID' => $task->id,
                'requesterUserID' => $request->requesterUserID,
            ]));
        }
    }
}