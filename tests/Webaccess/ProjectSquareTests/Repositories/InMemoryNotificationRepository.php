<?php

namespace Webaccess\ProjectSquareTests\Repositories;

use Webaccess\ProjectSquare\Entities\Notification;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;

class InMemoryNotificationRepository implements NotificationRepository
{
    public $objects;

    public function __construct()
    {
        $this->objects = [];
    }

    public function getNextID()
    {
        return count($this->objects) + 1;
    }

    public function getNotification($notificationID)
    {
        if (isset($this->objects[$notificationID])) {
            return $this->objects[$notificationID];
        }

        return false;
    }

    public function getNotifications($userID, $projectID = null)
    {
        $result = [];
        foreach ($this->objects as $notification) {
            if ($notification->userID == $userID) {
                $result[]= $notification;
            }
        }

        return $result;
    }

    public function persistNotification(Notification $notification)
    {
        if (!isset($notification->id)) {
            $notification->id = self::getNextID();
        }
        $this->objects[$notification->id]= $notification;

        return $notification;
    }

    public function removeNotification($notificationID)
    {
        if (isset($this->objects[$notificationID])) {
            unset($this->objects[$notificationID]);
        }
    }
}