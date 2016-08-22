<?php

namespace Webaccess\ProjectSquare\Interactors\Planning;

use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Requests\Planning\GetEventsRequest;

class GetEventsInteractor
{
    protected $repository;

    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(GetEventsRequest $request)
    {
        return $this->repository->getEvents($request->userID, $request->projectID, $request->ticketID, $request->taskID);
    }
}
