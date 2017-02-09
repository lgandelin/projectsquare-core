<?php

namespace Webaccess\ProjectSquare\Repositories;

interface SettingRepository
{
    public static function getSettingByKeyAndProject($key, $projectID);

    public static function getSettingByKeyAndUser($key, $userID);

    public static function createOrUpdateProjectSetting($projectID, $key, $value);
}
