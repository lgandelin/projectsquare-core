<?php

namespace Webaccess\ProjectSquare\Repositories;

interface ClientRepository
{
    public static function getClient($clientID);

    public static function getClients();

    public static function getClientsPaginatedList($limit = null);

    public static function createClient($name);

    public static function updateClient($clientID, $name);

    public static function deleteClient($clientID);
}
