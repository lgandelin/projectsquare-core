<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Entities\Project;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Tickets\CreateTicketEvent;
use Webaccess\Gateway\Interactors\Tickets\CreateTicketInteractor;
use Webaccess\Gateway\Requests\Tickets\CreateTicketRequest;
use Webaccess\Gateway\Responses\Tickets\CreateTicketResponse;
use Webaccess\GatewayTests\Dummies\DummyTranslator;
use Webaccess\GatewayTests\Repositories\InMemoryProjectRepository;
use Webaccess\GatewayTests\Repositories\InMemoryTicketRepository;

class CreateTicketInteractorTest extends PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        $this->repository = new InMemoryTicketRepository();
        Context::set('translator', new DummyTranslator());
        Context::set('event_dispatcher', Mockery::spy("EventDispatcherInterface"));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTicketWithoutTitle()
    {
        $this->response = (new CreateTicketInteractor($this->repository))->execute(new CreateTicketRequest([
            'title' => '',
        ]));
    }

    public function testCreateTicket()
    {
        $projectID = $this->createSampleProject();
        $this->response = (new CreateTicketInteractor($this->repository))->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $projectID,
            'statusID' => 2,
            'dueDate' => new \DateTime('2016-02-30')
        ]));
        $this->assertInstanceOf(CreateTicketResponse::class, $this->response);

        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_TICKET,
            Mockery::type(CreateTicketEvent::class)
        );
    }

    private function createSampleProject()
    {
        $project = new Project();
        $project->name = 'Sample Project';

        return (new InMemoryProjectRepository())->persistProject($project);
    }
}

/*
 *  public $title;
    public $projectID;
    public $typeID;
    public $description;
    public $statusID;
    public $authorUserID;
    public $allocatedUserID;
    public $priority;
    public $dueDate;
    public $comments;
 */