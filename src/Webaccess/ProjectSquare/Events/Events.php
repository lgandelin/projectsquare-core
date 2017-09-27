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
    const ALLOCATE_TASK_IN_PLANNING = 'planning.allocate_task';
    const CREATE_STEP = 'steps.create';
    const UPDATE_STEP = 'steps.update';
    const DELETE_STEP = 'steps.delete';
    const CREATE_TODO = 'todos.create';
    const UPDATE_TODO = 'todos.update';
    const DELETE_TODO = 'todos.delete';
    const CREATE_TASK = 'tasks.create';
    const UPDATE_TASK = 'tasks.update';
    const DELETE_TASK = 'tasks.delete';
    const CREATE_PROJECT = 'projects.create';
    const UPDATE_PROJECT = 'projects.update';
    const CREATE_PHASE = 'phases.create';
    const UPDATE_PHASE = 'phases.update';
    const DELETE_PHASE = 'phases.delete';
    const ALERT_LOADING_TIME = 'alerts.loading_time';
    const ALERT_STATUS_CODE = 'alerts.status_code';
}
