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
        $this->interactor = new CreateConversationInteractor($this->conversationRepository, $this->projectRepository);
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
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
        $this->assertInstanceOf(CreateConversationResponse::class, $response);

        $this->assertCount(1, $this->conversationRepository->objects);

        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_CONVERSATION,
            Mockery::type(CreateConversationEvent::class)
        );
    }
}