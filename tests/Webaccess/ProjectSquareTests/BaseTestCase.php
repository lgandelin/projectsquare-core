<?php

namespace Webaccess\ProjectSquareTests;

use Mockery;
use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Client;
use Webaccess\ProjectSquare\Entities\Conversation;
use Webaccess\ProjectSquare\Entities\Project;
use Webaccess\ProjectSquare\Entities\Ticket;
use Webaccess\ProjectSquare\Entities\TicketState;
use Webaccess\ProjectSquare\Entities\User;
use Webaccess\ProjectSquare\Interactors\Planning\CreateEventInteractor;
use Webaccess\ProjectSquare\Interactors\Messages\CreateMessageInteractor;
use Webaccess\ProjectSquare\Interactors\Calendar\CreateStepInteractor;
use Webaccess\ProjectSquare\Interactors\Tasks\CreateTaskInteractor;
use Webaccess\ProjectSquare\Interactors\Todos\CreateTodoInteractor;
use Webaccess\ProjectSquare\Requests\Planning\CreateEventRequest;
use Webaccess\ProjectSquare\Requests\Messages\CreateMessageRequest;
use Webaccess\ProjectSquare\Requests\Calendar\CreateStepRequest;
use Webaccess\ProjectSquare\Requests\Tasks\CreateTaskRequest;
use Webaccess\ProjectSquare\Requests\Todos\CreateTodoRequest;
use Webaccess\ProjectSquareTests\Dummies\DummyTranslator;
use Webaccess\ProjectSquareTests\Repositories\InMemoryClientRepository;
use Webaccess\ProjectSquareTests\Repositories\InMemoryConversationRepository;
use Webaccess\ProjectSquareTests\Repositories\InMemoryEventRepository;
use Webaccess\ProjectSquareTests\Repositories\InMemoryMessageRepository;
use Webaccess\ProjectSquareTests\Repositories\InMemoryNotificationRepository;
use Webaccess\ProjectSquareTests\Repositories\InMemoryProjectRepository;
use Webaccess\ProjectSquareTests\Repositories\InMemoryStepRepository;
use Webaccess\ProjectSquareTests\Repositories\InMemoryTaskRepository;
use Webaccess\ProjectSquareTests\Repositories\InMemoryTodoRepository;
use Webaccess\ProjectSquareTests\Repositories\InMemoryTicketRepository;
use Webaccess\ProjectSquareTests\Repositories\InMemoryUserRepository;

class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        $this->ticketRepository = new InMemoryTicketRepository();
        $this->projectRepository = new InMemoryProjectRepository();
        $this->userRepository = new InMemoryUserRepository();
        $this->conversationRepository = new InMemoryConversationRepository();
        $this->messageRepository = new InMemoryMessageRepository();
        $this->eventRepository = new InMemoryEventRepository();
        $this->notificationRepository = new InMemoryNotificationRepository();
        $this->stepRepository = new InMemoryStepRepository();
        $this->todoRepository = new InMemoryTodoRepository();
        $this->taskRepository = new InMemoryTaskRepository();
        $this->clientRepository = new InMemoryClientRepository();

        Context::set('translator', new DummyTranslator());
        Context::set('event_dispatcher', Mockery::spy('EventDispatcherInterface'));
    }

    protected function createSampleTicket($title, $projectID, $description)
    {
        $ticket = new Ticket();
        $ticket->title = $title;
        $ticket->projectID = $projectID;
        $ticket->description = $description;
        $ticket = $this->ticketRepository->persistTicket($ticket);

        $ticketState = new TicketState();
        $ticketState->ticketID = $ticket->id;
        $this->ticketRepository->persistTicketState($ticketState);

        return $ticket->id;
    }

    protected function createSampleClient()
    {
        $client = new Client();
        $client->name = 'Sample Client';

        return $this->clientRepository->persistClient($client);
    }

    protected function createSampleProject($clientID = null)
    {
        $project = new Project();
        $project->name = 'Sample Project';
        if ($clientID) $project->clientID = $clientID;

        return $this->projectRepository->persistProject($project);
    }

    protected function createSampleUser($isAdministrator = false)
    {
        $user = new User();
        $user->firstName = 'John';
        $user->lastName = 'Doe';
        $user->isAdministrator = $isAdministrator;

        return $this->userRepository->persistUser($user);
    }

    protected function createSampleConversation($projectID)
    {
        $conversation = new Conversation();
        $conversation->title = 'Sample title';
        $conversation->projectID = $projectID;

        return $this->conversationRepository->persistConversation($conversation);
    }

    protected function createSampleMessage($conversationID, $userID)
    {
        $response = (new CreateMessageInteractor(
            $this->messageRepository,
            $this->conversationRepository,
            $this->userRepository,
            $this->projectRepository,
            $this->notificationRepository
        ))->execute(new CreateMessageRequest([
            'content' => 'Sample message',
            'conversationID' => $conversationID,
            'requesterUserID' => $userID
        ]));

        return $response->message;
    }

    protected function createSampleEvent($userID, $requesterUserID = null)
    {
        $response = (new CreateEventInteractor(
            $this->eventRepository,
            $this->notificationRepository,
            $this->ticketRepository,
            $this->projectRepository,
            $this->taskRepository
        ))->execute(new CreateEventRequest([
            'name' => 'Sample event',
            'startTime' => new \DateTime('2016-03-15 10:30:00'),
            'endTime' => new \DateTime('2016-03-15 18:30:00'),
            'userID' => $userID,
            'requesterUserID' => ($requesterUserID) ? $requesterUserID : $userID,
        ]));

        return $response->event;
    }

    protected function createSampleStep($projectID, $requesterUserID)
    {
        $response = (new CreateStepInteractor(
            $this->stepRepository,
            $this->projectRepository
        ))->execute(new CreateStepRequest([
            'name' => 'Sample step',
            'startTime' => new \DateTime('2016-03-15 10:30:00'),
            'endTime' => new \DateTime('2016-03-15 18:30:00'),
            'projectID' => $projectID,
            'requesterUserID' => $requesterUserID
        ]));

        return $response->step;
    }

    protected function createSampleTodo($userID)
    {
        $response = (new CreateTodoInteractor(
            $this->todoRepository
        ))->execute(new CreateTodoRequest([
            'name' => 'Sample todo',
            'userID' => $userID
        ]));

        return $response->todo;
    }

    protected function createSampleTask($projectID = null)
    {
        $response = (new CreateTaskInteractor(
            $this->taskRepository,
            $this->projectRepository,
            $this->userRepository,
            $this->notificationRepository
        ))->execute(new CreateTaskRequest([
            'title' => 'Sample task',
            'projectID' => $projectID
        ]));

        return $response->task;
    }
}