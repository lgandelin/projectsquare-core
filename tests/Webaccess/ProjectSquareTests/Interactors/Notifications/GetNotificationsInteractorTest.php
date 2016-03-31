<?php

use Webaccess\ProjectSquare\Interactors\Notifications\GetNotificationsInteractor;
use Webaccess\ProjectSquare\Requests\Notifications\GetUnreadNotificationsRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetNotificationsInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetNotificationsInteractor($this->notificationRepository);
    }

    public function testGetUnreadNotifications()
    {
        $user = $this->createSampleUser();
        $this->createSampleEvent($user->id);

        $response = $this->interactor->getUnreadNotifications(new GetUnreadNotificationsRequest([
            'userID' => $user->id
        ]));
        $this->assertEquals(0, count($response->notifications));
    }

    public function testGetUnreadNotifications2()
    {
        $user1 = $this->createSampleUser();
        $user2 = $this->createSampleUser();
        $this->createSampleEvent($user1->id, $user2->id);

        $response = $this->interactor->getUnreadNotifications(new GetUnreadNotificationsRequest([
            'userID' => $user1->id
        ]));
        $this->assertEquals(1, count($response->notifications));
    }
}