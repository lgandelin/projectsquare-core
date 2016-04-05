<?php

namespace Webaccess\ProjectSquare\Responses\Notifications;

use Webaccess\ProjectSquare\Entities\Notification;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Requests\Notifications\CreateNotificationRequest;

class CreateNotificationInteractor
{
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function execute(CreateNotificationRequest $request)
    {
        $notification = new Notification();
        $notification->userID = $request->userID;
        $notification->read = false;
        $notification->entityID = $request->entityID;
        $notification->type = $request->type;

        $this->notificationRepository->persistNotification($notification);
    }
}
