<?php

use Webaccess\ProjectSquare\Interactors\Messages\ReadMessageInteractor;
use Webaccess\ProjectSquare\Requests\Messages\ReadMessageRequest;
use Webaccess\ProjectSquare\Responses\Messages\ReadMessageResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class ReadMessageInteractorTe extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new ReadMessageInteractor($this->messageRepository, $this->conversationRepository, $this->userRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function readMessageWithNonExistingMessage()
    {
        $this->interactor->execute(new ReadMessageRequest([]));
    }

    /**
     * @expectedException Exception
     */
    public function readMessageWithNonExistingUser()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $conversation = $this->createSampleConversation($project->id);
        $this->projectRepository->addUserToProject($project, $user, null);
        $message = $this->createSampleMessage($conversation->id, $user->id);
        $this->interactor->execute(new ReadMessageRequest([
            'messageID' => $message->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function readMessageWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $conversation = $this->createSampleConversation($project->id);
        $message = $this->createSampleMessage($conversation->id, $user->id);
        $this->interactor->execute(new ReadMessageRequest([
            'messageID' => $message->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function readMessage()
    {
        $project = $this->createSampleProject();
        $user1 = $this->createSampleUser();
        $user2 = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user1, null);
        $this->projectRepository->addUserToProject($project, $user2, null);
        $conversation = $this->createSampleConversation($project->id);
        $message = $this->createSampleMessage($conversation->id, $user1->id);

        //Check unread messages
        $this->assertEquals(1, count($this->userRepository->getUnreadMessages($user2->id)));

        $response = $this->interactor->execute(new ReadMessageRequest([
            'messageID' => $message->id,
            'requesterUserID' => $user1->id
        ]));

        //Check response
        $this->assertInstanceOf(ReadMessageResponse::class, $response);

        //Check unread messages
        $this->assertEquals(0, count($this->userRepository->getUnreadMessages($user1->id)));
    }

    public function createMessageAndCheckReadFlag()
    {
        $project = $this->createSampleProject();
        $user1 = $this->createSampleUser();
        $user2 = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user1, null);
        $this->projectRepository->addUserToProject($project, $user2, null);
        $conversation = $this->createSampleConversation($project->id);
        $this->createSampleMessage($conversation->id, $user1->id);

        $this->assertEquals(1, count($this->userRepository->getUnreadMessages($user2->id)));
    }
}