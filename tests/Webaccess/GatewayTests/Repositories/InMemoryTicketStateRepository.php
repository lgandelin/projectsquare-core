<?php

namespace Webaccess\GatewayTests\Repositories;

class InMemoryTicketStateRepository
{
    public $objects;

    public function __construct()
    {
        $this->objects = [];
    }

    public function getNextID()
    {
        return count($this->objects) + 1;
    }

    public function getTicketStates($ticketID)
    {
        $result = [];
        foreach ($this->objects as $ticketState) {
            if (isset($ticketState->ticketID) && $ticketState->ticketID == $ticketID) {
                $result[]= $ticketState;
            }
        }

        return $result;
    }
}