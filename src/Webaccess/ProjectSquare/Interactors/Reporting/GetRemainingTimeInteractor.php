<?php

namespace Webaccess\ProjectSquare\Interactors\Reporting;

use Webaccess\ProjectSquare\Responses\Reporting\GetRemainingTimeResponse;

class GetRemainingTimeInteractor
{
    const HOURS_IN_DAY = 7;

    public function getRemainingTime($scheduledTime, $spentTime)
    {
        if ($spentTime->days * self::HOURS_IN_DAY + $spentTime->hours > $scheduledTime->days * self::HOURS_IN_DAY + $scheduledTime->hours) {
            $remainingTimeDays = 0;
            $remainingTimeHours = 0;
        } else {
            $spentTimeDays = $spentTime->days;
            if ($scheduledTime->hours >= $spentTime->hours) {
                $remainingTimeHours = $scheduledTime->hours - $spentTime->hours;
            } else {
                $remainingTimeHours = $scheduledTime->hours + self::HOURS_IN_DAY - $spentTime->hours;
                $spentTimeDays++;
            }

            $remainingTimeDays = ($scheduledTime->days - $spentTimeDays > 0) ? $scheduledTime->days - $spentTimeDays : 0;
        }

        return new GetRemainingTimeResponse(['days' => $remainingTimeDays, 'hours' => $remainingTimeHours]);
    }
}