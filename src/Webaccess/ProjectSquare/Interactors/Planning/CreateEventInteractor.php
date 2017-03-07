<?php

namespace Webaccess\ProjectSquare\Interactors\Planning;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Event;
use Webaccess\ProjectSquare\Events\Planning\CreateEventEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Interactors\Notifications\CreateNotificationInteractor;
use Webaccess\ProjectSquare\Interactors\Tasks\UpdateTaskInteractor;
use Webaccess\ProjectSquare\Interactors\Tickets\UpdateTicketInteractor;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Repositories\TicketRepository;
use Webaccess\ProjectSquare\Requests\Planning\CreateEventRequest;
use Webaccess\ProjectSquare\Requests\Notifications\CreateNotificationRequest;
use Webaccess\ProjectSquare\Requests\Tasks\UpdateTaskRequest;
use Webaccess\ProjectSquare\Requests\Tickets\UpdateTicketRequest;
use Webaccess\ProjectSquare\Responses\Planning\CreateEventResponse;

class CreateEventInteractor
{
    public function __construct(EventRepository $repository, NotificationRepository $notificationRepository, TicketRepository $ticketRepository, ProjectRepository $projectRepository, TaskRepository $taskRepository)
    {
        $this->repository = $repository;
        $this->notificationRepository = $notificationRepository;
        $this->ticketRepository = $ticketRepository;
        $this->projectRepository = $projectRepository;
        $this->taskRepository = $taskRepository;
    }

    public function execute(CreateEventRequest $request)
    {
        $this->validateRequest($request);
        $this->allocateTicketIfRequired($request);
        $this->allocateTaskIfRequired($request);
        $event = $this->createEvent($request);
        $this->createNotificationIfRequired($request, $event);
        $this->dispatchEvent($event);

        return new CreateEventResponse([
            'event' => $event,
        ]);
    }

    private function validateRequest(CreateEventRequest $request)
    {
        $this->validateDates($request);
        //TODO : if ticket, validate ticket
        //TODO : if project, validate project
    }

    private function validateDates(CreateEventRequest $request)
    {
        if (!$request->startTime instanceof \DateTime || !$request->endTime instanceof \DateTime) {
            throw new \Exception(Context::get('translator')->translate('events.invalid_event_dates'));
        }
    }

    private function createEvent(CreateEventRequest $request)
    {
        $event = new Event();
        $event->name = $request->name;
        $event->userID = $request->userID;
        $event->startTime = $request->startTime;
        $event->endTime = $request->endTime;
        $event->ticketID = $request->ticketID;
        $event->taskID = $request->taskID;
        $event->projectID = $request->projectID;
        if ($request->ticketID != null) {
            $ticket = $this->ticketRepository->getTicket($request->ticketID);
            $event->projectID = $ticket->projectID;
        }
        if ($request->taskID != null) {
            $task = $this->taskRepository->getTask($request->taskID);
            $event->projectID = $task->projectID;
        }

        return $this->repository->persistEvent($event);
    }

    private function createNotificationIfRequired(CreateEventRequest $request, Event $event)
    {
        if ($this->isNotificationRequired($request)) {
            (new CreateNotificationInteractor($this->notificationRepository))->execute(new CreateNotificationRequest([
                'userID' => $request->userID,
                'entityID' => $event->id,
                'type' => 'EVENT_CREATED',
            ]));
        }
    }

    private function isNotificationRequired(CreateEventRequest $request)
    {
        return $request->requesterUserID != $request->userID;
    }

    private function dispatchEvent(Event $event)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::CREATE_EVENT,
            new CreateEventEvent($event)
        );
    }

    private function allocateTicketIfRequired(CreateEventRequest $request)
    {
        if ($request->ticketID) {
            (new UpdateTicketInteractor($this->ticketRepository, $this->projectRepository))->execute(new UpdateTicketRequest([
                'ticketID' => $request->ticketID,
                'allocatedUserID' => $request->userID,
                'requesterUserID' => $request->userID,
            ]));
        }
    }

    private function allocateTaskIfRequired(CreateEventRequest $request)
    {
        if ($request->taskID) {
            (new UpdateTaskInteractor($this->taskRepository, $this->projectRepository))->execute(new UpdateTaskRequest([
                'taskID' => $request->taskID,
                'allocatedUserID' => $request->userID,
                'requesterUserID' => $request->userID,
            ]));
        }
    }
}
