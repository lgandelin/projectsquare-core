<?php

namespace Webaccess\ProjectSquare\Repositories;

interface AlertRepository
{
    public static function getLastAlerts($limit);

    public static function createAlert($type, $variables, $projectID);
}
