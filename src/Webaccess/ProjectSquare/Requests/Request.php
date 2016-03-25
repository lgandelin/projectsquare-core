<?php

namespace Webaccess\ProjectSquare\Requests;

class Request
{
    public function __construct($params = array())
    {
        foreach ($params as $key => $value) {
            if (property_exists($this, $key) && $value != null) {
                $this->$key = $value;
            }
        }
    }
}
