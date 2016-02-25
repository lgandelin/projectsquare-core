<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Entities\Ticket;
use Webaccess\Gateway\Entities\TicketState;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Tickets\UpdateTicketInfosEvent;
use Webaccess\Gateway\Interactors\Tickets\UpdateTicketInfosInteractor;
use Webaccess\Gateway\Requests\Tickets\UpdateTicketInfosRequest;
use Webaccess\GatewayTests\Dummies\DummyTranslator;
use Webaccess\GatewayTests\Repositories\InMemoryTicketRepository;

class UpdateTicketInteractorTest extends PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        $this->repository = new InMemoryTicketRepository();
        Context::set('translator', new DummyTranslator());
        Context::set('event_dispatcher', Mockery::spy("EventDispatcherInterface"));
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateNonExistingTicket()
    {
        $this->response = (new UpdateTicketInfosInteractor($this->repository))->execute(new UpdateTicketInfosRequest([
            'ticketID' => 1,
            'title' => 'New title'
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateTicketWithNonExistingProject()
    {
        $ticketID = $this->createSampleTicket('Sample ticket', null, 'Lorem ipsum dolor sit amet');
        $this->response = (new UpdateTicketInfosInteractor($this->repository))->execute(new UpdateTicketInfosRequest([
            'ticketID' => $ticketID,
            'projectID' => 1
        ]));
    }

    public function testUpdateTicket()
    {
        $ticketID = $this->createSampleTicket('Sample ticket', null, 'Lorem ipsum dolor sit amet');
        $this->response = (new UpdateTicketInfosInteractor($this->repository))->execute(new UpdateTicketInfosRequest([
            'ticketID' => $ticketID,
            'title' => 'New title'
        ]));

        $ticket = $this->repository->getTicket($ticketID);
        $this->assertEquals('New title', $ticket->title);

        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_TICKET_INFOS,
            Mockery::type(UpdateTicketInfosEvent::class)
        );
    }

    private function createSampleTicket($title, $projectID, $description)
    {
        $ticket = new Ticket();
        $ticket->title = $title;
        $ticket->projectID = $projectID;
        $ticket->description = $description;
        $ticketID = $this->repository->persistTicket($ticket);

        $ticketState = new TicketState();
        $ticketState->ticketID = $ticketID;
        $this->repository->persistTicketState($ticketState);

        return $ticketID;
    }
}
