<?php

use Webaccess\ProjectSquare\Interactors\Notifications\GetNotificationsInteractor;
use Webaccess\ProjectSquare\Interactors\Notifications\ReadNotificationInteractor;
use Webaccess\ProjectSquare\Requests\Notifications\GetUnreadNotificationsRequest;
use Webaccess\ProjectSquare\Requests\Notifications\ReadNotificationRequest;
use Webaccess\ProjectSquare\Responses\Notifications\ReadNotificationResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class ReadNotificationInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new ReadNotificationInteractor($this->notificationRepository, $this->userRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testReadNotificationWithNonExistingMessage()
    {
        $this->interactor->execute(new ReadNotificationRequest([]));
    }

    public function testReadNotification()
    {
        $project = $this->createSampleProject();
        $user1 = $this->createSampleUser();
        $user2 = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user1, null);
        $this->projectRepository->addUserToProject($project, $user2, null);
        $conversation = $this->createSampleConversation($project->id);
        $this->createSampleMessage($conversation->id, $user1->id);

        //Check unread notifications
        $response = (new GetNotificationsInteractor($this->notificationRepository))->getUnreadNotifications(new GetUnreadNotificationsRequest([
            'userID' => $user2->id,
        ]));
        $this->assertEquals(1, sizeof($response->notifications));
        $notification = array_shift($response->notifications);

        $response = $this->interactor->execute(new ReadNotificationRequest([
            'userID' => $user2->id,
            'notificationID' => $notification->id
        ]));

        //Check response
        $this->assertInstanceOf(ReadNotificationResponse::class, $response);

        //Check unread notifications
        $response = (new GetNotificationsInteractor($this->notificationRepository))->getUnreadNotifications(new GetUnreadNotificationsRequest([
            'userID' => $user2->id,
        ]));
        $this->assertEquals(0, sizeof($response->notifications));
    }
}