<?php

namespace Webaccess\ProjectSquare\Interactors\Tickets;

use Webaccess\ProjectSquare\Repositories\TicketRepository;

class GetTicketsInteractor
{
    protected $repository;

    public function __construct(TicketRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getTicketsByProjectID($projectID)
    {
        return $this->repository->getTicketsByProjectID($projectID);
    }
}