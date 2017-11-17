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
        $this->interactor = new CreateTicketInteractor($this->ticketRepository, $this->projectRepository, $this->userRepository, $this->notificationRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTicketWithoutTitle()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project->id, $user->id, null);
        $this->interactor->execute(new CreateTicketRequest([
            'title' => '',
            'statusID' => 2,
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTicketWithoutStatus()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project->id, $user->id, null);
        $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
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
            'statusID' => 2,
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
            'statusID' => 2,
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
        $this->projectRepository->addUserToProject($project->id, $user->id, null);
        $allocatedUser = $this->createSampleUser();
        $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $project->id,
            'statusID' => 2,
            'allocatedUserID' => $allocatedUser->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testCreateTicket()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project->id, $user->id, null);
        $dateTime = new \DateTime('2029-01-01');

        $response = $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $project->id,
            'statusID' => 2,
            'dueDate' => $dateTime,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(CreateTicketResponse::class, $response);
        $this->assertInstanceOf(Ticket::class, $response->ticket);
        $this->assertInstanceOf(TicketState::class, $response->ticketState);
        $this->assertEquals('Sample ticket', $response->ticket->title);
        $this->assertEquals($project->id, $response->ticket->projectID);
        $this->assertEquals(2, $response->ticketState->statusID);
        $this->assertEquals($dateTime, $response->ticketState->dueDate);
        $this->assertEquals($user->id, $response->ticketState->authorUserID);

        //Check insertion
        $this->assertCount(1, $this->ticketRepository->objects);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_TICKET,
            Mockery::type(CreateTicketEvent::class)
        );
    }

    public function testCreateTicketCheckNotifications()
    {
        $project = $this->createSampleProject();
        $user1 = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project->id, $user1->id, null);

        $user2 = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project->id, $user2->id, null);

        $response = $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $project->id,
            'statusID' => 2,
            'allocatedUserID' => $user1->id,
            'requesterUserID' => $user1->id
        ]));

        $this->assertCount(0, $this->notificationRepository->objects);
    }

    public function testCreateTicketCheckNotifications2()
    {
        $project = $this->createSampleProject();
        $user1 = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project->id, $user1->id, null);

        $user2 = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project->id, $user2->id, null);

        $response = $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $project->id,
            'statusID' => 2,
            'allocatedUserID' => $user2->id,
            'requesterUserID' => $user1->id
        ]));

        $this->assertCount(1, $this->notificationRepository->objects);
    }
}