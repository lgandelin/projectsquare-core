<?php

namespace Webaccess\ProjectSquare\Interactors\Messages;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Repositories\ConversationRepository;
use Webaccess\ProjectSquare\Repositories\MessageRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Messages\ReadMessageRequest;
use Webaccess\ProjectSquare\Responses\Messages\ReadMessageResponse;

class ReadMessageInteractor
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

    public function execute(ReadMessageRequest $request)
    {
        $this->validate($request);
        $this->readMessage($request);

        return new ReadMessageResponse([

        ]);
    }

    private function validate(ReadMessageRequest $request)
    {
        $this->validateMessage($request);
        $this->validateUser($request);
        $this->validateRequesterPermissions($request);
    }

    private function validateMessage(ReadMessageRequest $request)
    {
        if (!$message = $this->repository->getMessage($request->messageID)) {
            throw new \Exception('Message not found');
        }
    }

    private function validateUser(ReadMessageRequest $request)
    {
        if (!$user = $this->userRepository->getUser($request->requesterUserID)) {
            throw new \Exception('User not found');
        }
    }

    private function validateRequesterPermissions(ReadMessageRequest $request)
    {
        if (!$this->isUserAuthorizedToReadMessage($request)) {
            throw new \Exception(Context::get('translator')->translate('users.message_view_not_allowed'));
        }
    }

    private function isUserAuthorizedToReadMessage(ReadMessageRequest $request)
    {
        $message = $this->repository->getMessage($request->messageID);
        $conversation = $this->conversationRepository->getConversation($message->conversationID);
        $project = $this->projectRepository->getProject($conversation->projectID);

        return $this->projectRepository->isUserInProject($project, $request->requesterUserID);
    }

    private function readMessage(ReadMessageRequest $request)
    {
        $user = $this->userRepository->getUser($request->requesterUserID);
        $message = $this->repository->getMessage($request->messageID);
        $this->userRepository->setReadFlagMessage($user->id, $message->id);
    }
}