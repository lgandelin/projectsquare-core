<?php

namespace Webaccess\ProjectSquareTests;

use Mockery;
use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Conversation;
use Webaccess\ProjectSquare\Entities\Project;
use Webaccess\ProjectSquare\Entities\Ticket;
use Webaccess\ProjectSquare\Entities\TicketState;
use Webaccess\ProjectSquare\Entities\User;
use Webaccess\ProjectSquare\Interactors\Messages\CreateMessageInteractor;
use Webaccess\ProjectSquare\Requests\Messages\CreateMessageRequest;
use Webaccess\ProjectSquareTests\Dummies\DummyTranslator;
use Webaccess\ProjectSquareTests\Repositories\InMemoryConversationRepository;
use Webaccess\ProjectSquareTests\Repositories\InMemoryEventRepository;
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
        $this->eventRepository = new InMemoryEventRepository();
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
        $response = (new CreateMessageInteractor(
            $this->messageRepository,
            $this->conversationRepository,
            $this->userRepository,
            $this->projectRepository
        ))->execute(new CreateMessageRequest([
            'content' => 'Sample message',
            'conversationID' => $conversationID,
            'requesterUserID' => $userID
        ]));

        return $response->message;
    }
}