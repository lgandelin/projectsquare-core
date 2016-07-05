<?php

namespace Webaccess\ProjectSquare\Repositories;

interface AlertRepository
{
    public static function getAlertsPaginatedList($limit=null);

    public static function createAlert($type, $variables, $projectID);
}
