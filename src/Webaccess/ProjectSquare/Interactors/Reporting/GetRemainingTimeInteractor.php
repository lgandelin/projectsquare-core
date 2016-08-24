<?php

namespace Webaccess\ProjectSquare\Interactors\Reporting;

use Webaccess\ProjectSquare\Responses\Reporting\GetRemainingTimeResponse;

class GetRemainingTimeInteractor
{
    const HOURS_IN_DAY = 7;

    public function getRemainingTime($estimatedTime, $spentTime)
    {
        if ($spentTime->days * self::HOURS_IN_DAY + $spentTime->hours > $estimatedTime->days * self::HOURS_IN_DAY + $estimatedTime->hours) {
            $remainingTimeDays = 0;
            $remainingTimeHours = 0;
        } else {
            $spentTimeDays = $spentTime->days;
            if ($estimatedTime->hours >= $spentTime->hours) {
                $remainingTimeHours = $estimatedTime->hours - $spentTime->hours;
            } else {
                $remainingTimeHours = $estimatedTime->hours + self::HOURS_IN_DAY - $spentTime->hours;
                $spentTimeDays++;
            }

            $remainingTimeDays = ($estimatedTime->days - $spentTimeDays > 0) ? $estimatedTime->days - $spentTimeDays : 0;
        }

        return new GetRemainingTimeResponse(['days' => $remainingTimeDays, 'hours' => $remainingTimeHours]);
    }
}