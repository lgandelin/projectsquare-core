<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tickets\UpdateTicketEvent;
use Webaccess\ProjectSquare\Interactors\Tickets\UpdateTicketInteractor;
use Webaccess\ProjectSquare\Requests\Tickets\UpdateTicketRequest;
use Webaccess\ProjectSquare\Responses\Tickets\UpdateTicketResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

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
        $this->interactor->execute(new UpdateTicketRequest([
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
        $this->projectRepository->addUserToProject($project->id, $user->id, null);
        $ticketID = $this->createSampleTicket('Sample ticket', $project->id, 'Lorem ipsum dolor sit amet');
        $this->interactor->execute(new UpdateTicketRequest([
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

    /**
     * @expectedException Exception
     */
    public function testUpdateTicketToUnauthorizedAllocatedUser()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project->id, $user->id, null);
        $allocatedUser = $this->createSampleUser();
        $ticketID = $this->createSampleTicket('Sample ticket', 1, 'Lorem ipsum dolor sit amet');
        $this->interactor->execute(new UpdateTicketRequest([
            'ticketID' => $ticketID,
            'allocatedUserID' => $allocatedUser->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testUpdateTicket()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project->id, $user->id, null);
        $ticketID = $this->createSampleTicket('Sample ticket', $project->id, 'Lorem ipsum dolor sit amet');
        $response = $this->interactor->execute(new UpdateTicketRequest([
            'ticketID' => $ticketID,
            'statusID' => 2,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(UpdateTicketResponse::class, $response);
        $this->assertEquals($ticketID, $response->ticket->id);
        $this->assertEquals(2, $response->ticketState->statusID);

        //Check update
        $ticket = $this->ticketRepository->getTicketWithStates($ticketID);
        $this->assertEquals(2, $ticket->states[1]->statusID);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_TICKET,
            Mockery::type(UpdateTicketEvent::class)
        );
    }
}