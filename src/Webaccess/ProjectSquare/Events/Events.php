<?php

namespace Webaccess\ProjectSquare\Events;

final class Events
{
    const CREATE_TICKET = 'tickets.create';
    const UPDATE_TICKET = 'tickets.update';
    const UPDATE_TICKET_INFOS = 'tickets.update.infos';
    const DELETE_TICKET = 'tickets.delete';
    const CREATE_CONVERSATION = 'conversations.create';
    const CREATE_MESSAGE = 'messages.create';
    const CREATE_EVENT = 'events.create';
    const UPDATE_EVENT = 'events.update';
    const DELETE_EVENT = 'events.delete';
    const CREATE_STEP = 'steps.create';
    const UPDATE_STEP = 'steps.update';
    const DELETE_STEP = 'steps.delete';
    const CREATE_TASK = 'tasks.create';
}
