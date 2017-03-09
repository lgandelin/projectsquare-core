<?php

namespace Webaccess\ProjectSquare\Entities;

class Project
{
    const IN_PROGRESS = 1;
    const ARCHIVED = 2;

    public $id;
    public $name;
    public $clientID;
    public $color;
    public $websiteFrontURL;
    public $websiteBackURL;
    public $statusID;
    public $createdAt;
    public $udpatedAt;
}
