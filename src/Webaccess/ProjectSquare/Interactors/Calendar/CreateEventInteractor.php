<?php

namespace Webaccess\ProjectSquare\Interactors\Calendar;

use Webaccess\ProjectSquare\Requests\Calendar\CreateEventRequest;
use Webaccess\ProjectSquare\Responses\Calendar\CreateEventResponse;

class CreateEventInteractor
{
    public function execute(CreateEventRequest $request)
    {
        return new CreateEventResponse([
            'event' => null
        ]);
    }
}