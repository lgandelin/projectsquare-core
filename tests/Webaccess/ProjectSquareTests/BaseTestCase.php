<?php

namespace Webaccess\ProjectSquareTests;

use Mockery;
use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Conversation;
use Webaccess\ProjectSquare\Entities\Message;
use Webaccess\ProjectSquare\Entities\Project;
use Webaccess\ProjectSquare\Entities\Ticket;
use Webaccess\ProjectSquare\Entities\TicketState;
use Webaccess\ProjectSquare\Entities\User;
use Webaccess\ProjectSquareTests\Dummies\DummyTranslator;
use Webaccess\ProjectSquareTests\Repositories\InMemoryConversationRepository;
use Webaccess\ProjectSquareTests\Repositories\InMemoryMessageRepository;
use Webaccess\ProjectSquareTests\Repositories\InMemoryProjectRepository;
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

    protected function createSampleProject()
    {
        $project = new Project();
        $project->name = 'Sample Project';

        return $this->projectRepository->persistProject($project);
    }

    protected function createSampleUser()
    {
        $user = new User();
        $user->firstName = 'John';
        $user->lastName = 'Doe';

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
        $message = new Message();
        $message->content = 'Sample message';
        $message->conversationID = $conversationID;
        $message->userID = $userID;

        return $this->messageRepository->persistMessage($message);
    }
}