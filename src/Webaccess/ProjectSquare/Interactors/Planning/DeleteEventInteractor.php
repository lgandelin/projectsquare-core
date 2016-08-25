<?php

namespace Webaccess\ProjectSquare\Interactors\Planning;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Event;
use Webaccess\ProjectSquare\Events\Planning\DeleteEventEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Requests\Planning\DeleteEventRequest;
use Webaccess\ProjectSquare\Responses\Planning\DeleteEventResponse;

class DeleteEventInteractor
{
    public function __construct(EventRepository $repository, NotificationRepository $notificationRepository)
    {
        $this->repository = $repository;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute(DeleteEventRequest $request)
    {
        $event = $this->getEvent($request->eventID);
        $this->validateRequest($request, $event);
        $this->deleteEvent($event);
        $this->deleteLinkedNotifications($event);
        $this->dispatchEvent($event);

        return new DeleteEventResponse([
            'event' => $event,
        ]);
    }

    private function validateRequest(DeleteEventRequest $request, Event $event)
    {
    }

    private function getEvent($eventID)
    {
        if (!$event = $this->repository->getEvent($eventID)) {
            throw new \Exception(Context::get('translator')->translate('events.event_not_found'));
        }

        return $event;
    }

    private function deleteEvent(Event $event)
    {
        $this->repository->removeEvent($event->id);
    }

    private function dispatchEvent(Event $event)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::DELETE_EVENT,
            new DeleteEventEvent($event)
        );
    }

    private function deleteLinkedNotifications($event)
    {
        $this->notificationRepository->removeNotificationsByTypeAndEntityID('EVENT_CREATED', $event->id);
    }
}
