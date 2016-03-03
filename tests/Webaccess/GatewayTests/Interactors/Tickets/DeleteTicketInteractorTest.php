<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Entities\Ticket;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Tickets\DeleteTicketEvent;
use Webaccess\Gateway\Interactors\Tickets\DeleteTicketInteractor;
use Webaccess\Gateway\Requests\Tickets\DeleteTicketRequest;
use Webaccess\Gateway\Responses\Tickets\DeleteTicketResponse;
use Webaccess\GatewayTests\BaseTestCase;

class DeleteTicketInteractorTest extends BaseTestCase
{
    public $userRepository;

    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DeleteTicketInteractor($this->ticketRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteNonExistingTicket()
    {
        $this->interactor->execute(new DeleteTicketRequest([
            'ticketID' => 1,
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteTicketWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $ticketID = $this->createSampleTicket('Sample ticket', $project->id, 'Lorem ipsum dolor sit amet');
        $this->interactor->execute(new DeleteTicketRequest([
            'ticketID' => $ticketID,
            'requesterUserID' => $user->id
        ]));
    }

    public function testDeleteTicket()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $ticketID = $this->createSampleTicket('Sample ticket', $project->id, 'Lorem ipsum dolor sit amet');
        $response = $this->interactor->execute(new DeleteTicketRequest([
            'ticketID' => $ticketID,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(DeleteTicketResponse::class, $response);
        $this->assertInstanceOf(Ticket::class, $response->ticket);
        $this->assertEquals($ticketID, $response->ticket->id);

        //Check deletion
        $this->assertCount(0, $this->ticketRepository->objects);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::DELETE_TICKET,
            Mockery::type(DeleteTicketEvent::class)
        );
    }
}