<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Ticket;
use Webaccess\ProjectSquare\Entities\TicketState;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tickets\CreateTicketEvent;
use Webaccess\ProjectSquare\Interactors\Tickets\CreateTicketInteractor;
use Webaccess\ProjectSquare\Requests\Tickets\CreateTicketRequest;
use Webaccess\ProjectSquare\Responses\Tickets\CreateTicketResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class CreateTicketInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateTicketInteractor($this->ticketRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTicketWithoutTitle()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $this->interactor->execute(new CreateTicketRequest([
            'title' => '',
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTicketWithNonExistingProject()
    {
        $user = $this->createSampleUser();
        $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => 1,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTicketWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTicketWithUnauthorizedAllocatedUser()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $allocatedUser = $this->createSampleUser();
        $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $project->id,
            'allocatedUserID' => $allocatedUser->id,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTicketWithPassedDueDate()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $project->id,
            'allocatedUserID' => $user->id,
            'dueDate' => new DateTime('2010-01-01'),
            'requesterUserID' => $user->id
        ]));
    }

    public function testCreateTicket()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $response = $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $project->id,
            'statusID' => 2,
            'dueDate' => new \DateTime('now'),
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(CreateTicketResponse::class, $response);
        $this->assertInstanceOf(Ticket::class, $response->ticket);
        $this->assertInstanceOf(TicketState::class, $response->ticketState);
        $this->assertEquals('Sample ticket', $response->ticket->title);
        $this->assertEquals($project->id, $response->ticket->projectID);
        $this->assertEquals(2, $response->ticketState->statusID);
        $this->assertEquals(new \DateTime('now'), $response->ticketState->dueDate);
        $this->assertEquals($user->id, $response->ticketState->authorUserID);

        //Check insertion
        $this->assertCount(1, $this->ticketRepository->objects);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_TICKET,
            Mockery::type(CreateTicketEvent::class)
        );
    }
}