<?php

namespace Webaccess\ProjectSquare\Entities;

class Task
{
    public $id;
    public $title;
    public $description;
    public $estimatedTimeDays;
    public $estimatedTimeHours;
    public $spentTimeDays;
    public $spentTimeHours;
    public $statusID;
    public $projectID;
    public $startDate;
    public $endDate;
    public $allocatedUserID;
}