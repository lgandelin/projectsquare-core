<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Interactors\CreateTicketInteractor;
use Webaccess\Gateway\Requests\TicketCreateRequest;
use Webaccess\Gateway\Responses\TicketCreateResponse;
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
        $this->response = (new CreateTicketInteractor($this->repository))->execute(new TicketCreateRequest([
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
        $this->assertInstanceOf(TicketCreateResponse::class, $this->response);
        $this->assertEquals('New ticket', $ticket->title);
    }
}