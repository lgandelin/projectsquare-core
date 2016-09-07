<?php

namespace Webaccess\ProjectSquare\Interactors\Clients;

use Webaccess\ProjectSquare\Repositories\ClientRepository;

class GetClientInteractor
{
    public function __construct(ClientRepository $clientRepository)
    {
        $this->repository = $clientRepository;
    }

    public function execute($request)
    {
        return $this->repository->getClient($request->clientID);
    }
}