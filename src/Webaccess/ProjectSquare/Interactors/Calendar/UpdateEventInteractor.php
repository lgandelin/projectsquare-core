<?php

namespace Webaccess\ProjectSquare\Interactors\Calendar;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Event;
use Webaccess\ProjectSquare\Events\Calendar\UpdateEventEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Requests\Calendar\UpdateEventRequest;
use Webaccess\ProjectSquare\Responses\Calendar\UpdateEventResponse;

class UpdateEventInteractor
{
    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UpdateEventRequest $request)
    {
        $event = $this->getEvent($request->eventID);
        $this->validateRequest($request);
        $this->updateEvent($event, $request);
        $this->dispatchEvent($event);

        return new UpdateEventResponse([
            'event' => $event,
        ]);
    }

    private function validateRequest(UpdateEventRequest $request)
    {
        //TODO : validate user
        //TODO : validate startTime and endTime
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
