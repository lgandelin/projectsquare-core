<?php

namespace Webaccess\ProjectSquare\Interactors\Calendar;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Event;
use Webaccess\ProjectSquare\Entities\Notification;
use Webaccess\ProjectSquare\Events\Calendar\CreateEventEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Requests\Calendar\CreateEventRequest;
use Webaccess\ProjectSquare\Responses\Calendar\CreateEventResponse;

class CreateEventInteractor
{
    public function __construct(EventRepository $repository, NotificationRepository $notificationRepository)
    {
        $this->repository = $repository;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute(CreateEventRequest $request)
    {
        $this->validateRequest($request);
        $event = $this->createEvent($request);
        $this->createNotificationIfRequired($request, $event);
        $this->dispatchEvent($event);

        return new CreateEventResponse([
            'event' => $event
        ]);
    }

    private function validateRequest(CreateEventRequest $request)
    {
        //TODO : validate user
        //TODO : validate startTime and endTime
        //TODO : if ticket, validate ticket
        //TODO : if project, validate project
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

    private function createNotificationIfRequired(CreateEventRequest $request, Event $event)
    {
        if ($this->isNotificationRequired($request)) {
            $notification = new Notification();
            $notification->userID = $request->userID;
            $notification->read = false;
            $notification->entityID = $event->id;
            $notification->type = 'EVENT_CREATED';
            $this->notificationRepository->persistNotification($notification);
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
}