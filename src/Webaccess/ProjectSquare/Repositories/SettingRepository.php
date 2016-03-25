<?php

namespace Webaccess\ProjectSquare\Repositories;

interface SettingRepository
{
    public static function getSettingByKeyAndProject($key, $projectID);

    public static function createOrUpdateSetting($projectID, $key, $value);
}
