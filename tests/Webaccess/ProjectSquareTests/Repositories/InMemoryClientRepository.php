<?php

namespace Webaccess\ProjectSquareTests\Repositories;

use Webaccess\ProjectSquare\Entities\Client;
use Webaccess\ProjectSquare\Repositories\ClientRepository;

class InMemoryClientRepository implements ClientRepository
{
    public $objects;

    public function __construct()
    {
        $this->objects = [];
    }

    public function getNextID()
    {
        return count($this->objects) + 1;
    }

    public function getClient($clientID)
    {
        if (isset($this->objects[$clientID])) {
            return $this->objects[$clientID];
        }

        return false;
    }

    public function persistClient(Client $client)
    {
        if (!isset($client->id)) {
            $client->id = self::getNextID();
        }
        $this->objects[$client->id]= $client;

        return $client;
    }

    public function deleteClient($clientID)
    {
        if (isset($this->objects[$clientID])) {
            unset($this->objects[$clientID]);
        }
    }
}