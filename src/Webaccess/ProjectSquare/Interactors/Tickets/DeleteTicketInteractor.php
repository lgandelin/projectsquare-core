<?php

namespace Webaccess\ProjectSquare\Interactors\Tickets;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Ticket;
use Webaccess\ProjectSquare\Events\Tickets\DeleteTicketEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Interactors\Notifications\GetNotificationsInteractor;
use Webaccess\ProjectSquare\Interactors\Planning\DeleteEventInteractor;
use Webaccess\ProjectSquare\Interactors\Planning\GetEventsInteractor;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TicketRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Planning\DeleteEventRequest;
use Webaccess\ProjectSquare\Requests\Planning\GetEventsRequest;
use Webaccess\ProjectSquare\Requests\Tickets\DeleteTicketRequest;
use Webaccess\ProjectSquare\Responses\Tickets\DeleteTicketResponse;

class DeleteTicketInteractor extends GetTicketInteractor
{
    protected $repository;
    protected $projectRepository;
    protected $userRepository;

    public function __construct(TicketRepository $repository, ProjectRepository $projectRepository, UserRepository $userRepository, EventRepository $eventRepository, NotificationRepository $notificationRepository)
    {
        parent::__construct($repository);
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->eventRepository = $eventRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute(DeleteTicketRequest $request)
    {
        $ticket = $this->getTicket($request->ticketID);
        $this->validateRequest($request, $ticket);
        $this->deleteLinkedEvents($ticket->id);
        $this->deleteLinkedNotifications($ticket->id);
        $this->dispatchEvent($ticket);
        $this->deleteTicket($ticket);

        return new DeleteTicketResponse([
            'ticket' => $ticket,
        ]);
    }

    private function validateRequest(DeleteTicketRequest $request, Ticket $ticket)
    {
        $this->validateRequesterPermissions($request, $ticket);
    }

    private function validateRequesterPermissions(DeleteTicketRequest $request, Ticket $ticket)
    {
        if (!$this->isUserAuthorizedToDeleteTicket($request->requesterUserID, $ticket)) {
            throw new \Exception(Context::get('translator')->translate('users.ticket_deletion_not_allowed'));
        }
    }

    private function isUserAuthorizedToDeleteTicket($userID, Ticket $ticket)
    {
        $user = $this->userRepository->getUser($userID);

        return $this->projectRepository->isUserInProject($ticket->projectID, $userID) || $user->isAdministrator;
    }

    private function deleteTicket(Ticket $ticket)
    {
        $this->repository->deleteTicket($ticket->id);
    }

    private function dispatchEvent(Ticket $ticket)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::DELETE_TICKET,
            new DeleteTicketEvent($ticket)
        );
    }

    protected function deleteLinkedEvents($ticketID)
    {
        $events = (new GetEventsInteractor($this->eventRepository))->execute(new GetEventsRequest([
            'ticketID' => $ticketID
        ]));

        if (is_array($events) && sizeof($events) > 0) {
            foreach ($events as $event) {
                (new DeleteEventInteractor($this->eventRepository, $this->notificationRepository))->execute(new DeleteEventRequest([
                    'eventID' => $event->id
                ]));
            }
        }
    }

    private function deleteLinkedNotifications($ticketID)
    {
        $this->notificationRepository->removeNotificationsByTypeAndEntityID('TICKET_CREATED', $ticketID);
    }
}
