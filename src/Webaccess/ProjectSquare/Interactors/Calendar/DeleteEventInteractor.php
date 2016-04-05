<?php

namespace Webaccess\ProjectSquare\Interactors\Calendar;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Event;
use Webaccess\ProjectSquare\Events\Calendar\DeleteEventEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Requests\Calendar\DeleteEventRequest;
use Webaccess\ProjectSquare\Responses\Calendar\DeleteEventResponse;

class DeleteEventInteractor
{
    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(DeleteEventRequest $request)
    {
        $event = $this->getEvent($request->eventID);
        $this->validateRequest($request, $event);
        $this->deleteEvent($event);
        $this->dispatchEvent($event);

        return new DeleteEventResponse([
            'event' => $event,
        ]);
    }

    private function validateRequest(DeleteEventRequest $request, Event $event)
    {
        $this->validateRequesterPermissions($request, $event);
    }

    private function validateRequesterPermissions(DeleteEventRequest $request, Event $event)
    {
        if (!$this->isUserAuthorizedToDeleteEvent($request->requesterUserID, $event)) {
            throw new \Exception(Context::get('translator')->translate('users.event_deletion_not_allowed'));
        }
    }

    private function isUserAuthorizedToDeleteEvent($userID, Event $event)
    {
        return $userID == $event->userID;
    }

    private function getEvent($eventID)
    {
        if (!$event = $this->repository->getEvent($eventID)) {
            throw new \Exception(Context::get('translator')->translate('events.event_not_found'));
        }

        return $event;
    }

    private function deleteEvent($event)
    {
        $this->repository->removeEvent($event->id);
    }

    private function dispatchEvent($event)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::DELETE_EVENT,
            new DeleteEventEvent($event)
        );
    }
}
