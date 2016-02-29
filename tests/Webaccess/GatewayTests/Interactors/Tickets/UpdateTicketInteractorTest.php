<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Tickets\UpdateTicketEvent;
use Webaccess\Gateway\Interactors\Tickets\UpdateTicketInteractor;
use Webaccess\Gateway\Requests\Tickets\UpdateTicketRequest;
use Webaccess\Gateway\Responses\Tickets\UpdateTicketResponse;
use Webaccess\GatewayTests\BaseTestCase;

class UpdateTicketInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdateTicketInteractor($this->ticketRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateNonExistingTicket()
    {
        $this->response = $this->interactor->execute(new UpdateTicketRequest([
            'ticketID' => 1,
            'title' => 'New title'
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateTicketWithPassedDueDate()
    {
        $projectID = $this->createSampleProject();
        $ticketID = $this->createSampleTicket('Sample ticket', $projectID, 'Lorem ipsum dolor sit amet');
        $this->response = $this->interactor->execute(new UpdateTicketRequest([
            'ticketID' => $ticketID,
            'dueDate' => new DateTime('2010-01-01')
        ]));
    }

    public function testUpdateTicket()
    {
        $projectID = $this->createSampleProject();
        $ticketID = $this->createSampleTicket('Sample ticket', $projectID, 'Lorem ipsum dolor sit amet');
        $this->response = $this->interactor->execute(new UpdateTicketRequest([
            'ticketID' => $ticketID,
            'statusID' => 2,
        ]));
        $this->assertInstanceOf(UpdateTicketResponse::class, $this->response);

        $ticket = $this->ticketRepository->getTicketWithStates($ticketID);
        $this->assertEquals(2, $ticket->states[1]->statusID);

        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_TICKET,
            Mockery::type(UpdateTicketEvent::class)
        );
    }
}