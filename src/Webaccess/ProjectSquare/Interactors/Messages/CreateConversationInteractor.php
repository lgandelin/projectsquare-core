<?php

namespace Webaccess\ProjectSquare\Interactors\Messages;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Conversation;
use Webaccess\ProjectSquare\Entities\Message;
use Webaccess\ProjectSquare\Events\Messages\CreateConversationEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Repositories\ConversationRepository;
use Webaccess\ProjectSquare\Repositories\MessageRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Messages\CreateConversationRequest;
use Webaccess\ProjectSquare\Requests\Messages\CreateMessageRequest;
use Webaccess\ProjectSquare\Responses\Messages\CreateConversationResponse;

class CreateConversationInteractor
{
    public function __construct(ConversationRepository $repository, MessageRepository $messageRepository, UserRepository $userRepository, ProjectRepository $projectRepository)
    {
        $this->repository = $repository;
        $this->messageRepository = $messageRepository;
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(CreateConversationRequest $request)
    {
        $this->validateRequest($request);
        $conversation = $this->createConversation($request);
        $message = $this->createMessage($request->message, $conversation->id, $request->requesterUserID);
        $this->dispatchEvent($conversation, $message);

        return new CreateConversationResponse([
            'conversation' => $conversation,
            'message' => $message,
        ]);
    }

    private function validateRequest(CreateConversationRequest $request)
    {
        $this->validateRequesterPermissions($request);
    }

    private function validateRequesterPermissions(CreateConversationRequest $request)
    {
        if (!$this->isUserAuthorizedToCreateConversation($request)) {
            throw new \Exception(Context::get('translator')->translate('users.conversation_creation_not_allowed'));
        }
    }

    private function isUserAuthorizedToCreateConversation(CreateConversationRequest $request)
    {
        $project = $this->projectRepository->getProject($request->projectID);

        return $this->projectRepository->isUserInProject($project, $request->requesterUserID);
    }

    private function createConversation(CreateConversationRequest $request)
    {
        $conversation = new Conversation();
        $conversation->title = $request->title;
        $conversation->projectID = $request->projectID;

        return $this->repository->persistConversation($conversation);
    }

    private function createMessage($content, $conversationID, $userID)
    {
        $response = (new CreateMessageInteractor($this->messageRepository, $this->repository, $this->userRepository, $this->projectRepository))->execute(new CreateMessageRequest([
            'content' => $content,
            'conversationID' => $conversationID,
            'requesterUserID' => $userID,
        ]));

        return $response->message;
    }

    private function dispatchEvent(Conversation $conversation, Message $message)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::CREATE_CONVERSATION,
            new CreateConversationEvent($conversation, $message)
        );
    }
}
