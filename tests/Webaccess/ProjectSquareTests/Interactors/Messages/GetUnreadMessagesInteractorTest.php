<?php

use Webaccess\ProjectSquare\Interactors\Messages\GetUnreadMessagesCountInteractor;
use Webaccess\ProjectSquare\Requests\Messages\GetUnreadMessagesCountRequest;
use Webaccess\ProjectSquare\Responses\Messages\GetUnreadMessagesCountResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetUnreadMessagesInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetUnreadMessagesCountInteractor($this->messageRepository, $this->conversationRepository, $this->userRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testGetUnreadMessagesWithNonExistingUser()
    {
        $this->interactor->execute(new GetUnreadMessagesCountRequest([
            'userID' => 1
        ]));
    }

    public function testGetUnreadMessages0()
    {
        $user = $this->createSampleUser();
        $response = $this->interactor->execute(new GetUnreadMessagesCountRequest([
            'userID' => $user->id
        ]));

        $this->assertEquals(0, $response->count);
    }

    public function testGetUnreadMessages1()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $conversation = $this->createSampleConversation($project->id);

        $this->createSampleMessage($conversation->id, $user->id);
        $this->createSampleMessage($conversation->id, $user->id);

        $response = $this->interactor->execute(new GetUnreadMessagesCountRequest([
            'userID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(GetUnreadMessagesCountResponse::class, $response);

        //Check unread messages count
        $this->assertEquals(2, $response->count);
    }

}