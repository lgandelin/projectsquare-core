<?php

namespace Webaccess\ProjectSquare\Interactors\Notifications;

use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Notifications\ReadNotificationRequest;
use Webaccess\ProjectSquare\Responses\Notifications\ReadNotificationResponse;

class ReadNotificationInteractor
{
    protected $repository;

    public function __construct(NotificationRepository $repository, UserRepository $userRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    public function execute(ReadNotificationRequest $request)
    {
        $this->validateRequest($request);
        $notification = $this->repository->getNotification($request->notificationID);
        $notification->read = true;
        $this->repository->persistNotification($notification);

        return new ReadNotificationResponse([
            'notification' => $notification,
        ]);
    }

    private function validateRequest(ReadNotificationRequest $request)
    {
        $this->validateNotification($request);
        $this->validateUser($request);
    }

    private function validateNotification(ReadNotificationRequest $request)
    {
        if (!$notification = $this->repository->getNotification($request->notificationID)) {
            throw new \Exception('Notification not found');
        }
    }

    private function validateUser(ReadNotificationRequest $request)
    {
        if (!$user = $this->userRepository->getUser($request->userID)) {
            throw new \Exception('User not found');
        }
    }
}
