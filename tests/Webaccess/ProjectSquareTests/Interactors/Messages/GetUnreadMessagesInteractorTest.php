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
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $conversation = $this->createSampleConversation($project->id);

        $this->createSampleMessage($conversation->id, $user->id);
        $this->createSampleMessage($conversation->id, $user->id);

        $response = $this->interactor->execute(new GetUnreadMessagesRequest([
            'userID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(GetUnreadMessagesResponse::class, $response);

        //Check unread messages count
        $this->assertEquals(2, count($response->messages));
    }

}