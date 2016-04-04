<?php

namespace Webaccess\ProjectSquare\Interactors\Messages;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Message;
use Webaccess\ProjectSquare\Entities\Notification;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Messages\CreateMessageEvent;
use Webaccess\ProjectSquare\Exceptions\Messages\MessageReplyNotAuthorizedException;
use Webaccess\ProjectSquare\Repositories\ConversationRepository;
use Webaccess\ProjectSquare\Repositories\MessageRepository;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Messages\CreateMessageRequest;
use Webaccess\ProjectSquare\Responses\Messages\CreateMessageResponse;

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
    )
    {
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
        $this->createNotifications($message);
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

    private function createNotifications(Message $message)
    {
        $conversation = $this->conversationRepository->getConversation($message->conversationID);
        foreach ($this->userRepository->getUsersByProject($conversation->projectID) as $user) {
            $notification = new Notification();
            $notification->userID = $user->id;
            $notification->read = false;
            $notification->entityID = $message->id;
            $notification->type = 'MESSAGE_CREATED';
            $this->notificationRepository->persistNotification($notification);
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
}
