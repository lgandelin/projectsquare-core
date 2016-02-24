<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Entities\Ticket;
use Webaccess\Gateway\Entities\TicketState;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Tickets\UpdateTicketEvent;
use Webaccess\Gateway\Interactors\Tickets\GetTicketInteractor;
use Webaccess\Gateway\Interactors\Tickets\UpdateTicketInfosInteractor;
use Webaccess\Gateway\Interactors\Tickets\UpdateTicketInteractor;
use Webaccess\Gateway\Requests\Tickets\UpdateTicketInfosRequest;
use Webaccess\Gateway\Requests\Tickets\UpdateTicketRequest;
use Webaccess\Gateway\Responses\Tickets\UpdateTicketInfosResponse;
use Webaccess\GatewayTests\Repositories\InMemoryTicketRepository;

class UpdateTicketAcceptanceTest extends FeatureContext
{
    public function __construct()
    {
        parent::__construct();
        $this->repository = new InMemoryTicketRepository();
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
        $ticket = (new GetTicketInteractor($this->repository))->getTicketWithStates(1);
        $this->assertCount(2, $ticket->states);
    }

    /**
     * @Then I get notified of the ticket update
     */
    public function iGetNotifiedOfTheTicketUpdate()
    {
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_TICKET,
            Mockery::type(UpdateTicketEvent::class)
        );
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
        $this->response = (new UpdateTicketInfosInteractor($this->repository))->execute(new UpdateTicketInfosRequest([
            'ticketID' => 1,
            'title' => $title
        ]));
    }

    /**
     * @Then I get the ticket back after the update
     */
    public function iGetTheTicketBackAfterTheUpdate()
    {
        $this->assertInstanceOf(UpdateTicketInfosResponse::class, $this->response);
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