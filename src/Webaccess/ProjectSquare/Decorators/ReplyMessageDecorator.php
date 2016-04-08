<?php

namespace Webaccess\ProjectSquare\Decorators;

use Webaccess\ProjectSquare\Responses\Messages\CreateMessageResponse;

class ReplyMessageDecorator
{
    public function decorate(CreateMessageResponse $response)
    {
        return [
            'id' => $response->message->id,
            'datetime' => $response->createdAt->format('d/m/Y H:i:s'),
            'username' => $response->user->firstName . ' ' . $response->user->lastName,
            'message' => $response->message->content,
            'count' => $response->count,
        ];
    }
}