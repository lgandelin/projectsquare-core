<?php

use Webaccess\ProjectSquare\Interactors\Messages\GetUnreadMessagesInteractor;
use Webaccess\ProjectSquare\Requests\Messages\GetUnreadMessagesRequest;
use Webaccess\ProjectSquare\Responses\Messages\GetUnreadMessagesResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetUnreadMessagesInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetUnreadMessagesInteractor($this->userRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testGetUnreadMessagesWithNonExistingUser()
    {
        $this->interactor->execute(new GetUnreadMessagesRequest([
            'userID' => 1
        ]));
    }

    public function testGetUnreadMessages0()
    {
        $user = $this->createSampleUser();
        $response = $this->interactor->execute(new GetUnreadMessagesRequest([
            'userID' => $user->id
        ]));

        $this->assertEquals(0, count($response->messages));
    }

    public function testGetUnreadMessages1()
    {
        $project = $this->createSampleProject();
        $user1 = $this->createSampleUser();
        $user2 = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user1, null);
        $this->projectRepository->addUserToProject($project, $user2, null);
        $conversation = $this->createSampleConversation($project->id);

        $this->createSampleMessage($conversation->id, $user1->id);
        $this->createSampleMessage($conversation->id, $user1->id);

        $response = $this->interactor->execute(new GetUnreadMessagesRequest([
            'userID' => $user2->id
        ]));

        //Check response
        $this->assertInstanceOf(GetUnreadMessagesResponse::class, $response);

        //Check unread messages count
        $this->assertEquals(2, count($response->messages));
    }

}