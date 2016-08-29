<?php

namespace Webaccess\ProjectSquare\Interactors\Clients;

use Webaccess\ProjectSquare\Repositories\ClientRepository;
use Webaccess\ProjectSquare\Requests\Clients\UpdateClientRequest;

class UpdateClientInteractor
{
    public function __construct(ClientRepository $clientRepository)
    {
        $this->repository = $clientRepository;
    }

    public function execute(UpdateClientRequest $request)
    {
        $client = $this->getClient($request->clientID);
        if ($request->name !== null) $client->name = $request->name;
        if ($request->address !== null) $client->address = $request->address;

        $this->repository->persistClient($client);
    }

    private function getClient($clientID)
    {
        if (!$client = $this->repository->getClient($clientID)) {
            throw new \Exception(Context::get('translator')->translate('clients.client_not_found'));
        }

        return $client;
    }
}