<?php

namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Notification;

interface NotificationRepository
{
    public function getNotification($notificationID);

    public function getNotifications($userID);

    public function getUnreadNotifications($userID);

    public function persistNotification(Notification $notification);

    public function removeNotification($notificationID);

    public function removeNotificationsByTypeAndEntityID($type, $entityID);
}
