<?php

namespace Webaccess\ProjectSquare\Entities;

class Task
{
    const TODO = 1;
    const IN_PROGRESS = 2;
    const COMPLETED = 3;

    const SPENT_TIME_EXCEEDED = 1;
    const SPENT_TIME_NORMAL = 2;
    const SPENT_TIME_AHEAD = 3;

    public $id;
    public $title;
    public $description;
    public $estimatedTimeDays;
    public $estimatedTimeHours;
    public $spentTimeDays;
    public $spentTimeHours;
    public $statusID;
    public $projectID;
    public $phaseID;
    public $authorUserID;
    public $allocatedUserID;
    public $order;
}