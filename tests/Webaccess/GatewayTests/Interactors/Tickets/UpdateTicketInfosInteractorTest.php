<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Entities\Project;
use Webaccess\Gateway\Entities\Ticket;
use Webaccess\Gateway\Entities\TicketState;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Tickets\UpdateTicketInfosEvent;
use Webaccess\Gateway\Interactors\Tickets\UpdateTicketInfosInteractor;
use Webaccess\Gateway\Requests\Tickets\UpdateTicketInfosRequest;
use Webaccess\Gateway\Responses\Tickets\UpdateTicketInfosResponse;
use Webaccess\GatewayTests\Dummies\DummyTranslator;
use Webaccess\GatewayTests\Repositories\InMemoryProjectRepository;
use Webaccess\GatewayTests\Repositories\InMemoryTicketRepository;

class UpdateTicketInfosInteractorTest extends PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        $this->repository = new InMemoryTicketRepository();
        $this->interactor = (new UpdateTicketInfosInteractor($this->repository));
        Context::set('translator', new DummyTranslator());
        Context::set('event_dispatcher', Mockery::spy("EventDispatcherInterface"));
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
