<?php

use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Interactors\Tickets\CreateTicketInteractor;
use Webaccess\Gateway\Requests\CreateTicketRequest;
use Webaccess\Gateway\Responses\CreateTicketResponse;
use Webaccess\GatewayTests\Repositories\InMemoryTicketRepository;

class CreateTicketAcceptanceTest extends FeatureContext
{
    public function __construct()
    {
        parent::__construct();

        $this->eventDispatcher = Mockery::spy("EventDispatcherInterface");
        $this->repository = new InMemoryTicketRepository();
        $this->interactor = (new CreateTicketInteractor($this->repository, $this->eventDispatcher));
    }

    /**
     * @When I create a new ticket
     */
    public function iCreateANewTicket()
    {
        $this->response = $this->interactor->execute(new CreateTicketRequest([
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
        $this->eventDispatcher->shouldHaveReceived("dispatch")->with(
            Events::CREATE_TICKET,
            Mockery::type(Webaccess\Gateway\Events\CreateTicketEvent::class)
        );
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