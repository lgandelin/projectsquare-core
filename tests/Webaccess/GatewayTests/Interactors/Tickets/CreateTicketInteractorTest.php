<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Tickets\CreateTicketEvent;
use Webaccess\Gateway\Interactors\Tickets\CreateTicketInteractor;
use Webaccess\Gateway\Requests\Tickets\CreateTicketRequest;
use Webaccess\Gateway\Responses\Tickets\CreateTicketResponse;
use Webaccess\GatewayTests\BaseTestCase;

class CreateTicketInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateTicketInteractor($this->ticketRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTicketWithoutTitle()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $this->interactor->execute(new CreateTicketRequest([
            'title' => '',
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTicketWithNonExistingProject()
    {
        $user = $this->createSampleUser();
        $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => 1,
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
        $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testCreateTicket()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $response = $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $project->id,
            'statusID' => 2,
            'dueDate' => new \DateTime('2016-02-30'),
            'requesterUserID' => $user->id
        ]));
        $this->assertInstanceOf(CreateTicketResponse::class, $response);

        $this->assertCount(1, $this->ticketRepository->objects);

        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_TICKET,
            Mockery::type(CreateTicketEvent::class)
        );
    }
}