<?php

namespace Webaccess\ProjectSquare\Interactors\Events;

use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Requests\Events\GetEventRequest;

class GetEventInteractor
{
    protected $repository;

    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(GetEventRequest $request)
    {
        return $this->repository->getEvent($request->eventID);
    }
}
