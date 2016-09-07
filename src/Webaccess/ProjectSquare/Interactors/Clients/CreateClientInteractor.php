<?php

namespace Webaccess\ProjectSquare\Interactors\Clients;

use Webaccess\ProjectSquare\Entities\Client;
use Webaccess\ProjectSquare\Repositories\ClientRepository;
use Webaccess\ProjectSquare\Requests\Clients\CreateClientRequest;
use Webaccess\ProjectSquare\Responses\Clients\CreateClientResponse;

class CreateClientInteractor
{
    public function __construct(ClientRepository $clientRepository)
    {
        $this->repository = $clientRepository;
    }

    public function execute(CreateClientRequest $request)
    {
        $client = $this->createClient($request);

        return new CreateClientResponse([
            'client' => $client,
        ]);
    }

    private function createClient($request)
    {
        $client = new Client();
        $client->name = $request->name;
        $client->address = $request->address;

        return $this->repository->persistClient($client);
    }
}