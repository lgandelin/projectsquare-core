<?php

namespace Webaccess\ProjectSquare\Interactors\Messages;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Repositories\ConversationRepository;
use Webaccess\ProjectSquare\Repositories\MessageRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Messages\ViewMessageRequest;

class ViewMessageInteractor
{
    protected $repository;
    protected $conversationRepository;
    protected $projectRepository;
    protected $userRepository;

    public function __construct(MessageRepository $repository, ConversationRepository $conversationRepository, UserRepository $userRepository, ProjectRepository $projectRepository)
    {
        $this->repository = $repository;
        $this->conversationRepository = $conversationRepository;
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(ViewMessageRequest $request)
    {
        $this->validate($request);
    }

    private function validate(ViewMessageRequest $request)
    {
        $this->validateMessage($request);
        $this->validateUser($request);
        $this->validateRequesterPermissions($request);
    }

    private function validateMessage(ViewMessageRequest $request)
    {
        if (!$message = $this->repository->getMessage($request->messageID)) {
            throw new \Exception('Message not found');
        }
    }

    private function validateUser(ViewMessageRequest $request)
    {
        if (!$user = $this->userRepository->getUser($request->requesterUserID)) {
            throw new \Exception('User not found');
        }
    }

    private function validateRequesterPermissions(ViewMessageRequest $request)
    {
        if (!$this->isUserAuthorizedToViewMessage($request)) {
            throw new \Exception(Context::get('translator')->translate('users.message_view_not_allowed'));
        }
    }

    private function isUserAuthorizedToViewMessage(ViewMessageRequest $request)
    {
        $message = $this->repository->getMessage($request->messageID);
        $conversation = $this->conversationRepository->getConversation($message->conversationID);
        $project = $this->projectRepository->getProject($conversation->projectID);

        return $this->projectRepository->isUserInProject($project, $request->requesterUserID);
    }
}