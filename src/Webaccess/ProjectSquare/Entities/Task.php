<?php

namespace Webaccess\ProjectSquare\Entities;

class Task
{
    public $id;
    public $title;
    public $description;
    public $estimatedTime;
    public $statusID;
    public $projectID;
    public $startDate;
    public $endDate;
    public $allocatedUserID;
}