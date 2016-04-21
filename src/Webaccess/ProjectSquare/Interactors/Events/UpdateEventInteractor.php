<?php

namespace Webaccess\ProjectSquare\Interactors\Events;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Event;
use Webaccess\ProjectSquare\Events\Events\UpdateEventEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Exceptions\Events\EventUpdateNotAuthorizedException;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Requests\Events\UpdateEventRequest;
use Webaccess\ProjectSquare\Responses\Events\UpdateEventResponse;

class UpdateEventInteractor
{
    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UpdateEventRequest $request)
    {
        $event = $this->getEvent($request->eventID);
        $this->validateRequest($request, $event);
        $this->updateEvent($event, $request);
        $this->dispatchEvent($event);

        return new UpdateEventResponse([
            'event' => $event,
        ]);
    }

    private function validateRequest(UpdateEventRequest $request, Event $event)
    {
        //$this->validateRequesterPermissions($request, $event);
        $this->validateDates($request);
        //TODO : if ticket, validate ticket
        //TODO : if project, validate project
    }

    private function validateRequesterPermissions(UpdateEventRequest $request, Event $event)
    {
        if (!$this->isUserAuthorizedToDeleteEvent($request->requesterUserID, $event)) {
            throw new EventUpdateNotAuthorizedException(Context::get('translator')->translate('events.update_not_allowed'));
        }
    }

    private function validateDates(UpdateEventRequest $request)
    {
        if (($request->startTime && !$request->startTime instanceof \DateTime) || ($request->endTime && !$request->endTime instanceof \DateTime)) {
            throw new \Exception(Context::get('translator')->translate('events.invalid_event_dates'));
        }
    }

    private function isUserAuthorizedToDeleteEvent($userID, Event $event)
    {
        return $userID == $event->userID;
    }

    private function dispatchEvent(Event $event)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::UPDATE_EVENT,
            new UpdateEventEvent($event)
        );
    }

    private function getEvent($eventID)
    {
        if (!$event = $this->repository->getEvent($eventID)) {
            throw new \Exception(Context::get('translator')->translate('events.event_not_found'));
        }

        return $event;
    }

    private function updateEvent(Event $event, UpdateEventRequest $request)
    {
        if ($request->name) {
            $event->name = $request->name;
        }
        if ($request->startTime) {
            $event->startTime = $request->startTime;
        }
        if ($request->endTime) {
            $event->endTime = $request->endTime;
        }
        if ($request->projectID) {
            $event->projectID = $request->projectID;
        }
        if ($request->ticketID) {
            $event->ticketID = $request->ticketID;
        }
        if ($request->userID) {
            $event->userID = $request->userID;
        }

        $this->repository->persistEvent($event);
    }
}
