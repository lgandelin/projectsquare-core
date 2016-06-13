<?php

namespace Webaccess\ProjectSquare\Repositories;

interface ClientRepository
{
    public static function getClient($clientID);

    public static function getClients();

    public static function getClientsPaginatedList($limit = null);

    public static function createClient($name, $address);

    public static function updateClient($clientID, $name, $address);

    public static function deleteClient($clientID);
}
