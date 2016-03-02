<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Events\Conversations\CreateConversationEvent;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Interactors\Conversations\CreateConversationInteractor;
use Webaccess\Gateway\Requests\Conversations\CreateConversationRequest;
use Webaccess\Gateway\Responses\Conversations\CreateConversationResponse;
use Webaccess\GatewayTests\BaseTestCase;

class CreateConversationInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateConversationInteractor($this->conversationRepository, $this->messageRepository, $this->userRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testCreateConversationWithNonExistingProject()
    {
        $user = $this->createSampleUser();

        $this->interactor->execute(new CreateConversationRequest([
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTicketWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();

        $this->interactor->execute(new CreateConversationRequest([
            'title' => 'Sample ticket',
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testCreateConversation()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);

        $response = $this->interactor->execute(new CreateConversationRequest([
            'title' => 'Sample conversation',
            'message' => 'Sample text',
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(CreateConversationResponse::class, $response);
        $this->assertEquals('Sample conversation', $response->conversation->title);
        $this->assertEquals($project->id, $response->conversation->projectID);
        $this->assertEquals('Sample text', $response->message->content);
        $this->assertEquals($response->conversation->id, $response->message->conversationID);

        $this->assertCount(1, $this->conversationRepository->objects);
        $this->assertCount(1, $this->messageRepository->objects);

        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_CONVERSATION,
            Mockery::type(CreateConversationEvent::class)
        );
    }
}