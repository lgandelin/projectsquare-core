<?php

namespace Webaccess\ProjectSquare\Interactors\Users;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Interactors\Notifications\CreateNotificationInteractor;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Notifications\CreateNotificationRequest;
use Webaccess\ProjectSquare\Requests\Users\AddUserToProjectRequest;
use Webaccess\ProjectSquare\Responses\Users\AddUserToProjectResponse;

class AddUserToProjectInteractor
{
    public function __construct(UserRepository $userRepository, ProjectRepository $projectRepository, NotificationRepository $notificationRepository)
    {
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute(AddUserToProjectRequest $request)
    {
        $this->validateRequest($request);
        $this->checkIfUserNotAlreadyInProject($request);
        $this->projectRepository->addUserToProject($request->projectID, $request->userID, $request->roleID);
        $this->notifyUser($request);

        return new AddUserToProjectResponse([
            'success' => true,
        ]);
    }

    private function validateRequest(AddUserToProjectRequest $request)
    {
        $this->validateUser($request);
        $this->validateProject($request);
    }

    /**
     * @param AddUserToProjectRequest $request
     * @throws \Exception
     */
    private function validateUser(AddUserToProjectRequest $request)
    {
        if (!$user = $this->userRepository->getUser($request->userID)) {
            throw new \Exception(Context::get('translator')->translate('users.user_not_found'));
        }
    }

    /**
     * @param AddUserToProjectRequest $request
     * @throws \Exception
     */
    private function validateProject(AddUserToProjectRequest $request)
    {
        if (!$project = $this->projectRepository->getProject($request->projectID)) {
            throw new \Exception(Context::get('translator')->translate('projects.project_not_found'));
        }
    }

    private function checkIfUserNotAlreadyInProject($request)
    {
        if ($this->projectRepository->isUserInProject($request->projectID, $request->userID)) {
            throw new \Exception(Context::get('translator')->translate('projects.user_already_in_project'));
        }
    }

    private function notifyUser(AddUserToProjectRequest $request)
    {
        if ($request->userID != $request->requesterUserID) {
            (new CreateNotificationInteractor($this->notificationRepository))->execute(new CreateNotificationRequest([
                'userID' => $request->userID,
                'entityID' => $request->projectID,
                'type' => 'ASSIGNED_TO_PROJECT',
            ]));
        }
    }
}