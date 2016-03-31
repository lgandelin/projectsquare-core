<?php

namespace Webaccess\ProjectSquare\Interactors\Calendar;

use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Requests\Calendar\GetEventsRequest;

class GetEventsInteractor
{
    protected $repository;

    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(GetEventsRequest $request)
    {
        return $this->repository->getEvents($request->userID, $request->projectID);
    }
}