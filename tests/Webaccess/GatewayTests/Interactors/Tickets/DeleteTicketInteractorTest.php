<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Entities\Project;
use Webaccess\Gateway\Entities\Ticket;
use Webaccess\Gateway\Entities\TicketState;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Tickets\DeleteTicketEvent;
use Webaccess\Gateway\Interactors\Tickets\DeleteTicketInteractor;
use Webaccess\Gateway\Requests\Tickets\DeleteTicketRequest;
use Webaccess\Gateway\Responses\Tickets\DeleteTicketResponse;
use Webaccess\GatewayTests\BaseTestCase;
use Webaccess\GatewayTests\Repositories\InMemoryProjectRepository;
use Webaccess\GatewayTests\Repositories\InMemoryTicketRepository;

class DeleteTicketInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->repository = new InMemoryTicketRepository();
        $this->projectRepository = new InMemoryProjectRepository();
        $this->interactor = new DeleteTicketInteractor($this->repository);
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteNonExistingTicket()
    {
        $this->interactor->execute(new DeleteTicketRequest([
            'ticketID' => 1
        ]));
    }

    public function testDeleteTicket()
    {
        $projectID = $this->createSampleProject();
        $ticketID = $this->createSampleTicket('Sample ticket', $projectID, 'Lorem ipsum dolor sit amet');
        $response = $this->interactor->execute(new DeleteTicketRequest([
            'ticketID' => $ticketID
        ]));
        $this->assertInstanceOf(DeleteTicketResponse::class, $response);

        $this->assertCount(0, $this->repository->objects);

        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::DELETE_TICKET,
            Mockery::type(DeleteTicketEvent::class)
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

        return $this->projectRepository->persistProject($project);
    }
}