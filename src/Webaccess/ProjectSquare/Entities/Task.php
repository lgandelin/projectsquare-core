<?php

namespace Webaccess\ProjectSquare\Entities;

class Task
{
    const TODO = 1;
    const IN_PROGRESS = 2;
    const COMPLETED = 3;

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
    public $allocatedUserID;
    public $order;
}