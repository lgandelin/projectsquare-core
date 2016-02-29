<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Entities\Project;
use Webaccess\Gateway\Entities\Ticket;
use Webaccess\Gateway\Entities\TicketState;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Tickets\UpdateTicketEvent;
use Webaccess\Gateway\Interactors\Tickets\UpdateTicketInteractor;
use Webaccess\Gateway\Requests\Tickets\UpdateTicketRequest;
use Webaccess\Gateway\Responses\Tickets\UpdateTicketResponse;
use Webaccess\GatewayTests\BaseTestCase;
use Webaccess\GatewayTests\Repositories\InMemoryProjectRepository;
use Webaccess\GatewayTests\Repositories\InMemoryTicketRepository;

class UpdateTicketInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->repository = new InMemoryTicketRepository();
        $this->interactor = (new UpdateTicketInteractor($this->repository));
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

        $ticket = $this->repository->getTicketWithStates($ticketID);
        $this->assertEquals(2, $ticket->states[1]->statusID);

        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_TICKET,
            Mockery::type(UpdateTicketEvent::class)
        );
    }

    private function createSampleTicket($title, $projectID, $description)
    {
        $ticket = new Ticket();
        $ticket->title = $title;
        $ticket->projectID = $projectID;
        $ticket->description = $description;
        $ticket = $this->repository->persistTicket($ticket);

        $ticketState = new TicketState();
        $ticketState->ticketID = $ticket->id;
        $this->repository->persistTicketState($ticketState);

        return $ticket->id;
    }

    private function createSampleProject()
    {
        $project = new Project();
        $project->name = 'Sample Project';

        return (new InMemoryProjectRepository())->persistProject($project);
    }
}