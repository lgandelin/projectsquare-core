<?php

namespace Webaccess\ProjectSquare\Interactors\Reporting;

use Webaccess\ProjectSquare\Responses\Reporting\GetRemainingTimeResponse;

class GetRemainingTimeInteractor
{
    const HOURS_IN_DAY = 7;

    public function getRemainingTime($scheduledTime, $spentTime)
    {
        $spentTimeInHours = $spentTime->days * self::HOURS_IN_DAY + $spentTime->hours;
        $scheduledTimeInHours = $scheduledTime->days * self::HOURS_IN_DAY + $scheduledTime->hours;

        if ($spentTimeInHours > $scheduledTimeInHours) {
            $remainingTimeDays = 0;
            $remainingTimeHours = 0;
        } else {
            $remainingTimeInHours = $scheduledTimeInHours - $spentTimeInHours;
            $remainingTimeDays = floor($remainingTimeInHours / self::HOURS_IN_DAY);
            $remainingTimeHours = floor($remainingTimeInHours - $remainingTimeDays * self::HOURS_IN_DAY);
        }

        return new GetRemainingTimeResponse(['days' => $remainingTimeDays, 'hours' => $remainingTimeHours]);
    }
}