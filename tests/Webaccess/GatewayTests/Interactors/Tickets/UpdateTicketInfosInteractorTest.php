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
        $ticketID = $this->createSampleTicket('Sample ticket', 1, 'Lorem ipsum dolor sit amet');
        $this->interactor->execute(new UpdateTicketInfosRequest([
            'ticketID' => $ticketID,
            'projectID' => 1
        ]));
    }

    public function testUpdateTicket()
    {
        $projectID = $this->createSampleProject();
        $ticketID = $this->createSampleTicket('Sample ticket', $projectID, 'Lorem ipsum dolor sit amet');
        $response = $this->interactor->execute(new UpdateTicketInfosRequest([
            'ticketID' => $ticketID,
            'title' => 'New title'
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
