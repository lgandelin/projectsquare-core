<?php

namespace Webaccess\ProjectSquare\Interactors\Reporting;

use Webaccess\ProjectSquare\Interactors\Tickets\GetTicketInteractor;
use Webaccess\ProjectSquare\Repositories\TicketRepository;
use Webaccess\ProjectSquare\Responses\Reporting\GetTicketsTotalTimeResponse;

class GetTicketsTotalTimeInteractor
{
    const HOURS_IN_DAY = 7;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->repository = $ticketRepository;
        $this->getTicketsInteractor = new GetTicketInteractor($ticketRepository);
    }

    public function getTicketsTotalEstimatedTime($userID, $projectID)
    {
        $tickets = $this->getTicketsInteractor->getTicketsList($userID, $projectID);

        $totalEstimatedTimeDays = 0;
        $totalEstimatedTimeHours = 0;

        //if (is_array($tickets) && sizeof($tickets) > 0) {
            foreach ($tickets as $ticket) {
                $totalEstimatedTimeDays += $ticket->last_state->estimated_time_days;
                $totalEstimatedTimeHours += $ticket->last_state->estimated_time_hours;
            }
        //}

        if ($totalEstimatedTimeHours >= self::HOURS_IN_DAY) {
            $totalEstimatedTimeDays += floor($totalEstimatedTimeHours / self::HOURS_IN_DAY);
            $totalEstimatedTimeHours = $totalEstimatedTimeHours % self::HOURS_IN_DAY;
        }

        return new GetTicketsTotalTimeResponse(['days' => $totalEstimatedTimeDays, 'hours' => $totalEstimatedTimeHours]);
    }

    public function getTicketsTotalSpentTime($userID, $projectID)
    {
        $tickets = $this->getTicketsInteractor->getTicketsList($userID, $projectID);

        $totalSpentTimeDays = 0;
        $totalSpentTimeHours = 0;

        //if (is_array($tickets) && sizeof($tickets) > 0) {
            foreach ($tickets as $ticket) {
                $totalSpentTimeDays += $ticket->last_state->spent_time_days;
                $totalSpentTimeHours += $ticket->last_state->spent_time_hours;
            }
        //}

        if ($totalSpentTimeHours >= self::HOURS_IN_DAY) {
            $totalSpentTimeDays += floor($totalSpentTimeHours / self::HOURS_IN_DAY);
            $totalSpentTimeHours = $totalSpentTimeHours % self::HOURS_IN_DAY;
        }

        return new GetTicketsTotalTimeResponse(['days' => $totalSpentTimeDays, 'hours' => $totalSpentTimeHours]);
    }
}