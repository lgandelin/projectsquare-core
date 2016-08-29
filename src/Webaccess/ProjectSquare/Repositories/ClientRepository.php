<?php

namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Client;

interface ClientRepository
{
    public function getClient($clientID);
    public function persistClient(Client $client);
    public function deleteClient($clientID);
}
