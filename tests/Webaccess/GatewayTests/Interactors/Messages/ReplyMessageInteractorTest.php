<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Messages\ReplyMessageEvent;
use Webaccess\Gateway\Interactors\Messages\ReplyMessageInteractor;
use Webaccess\Gateway\Requests\Messages\ReplyMessageRequest;
use Webaccess\Gateway\Responses\Messages\ReplyMessageResponse;
use Webaccess\GatewayTests\BaseTestCase;

class ReplyMessageInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new ReplyMessageInteractor($this->messageRepository, $this->conversationRepository, $this->projectRepository, $this->userRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testReplyMessageWithNonExistingConversation()
    {
        $user = $this->createSampleUser();

        $this->interactor->execute(new ReplyMessageRequest([
            'content' => 'Sample message',
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testReplyMessageWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $conversation = $this->createSampleConversation($project->id);

        $this->interactor->execute(new ReplyMessageRequest([
            'content' => 'Sample message',
            'conversationID' => $conversation->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testReplyMessage()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $conversation = $this->createSampleConversation($project->id);
        $this->projectRepository->addUserToProject($project, $user, null);

        $response = $this->interactor->execute(new ReplyMessageRequest([
            'content' => 'Sample message',
            'conversationID' => $conversation->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(ReplyMessageResponse::class, $response);
        $this->assertEquals(new \DateTime(), $response->createdAt);
        $this->assertEquals('John Doe', $response->user->firstName . ' ' . $response->user->lastName);
        $this->assertEquals(1, $response->count);

        //Check insertion
        $this->assertCount(1, $this->messageRepository->objects);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::REPLY_MESSAGE,
            Mockery::type(ReplyMessageEvent::class)
        );
    }
}