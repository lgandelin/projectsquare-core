<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Tickets\UpdateTicketInfosEvent;
use Webaccess\Gateway\Interactors\Tickets\UpdateTicketInfosInteractor;
use Webaccess\Gateway\Requests\Tickets\UpdateTicketInfosRequest;
use Webaccess\Gateway\Responses\Tickets\UpdateTicketInfosResponse;
use Webaccess\GatewayTests\BaseTestCase;

class UpdateTicketInfosInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdateTicketInfosInteractor($this->ticketRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateNonExistingTicket()
    {
        $this->interactor->execute(new UpdateTicketInfosRequest([
            'ticketID' => 1,
            'title' => 'New title'
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateTicketWithNonExistingProject()
    {
        $user = $this->createSampleUser();
        $ticketID = $this->createSampleTicket('Sample ticket', 1, 'Lorem ipsum dolor sit amet');
        $this->interactor->execute(new UpdateTicketInfosRequest([
            'ticketID' => $ticketID,
            'projectID' => 1,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateTicketWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $ticketID = $this->createSampleTicket('Sample ticket', 1, 'Lorem ipsum dolor sit amet');
        $this->interactor->execute(new UpdateTicketInfosRequest([
            'ticketID' => $ticketID,
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testUpdateTicket()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $ticketID = $this->createSampleTicket('Sample ticket', $project->id, 'Lorem ipsum dolor sit amet');
        $response = $this->interactor->execute(new UpdateTicketInfosRequest([
            'ticketID' => $ticketID,
            'title' => 'New title',
            'requesterUserID' => $user->id
        ]));
        $this->assertInstanceOf(UpdateTicketInfosResponse::class, $response);

        $ticket = $this->ticketRepository->getTicket($ticketID);
        $this->assertEquals('New title', $ticket->title);

        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_TICKET_INFOS,
            Mockery::type(UpdateTicketInfosEvent::class)
        );
    }
}
