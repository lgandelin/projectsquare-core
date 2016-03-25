<?php

namespace Webaccess\ProjectSquare\Repositories;

interface RoleRepository
{
    public static function getRole($roleID);

    public static function getRoles();

    public static function getRolesPaginatedList($limit);

    public static function createRole($name);

    public static function updateRole($roleID, $name);

    public static function deleteRole($roleID);
}
