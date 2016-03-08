<?php

use Webaccess\ProjectSquare\Interactors\Messages\CreateMessageInteractor;
use Webaccess\ProjectSquare\Interactors\Messages\ReadMessageInteractor;
use Webaccess\ProjectSquare\Requests\Messages\CreateMessageRequest;
use Webaccess\ProjectSquare\Requests\Messages\ReadMessageRequest;
use Webaccess\ProjectSquare\Responses\Messages\ReadMessageResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class ReadMessageInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new ReadMessageInteractor($this->messageRepository, $this->conversationRepository, $this->userRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testReadMessageWithNonExistingMessage()
    {
        $this->interactor->execute(new ReadMessageRequest([]));
    }

    /**
     * @expectedException Exception
     */
    public function testReadMessageWithNonExistingUser()
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
    public function testReadMessageWithoutPermission()
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

    public function testReadMessage()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $conversation = $this->createSampleConversation($project->id);
        $message = $this->createSampleMessage($conversation->id, $user->id);

        //Check unread messages
        $this->assertEquals(1, count($this->userRepository->getUnreadMessages($user->id)));

        $response = $this->interactor->execute(new ReadMessageRequest([
            'messageID' => $message->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(ReadMessageResponse::class, $response);

        //Check unread messages
        $this->assertEquals(0, count($this->userRepository->getUnreadMessages($user->id)));
    }

    public function testCreateMessageAndCheckReadFlag()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $conversation = $this->createSampleConversation($project->id);
        $message = $this->createSampleMessage($conversation->id, $user->id);

        $this->assertEquals(1, count($this->userRepository->getUnreadMessages($user->id)));
    }
}