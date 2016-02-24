<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Entities\Ticket;
use Webaccess\Gateway\Entities\TicketState;
use Webaccess\Gateway\Interactors\TicketInteractor;
use Webaccess\Gateway\Interactors\Tickets\UpdateTicketInteractor;
use Webaccess\Gateway\Requests\UpdateTicketRequest;
use Webaccess\GatewayLaravel\Events\TicketUpdatedEvent;
use Webaccess\GatewayTests\Repositories\InMemoryTicketRepository;

class UpdateTicketAcceptanceTest extends FeatureContext
{
    public function __construct()
    {
        parent::__construct();

        $this->repository = new InMemoryTicketRepository();
        $this->ticketInteractor = new TicketInteractor($this->repository);
    }

    /**
     * @Given A ticket
     */
    public function aTicket()
    {
        $ticket = new Ticket();
        $ticket->title = 'Sample ticket';
        $ticket->projectID = 1;
        $ticket->description = 'Lorem ipsum dolor sit amet';
        $this->repository->persistTicket($ticket);

        $ticketState = new TicketState();
        $ticketState->ticketID = 1;
        $ticketState->statusID = 5;
        $this->repository->persistTicketState($ticketState);
    }

    /**
     * @When I update the status of this ticket
     */
    public function iUpdateTheStatusOfThisTicket()
    {
        $this->response = (new UpdateTicketInteractor($this->repository))->execute(new UpdateTicketRequest([
            'ticketID' => 1,
            'statusID' => 2,
        ]));
    }

    /**
     * @Then A new state is created for this ticket
     */
    public function aNewStateIsCreatedForThisTicket()
    {
        $ticket = $this->ticketInteractor->getTicketWithStates(1);
        $this->assertCount(2, $ticket->states);
    }

    /**
     * @Then I get notified of the ticket update
     */
    public function iGetNotifiedOfTheTicketUpdate()
    {
        $this->assertInstanceOf(TicketUpdatedEvent::class, Context::get('event_manager')->getFiredEvents()[0]);
    }

    /**
     * @Given A ticket with the following title :title
     */
    public function aTicketWithTheFollowingTitle($title)
    {
        $ticket = new Ticket();
        $ticket->title = $title;
        $ticket->projectID = 1;
        $ticket->description = 'Lorem ipsum dolor sit amet';
        $this->repository->persistTicket($ticket);

        $ticketState = new TicketState();
        $ticketState->ticketID = 1;
        $ticketState->statusID = 5;
        $this->repository->persistTicketState($ticketState);
    }

    /**
     * @When I change the title to :title
     */
    public function iChangeTheTitleTo($title)
    {
        $this->ticketInteractor->updateInfos(1, $title, 1, 1, 5, 'Lorem ipsum dolor sit amet');
    }

    /**
     * @Then The ticket has the following title : :title
     */
    public function theTicketHasTheFollowingTitle($title)
    {
        $ticket = $this->repository->getTicket(1);

        $this->assertEquals($ticket->title, $title);
    }
}