<?php

namespace Webaccess\ProjectSquare\Repositories;

interface TaskRepository
{
    public function getTasks($projectID = null);
}