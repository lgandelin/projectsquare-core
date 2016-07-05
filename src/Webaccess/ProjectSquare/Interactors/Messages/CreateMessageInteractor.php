<?php

namespace Webaccess\ProjectSquare\Interactors\Messages;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Message;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Messages\CreateMessageEvent;
use Webaccess\ProjectSquare\Exceptions\Messages\MessageReplyNotAuthorizedException;
use Webaccess\ProjectSquare\Repositories\ConversationRepository;
use Webaccess\ProjectSquare\Repositories\MessageRepository;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Messages\CreateMessageRequest;
use Webaccess\ProjectSquare\Requests\Notifications\CreateNotificationRequest;
use Webaccess\ProjectSquare\Responses\Messages\CreateMessageResponse;
use Webaccess\ProjectSquare\Responses\Notifications\CreateNotificationInteractor;

class CreateMessageInteractor
{
    protected $repository;
    protected $conversationRepository;
    protected $projectRepository;
    protected $userRepository;

    public function __construct(
        MessageRepository $repository,
        ConversationRepository $conversationRepository,
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        NotificationRepository $notificationRepository
    ) {
        $this->repository = $repository;
        $this->conversationRepository = $conversationRepository;
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute(CreateMessageRequest $request)
    {
        $this->validateRequest($request);
        $message = $this->createMessage($request);
        $this->createNotifications($request, $message);
        $this->dispatchEvent($message);

        return new CreateMessageResponse([
            'message' => $message,
            'createdAt' => new \DateTime(),
            'user' => $this->getUserInfo($request->requesterUserID),
            'count' => $this->getMessageCount($request->conversationID),
        ]);
    }

    private function validateRequest(CreateMessageRequest $request)
    {
        $this->validateConversation($request);
        $this->validateRequesterPermissions($request);
    }

    private function validateConversation(CreateMessageRequest $request)
    {
        if (!$conversation = $this->conversationRepository->getConversation($request->conversationID)) {
            throw new \Exception('Conversation not found');
        }
    }

    private function validateRequesterPermissions(CreateMessageRequest $request)
    {
        if (!$this->isUserAuthorizedToCreateToMessage($request)) {
            throw new MessageReplyNotAuthorizedException(Context::get('translator')->translate('users.message_reply_not_allowed'));
        }
    }

    private function isUserAuthorizedToCreateToMessage(CreateMessageRequest $request)
    {
        $conversation = $this->conversationRepository->getConversation($request->conversationID);
        $project = $this->projectRepository->getProject($conversation->projectID);

        return $this->projectRepository->isUserInProject($project, $request->requesterUserID);
    }

    private function createMessage(CreateMessageRequest $request)
    {
        $message = new Message();
        $message->content = $request->content;
        $message->userID = $request->requesterUserID;
        $message->conversationID = $request->conversationID;

        return $this->repository->persistMessage($message);
    }

    private function createNotifications(CreateMessageRequest $request, Message $message)
    {
        $conversation = $this->conversationRepository->getConversation($message->conversationID);
        $project = $this->projectRepository->getProject($conversation->projectID);

        //Agency users
        foreach ($this->userRepository->getUsersByProject($conversation->projectID) as $user) {
            if ($user->id != $request->requesterUserID) {
                $this->notifyUserIfRequired($message, $user);
            }
        }

        //Client users
        foreach ($this->userRepository->getClientUsers($project->client_id) as $user) {
            if ($user->id != $request->requesterUserID) {
                $this->notifyUserIfRequired($message, $user);
            }
        }
    }

    private function dispatchEvent(Message $message)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::CREATE_MESSAGE,
            new CreateMessageEvent($message)
        );
    }

    private function getUserInfo($userID)
    {
        if ($user = $this->userRepository->getUser($userID)) {
            $data = new \StdClass();
            $data->lastName = $user->lastName;
            $data->firstName = $user->firstName;

            return $data;
        }

        return false;
    }

    private function getMessageCount($conversationID)
    {
        return count($this->repository->getMessagesByConversation($conversationID));
    }

    private function notifyUserIfRequired(Message $message, $user)
    {
        (new CreateNotificationInteractor($this->notificationRepository))->execute(new CreateNotificationRequest([
            'userID' => $user->id,
            'entityID' => $message->id,
            'type' => 'MESSAGE_CREATED',
        ]));
    }
}
