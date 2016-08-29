<?php

namespace Webaccess\ProjectSquare\Interactors\Clients;

use Webaccess\ProjectSquare\Repositories\ClientRepository;
use Webaccess\ProjectSquare\Requests\Clients\GetClientsRequest;

class GetClientsInteractor
{
    public function __construct(ClientRepository $taskRepository)
    {
        $this->repository = $taskRepository;
    }

    public function execute(GetClientsRequest $request)
    {
        return $this->repository->getClients();
    }
}