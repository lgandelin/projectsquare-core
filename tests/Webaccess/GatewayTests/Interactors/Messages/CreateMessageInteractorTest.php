<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Messages\CreateMessageEvent;
use Webaccess\Gateway\Interactors\Messages\CreateMessageInteractor;
use Webaccess\Gateway\Requests\Messages\CreateMessageRequest;
use Webaccess\Gateway\Responses\Messages\CreateMessageResponse;
use Webaccess\GatewayTests\BaseTestCase;

class CreateMessageInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateMessageInteractor($this->messageRepository, $this->conversationRepository, $this->userRepository, $this->projectRepository);
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
     * @expectedException Exception
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

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_MESSAGE,
            Mockery::type(CreateMessageEvent::class)
        );
    }
}