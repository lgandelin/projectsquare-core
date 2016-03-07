<?php

namespace Webaccess\ProjectSquare\Responses\Messages;

use Webaccess\ProjectSquare\Responses\Response;

class CreateMessageResponse extends Response
{
    public $message;
    public $createdAt;
    public $user;
    public $count;
}