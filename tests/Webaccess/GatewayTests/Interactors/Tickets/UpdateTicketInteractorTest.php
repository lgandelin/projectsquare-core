<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Tickets\UpdateTicketEvent;
use Webaccess\Gateway\Interactors\Tickets\UpdateTicketInteractor;
use Webaccess\Gateway\Requests\Tickets\UpdateTicketRequest;
use Webaccess\Gateway\Responses\Tickets\UpdateTicketResponse;
use Webaccess\GatewayTests\BaseTestCase;

class UpdateTicketInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdateTicketInteractor($this->ticketRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateNonExistingTicket()
    {
        $user = $this->createSampleUser();
        $response = $this->interactor->execute(new UpdateTicketRequest([
            'ticketID' => 1,
            'title' => 'New title',
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateTicketWithPassedDueDate()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $ticketID = $this->createSampleTicket('Sample ticket', $project->id, 'Lorem ipsum dolor sit amet');
        $response = $this->interactor->execute(new UpdateTicketRequest([
            'ticketID' => $ticketID,
            'dueDate' => new DateTime('2010-01-01'),
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateTicketWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $ticketID = $this->createSampleTicket('Sample ticket', 1, 'Lorem ipsum dolor sit amet');
        $this->interactor->execute(new UpdateTicketRequest([
            'ticketID' => $ticketID,
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testUpdateTicket()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $ticketID = $this->createSampleTicket('Sample ticket', $project->id, 'Lorem ipsum dolor sit amet');
        $response = $this->interactor->execute(new UpdateTicketRequest([
            'ticketID' => $ticketID,
            'statusID' => 2,
            'requesterUserID' => $user->id
        ]));
        $this->assertInstanceOf(UpdateTicketResponse::class, $response);

        $ticket = $this->ticketRepository->getTicketWithStates($ticketID);
        $this->assertEquals(2, $ticket->states[1]->statusID);

        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_TICKET,
            Mockery::type(UpdateTicketEvent::class)
        );
    }
}