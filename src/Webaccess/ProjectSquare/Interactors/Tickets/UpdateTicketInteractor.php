<?php

namespace Webaccess\ProjectSquare\Interactors\Tickets;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Ticket;
use Webaccess\ProjectSquare\Entities\TicketState;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tickets\UpdateTicketEvent;
use Webaccess\ProjectSquare\Interactors\Notifications\CreateNotificationInteractor;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TicketRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Notifications\CreateNotificationRequest;
use Webaccess\ProjectSquare\Requests\Tickets\UpdateTicketRequest;
use Webaccess\ProjectSquare\Responses\Tickets\UpdateTicketResponse;

class UpdateTicketInteractor extends GetTicketInteractor
{
    protected $repository;
    protected $projectRepository;
    protected $userRepository;
    protected $notificationRepository;

    public function __construct(TicketRepository $repository, ProjectRepository $projectRepository, UserRepository $userRepository, NotificationRepository $notificationRepository)
    {
        parent::__construct($repository);
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute(UpdateTicketRequest $request)
    {
        $ticket = $this->getTicket($request->ticketID);
        $this->validateRequest($request);
        $ticketState = $this->createTicketState($request);
        $this->createNotifications($request, $ticket);
        $this->dispatchEvent($ticket->id);

        return new UpdateTicketResponse([
            'ticket' => $ticket,
            'ticketState' => $ticketState,
        ]);
    }

    private function validateRequest(UpdateTicketRequest $request)
    {
        $this->validateDueDate($request);
        $this->validateRequesterPermissions($request);
        $this->validateAllocatedUser($request);
    }

    private function validateDueDate(UpdateTicketRequest $request)
    {
        if ($request->dueDate && $request->dueDate < new \DateTime('now')) {
            throw new \Exception(Context::get('translator')->translate('tickets.due_date_already_passed'));
        }
    }

    private function validateRequesterPermissions(UpdateTicketRequest $request)
    {
        $ticket = $this->repository->getTicket($request->ticketID);

        if (!$this->isUserInProject($ticket->projectID, $request->requesterUserID)) {
            throw new \Exception(Context::get('translator')->translate('users.ticket_update_not_allowed'));
        }
    }

    private function validateAllocatedUser(UpdateTicketRequest $request)
    {
        $ticket = $this->repository->getTicket($request->ticketID);

        if ($request->allocatedUserID && !$this->isUserInProject($ticket->projectID, $request->allocatedUserID)) {
            throw new \Exception(Context::get('translator')->translate('users.allocated_user_not_in_project'));
        }
    }

    private function isUserInProject($projectID, $userID)
    {
        return $this->projectRepository->isUserInProject($projectID, $userID);
    }

    private function createTicketState(UpdateTicketRequest $request)
    {
        $ticket = $this->repository->getTicketWithStates($request->ticketID);
        $lastState = $ticket->states[0];
        $ticketState = new TicketState();
        $ticketState->ticketID = $request->ticketID;
        $ticketState->statusID = (isset($request->statusID)) ? $request->statusID : $lastState->statusID;
        $ticketState->authorUserID = (isset($request->authorUserID)) ? $request->authorUserID : $lastState->authorUserID;
        $ticketState->allocatedUserID = (isset($request->allocatedUserID)) ? $request->allocatedUserID : $lastState->allocatedUserID;
        if (isset($request->allocatedUserID) && $request->allocatedUserID === 0) $ticketState->allocatedUserID = null;
        $ticketState->priority = (isset($request->priority)) ? $request->priority : $lastState->priority;
        $ticketState->dueDate = (isset($request->dueDate)) ? $request->dueDate : \DateTime::createFromFormat('d/m/Y', $lastState->dueDate);
        $ticketState->estimatedTimeHours = (isset($request->estimatedTimeHours)) ? $request->estimatedTimeHours : $lastState->estimatedTimeHours;
        $ticketState->spentTimeHours = (isset($request->spentTimeHours)) ? $request->spentTimeHours : $lastState->spentTimeHours;
        $ticketState->comments = $request->comments;
        $ticketState = $this->repository->persistTicketState($ticketState);

        return $ticketState;
    }


    private function createNotifications(UpdateTicketRequest $request, Ticket $ticket)
    {
        $project = $this->projectRepository->getProject($ticket->projectID);

        if ($request->allocatedUserID != $request->requesterUserID) {
            if ($allocatedUser = $this->userRepository->getUser($request->allocatedUserID)) {
                $this->notifyUser($ticket, $allocatedUser);
            }
        }

        //Client users
        foreach ($this->userRepository->getClientUsers($project->clientID) as $user) {
            if ($user->id != $request->requesterUserID) {
                $this->notifyUser($ticket, $user);
            }
        }
    }

    private function notifyUser($ticket, $user)
    {
        (new CreateNotificationInteractor($this->notificationRepository))->execute(new CreateNotificationRequest([
            'userID' => $user->id,
            'entityID' => $ticket->id,
            'type' => 'TICKET_UPDATED',
        ]));
    }

    private function dispatchEvent($ticketID)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::UPDATE_TICKET,
            new UpdateTicketEvent($ticketID)
        );
    }
}
