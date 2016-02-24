<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Interactors\Tickets\CreateTicketInteractor;
use Webaccess\Gateway\Requests\CreateTicketRequest;
use Webaccess\Gateway\Responses\CreateTicketResponse;
use Webaccess\GatewayLaravel\Events\TicketCreatedEvent;
use Webaccess\GatewayTests\Repositories\InMemoryTicketRepository;

class CreateTicket extends FeatureContext
{
    public function __construct()
    {
        parent::__construct();

        $this->repository = new InMemoryTicketRepository();
    }

    /**
     * @When I create a new ticket
     */
    public function iCreateANewTicket()
    {
        $this->response = (new CreateTicketInteractor($this->repository))->execute(new CreateTicketRequest([
            'title' => 'New ticket',
        ]));
    }

    /**
     * @Then A state is created for this ticket
     */
    public function aStateIsCreatedForThisTicket()
    {
        $ticket = $this->repository->getTicketWithStates(1);
        $this->assertCount(1, $ticket->states);
    }

    /**
     * @Then I get notified of the ticket creation
     */
    public function iGetNotifiedOfTheTicketCreation()
    {
        $this->assertInstanceOf(TicketCreatedEvent::class, Context::get('event_manager')->getFiredEvents()[0]);
    }

    /**
     * @Then I get the ticket back
     */
    public function iGetTheTicketBack()
    {
        $ticket = $this->response->ticket;
        $this->assertInstanceOf(CreateTicketResponse::class, $this->response);
        $this->assertEquals('New ticket', $ticket->title);
    }
}