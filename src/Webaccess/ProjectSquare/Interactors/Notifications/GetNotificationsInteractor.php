<?php

namespace Webaccess\ProjectSquare\Interactors\Notifications;

use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Responses\Notifications\GetUnreadNotificationsResponse;
use Webaccess\ProjectSquare\Requests\Notifications\GetUnreadNotificationsRequest;

class GetNotificationsInteractor
{
    protected $repository;

    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getUnreadNotifications(GetUnreadNotificationsRequest $request)
    {
        $userID = $request->userID;

        return new GetUnreadNotificationsResponse([
            'notifications' => $this->repository->getUnreadNotifications($userID),
        ]);
    }
}
