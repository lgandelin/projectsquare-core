<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Messages\CreateMessageEvent;
use Webaccess\ProjectSquare\Interactors\Messages\CreateMessageInteractor;
use Webaccess\ProjectSquare\Requests\Messages\CreateMessageRequest;
use Webaccess\ProjectSquare\Responses\Messages\CreateMessageResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class CreateMessageInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateMessageInteractor(
            $this->messageRepository,
            $this->conversationRepository,
            $this->userRepository,
            $this->projectRepository,
            $this->notificationRepository
        );
    }

    /**
     * @expectedException Exception
     */
    public function testCreateMessageWithNonExistingConversation()
    {
        $user = $this->createSampleUser();

        $this->interactor->execute(new CreateMessageRequest([
            'content' => 'Sample message',
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Webaccess\ProjectSquare\Exceptions\Messages\MessageReplyNotAuthorizedException
     */
    public function testCreateMessageWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $conversation = $this->createSampleConversation($project->id);

        $this->interactor->execute(new CreateMessageRequest([
            'content' => 'Sample message',
            'conversationID' => $conversation->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testCreateMessage()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $conversation = $this->createSampleConversation($project->id);
        $this->projectRepository->addUserToProject($project, $user, null);

        $response = $this->interactor->execute(new CreateMessageRequest([
            'content' => 'Sample message',
            'conversationID' => $conversation->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(CreateMessageResponse::class, $response);
        $this->assertEquals(new \DateTime(), $response->createdAt);
        $this->assertEquals('John Doe', $response->user->firstName . ' ' . $response->user->lastName);
        $this->assertEquals(1, $response->count);

        //Check insertion
        $this->assertCount(1, $this->messageRepository->objects);

        //Check notification
        $this->assertCount(1, $this->notificationRepository->objects);
        $notification = $this->notificationRepository->objects[1];
        $this->assertEquals('MESSAGE_CREATED', $notification->type);
        $this->assertEquals($response->message->id, $notification->entityID);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_MESSAGE,
            Mockery::type(CreateMessageEvent::class)
        );
    }
}