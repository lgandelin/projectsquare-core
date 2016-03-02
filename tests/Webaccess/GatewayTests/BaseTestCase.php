<?php

namespace Webaccess\GatewayTests;

use Mockery;
use Webaccess\Gateway\Context;
use Webaccess\Gateway\Entities\Conversation;
use Webaccess\Gateway\Entities\Project;
use Webaccess\Gateway\Entities\Ticket;
use Webaccess\Gateway\Entities\TicketState;
use Webaccess\Gateway\Entities\User;
use Webaccess\GatewayTests\Dummies\DummyTranslator;
use Webaccess\GatewayTests\Repositories\InMemoryConversationRepository;
use Webaccess\GatewayTests\Repositories\InMemoryMessageRepository;
use Webaccess\GatewayTests\Repositories\InMemoryProjectRepository;
use Webaccess\GatewayTests\Repositories\InMemoryTicketRepository;
use Webaccess\GatewayTests\Repositories\InMemoryUserRepository;

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
}