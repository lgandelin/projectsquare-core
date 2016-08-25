<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Ticket;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tickets\DeleteTicketEvent;
use Webaccess\ProjectSquare\Interactors\Planning\CreateEventInteractor;
use Webaccess\ProjectSquare\Interactors\Tickets\CreateTicketInteractor;
use Webaccess\ProjectSquare\Interactors\Tickets\DeleteTicketInteractor;
use Webaccess\ProjectSquare\Requests\Planning\CreateEventRequest;
use Webaccess\ProjectSquare\Requests\Tickets\CreateTicketRequest;
use Webaccess\ProjectSquare\Requests\Tickets\DeleteTicketRequest;
use Webaccess\ProjectSquare\Responses\Tickets\DeleteTicketResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class DeleteTicketInteractorTest extends BaseTestCase
{
    public $userRepository;

    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DeleteTicketInteractor($this->ticketRepository, $this->projectRepository, $this->eventRepository, $this->notificationRepository);
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

    public function testDeleteTicketAlongWithNotifications()
    {
        $user1 = $this->createSampleUser();
        $user2 = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->projectRepository->addUserToProject($project, $user1, null);
        $this->projectRepository->addUserToProject($project, $user2, null);

        $response = (new CreateTicketInteractor(
            $this->ticketRepository,
            $this->projectRepository,
            $this->userRepository,
            $this->notificationRepository
        ))->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $project->id,
            'allocatedUserID' => $user2->id,
            'requesterUserID' => $user1->id
        ]));

        $ticket = $response->ticket;

        $this->assertCount(1, $this->notificationRepository->objects);

        $this->interactor->execute(new DeleteTicketRequest([
            'ticketID' => $ticket->id,
            'requesterUserID' => $user1->id
        ]));

        $this->assertCount(0, $this->notificationRepository->objects);
    }

    public function testDeleteTicketAlongWithEvents()
    {
        $user1 = $this->createSampleUser();
        $user2 = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->projectRepository->addUserToProject($project, $user1, null);
        $this->projectRepository->addUserToProject($project, $user2, null);

        $response = (new CreateTicketInteractor(
            $this->ticketRepository,
            $this->projectRepository,
            $this->userRepository,
            $this->notificationRepository
        ))->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $project->id,
            'allocatedUserID' => $user2->id,
            'requesterUserID' => $user1->id
        ]));

        $ticket = $response->ticket;

        (new CreateEventInteractor(
            $this->eventRepository,
            $this->notificationRepository,
            $this->ticketRepository,
            $this->projectRepository,
            $this->taskRepository
        ))->execute(new CreateEventRequest([
            'name' => 'Sample event',
            'startTime' => new \DateTime('2016-03-15 10:30:00'),
            'endTime' => new \DateTime('2016-03-15 18:30:00'),
            'userID' => $user2->id,
            'ticketID' => $ticket->id,
            'requesterUserID' => $user1->id,
        ]));

        $this->assertCount(1, $this->eventRepository->objects);

        $this->interactor->execute(new DeleteTicketRequest([
            'ticketID' => $ticket->id,
            'requesterUserID' => $user1->id
        ]));

        $this->assertCount(0, $this->eventRepository->objects);
    }
}