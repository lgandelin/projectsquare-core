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
        $conversation = $this->createSampleConversation($project->id);
        $this->projectRepository->addUserToProject($project, $user, null);
        $message = $this->createSampleMessage($conversation->id, $user->id);
        $response = $this->interactor->execute(new ReadMessageRequest([
            'messageID' => $message->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(ReadMessageResponse::class, $response);
        //$this->assertEquals(new \DateTime(), $response->createdAt);
        //$this->assertEquals('John Doe', $response->user->firstName . ' ' . $response->user->lastName);
        //$this->assertEquals(1, $response->count);

        //Check message read
        //$this->assertCount(1, $this->userRepository->getReadMessages($user->id));
    }

    public function testCreateMessageAndCheckReadFlag()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $conversation = $this->createSampleConversation($project->id);
        $this->projectRepository->addUserToProject($project, $user, null);

        (new CreateMessageInteractor(
            $this->messageRepository,
            $this->conversationRepository,
            $this->userRepository,
            $this->projectRepository
        ))->execute(new CreateMessageRequest([
            'content' => 'Sample message',
            'conversationID' => $conversation->id,
            'requesterUserID' => $user->id
        ]));

        $this->assertEquals(1, count($this->userRepository->getUnreadMessages($user->id)));
    }
}