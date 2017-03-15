<?php

namespace Webaccess\ProjectSquare\Interactors\Users;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Users\AddUserToProjectRequest;
use Webaccess\ProjectSquare\Responses\Users\AddUserToProjectResponse;

class AddUserToProjectInteractor
{
    public function __construct(UserRepository $userRepository, ProjectRepository $projectRepository)
    {
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(AddUserToProjectRequest $request)
    {
        $this->validateRequest($request);
        $this->checkIfUserNotAlreadyInProject($request);
        $this->projectRepository->addUserToProject($request->projectID, $request->userID, $request->roleID);

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
}