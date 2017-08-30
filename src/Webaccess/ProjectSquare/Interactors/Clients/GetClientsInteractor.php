<?php

namespace Webaccess\ProjectSquare\Interactors\Clients;

use Webaccess\ProjectSquare\Repositories\ClientRepository;
use Webaccess\ProjectSquare\Requests\Clients\GetClientsRequest;

class GetClientsInteractor
{
    public function __construct(ClientRepository $clientRepository)
    {
        $this->repository = $clientRepository;
    }

    public function execute(GetClientsRequest $request)
    {
        return $this->repository->getClients();
    }

    public function getClientsPaginatedList($limit, $sortColumn = null, $sortOrder = null)
    {
        return $this->repository->getClientsPaginatedList($limit, $sortColumn, $sortOrder);
    }
}