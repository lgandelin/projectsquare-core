<?php

namespace Webaccess\ProjectSquare\Interactors\Calendar;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Event;
use Webaccess\ProjectSquare\Events\Calendar\CreateEventEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Requests\Calendar\CreateEventRequest;
use Webaccess\ProjectSquare\Responses\Calendar\CreateEventResponse;

class CreateEventInteractor
{
    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CreateEventRequest $request)
    {
        $this->validateRequest($request);
        $event = $this->createEvent($request);
        $this->dispatchEvent($event);

        return new CreateEventResponse([
            'event' => $event
        ]);
    }

    private function createEvent(CreateEventRequest $request)
    {
        $event = new Event();
        $event->name = $request->name;
        $event->userID = $request->userID;
        $event->startTime = $request->startTime;
        $event->endTime = $request->endTime;
        $event->ticketID = $request->ticketID;
        $event->projectID = $request->projectID;

        return $this->repository->persistEvent($event);
    }

    private function dispatchEvent($event)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::CREATE_EVENT,
            new CreateEventEvent($event)
        );
    }

    private function validateRequest($request)
    {
        //TODO : validate user
        //TODO : validate startTime and endTime
        //TODO : if ticket, validate ticket
        //TODO : if project, validate project
    }
}