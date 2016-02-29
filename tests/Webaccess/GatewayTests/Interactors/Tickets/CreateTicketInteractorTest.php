<?php

use Webaccess\Gateway\Context;
use Webaccess\Gateway\Entities\Project;
use Webaccess\Gateway\Events\Events;
use Webaccess\Gateway\Events\Tickets\CreateTicketEvent;
use Webaccess\Gateway\Interactors\Tickets\CreateTicketInteractor;
use Webaccess\Gateway\Requests\Tickets\CreateTicketRequest;
use Webaccess\Gateway\Responses\Tickets\CreateTicketResponse;
use Webaccess\GatewayTests\BaseTestCase;
use Webaccess\GatewayTests\Repositories\InMemoryProjectRepository;
use Webaccess\GatewayTests\Repositories\InMemoryTicketRepository;

class CreateTicketInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->repository = new InMemoryTicketRepository();
        $this->projectRepository = new InMemoryProjectRepository();
        $this->interactor = new CreateTicketInteractor($this->repository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTicketWithoutTitle()
    {
        $this->interactor->execute(new CreateTicketRequest([
            'title' => '',
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTicketWithNonExistingProject()
    {
        $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => 1
        ]));
    }

    public function testCreateTicket()
    {
        $projectID = $this->createSampleProject();
        $response = $this->interactor->execute(new CreateTicketRequest([
            'title' => 'Sample ticket',
            'projectID' => $projectID,
            'statusID' => 2,
            'dueDate' => new \DateTime('2016-02-30')
        ]));
        $this->assertInstanceOf(CreateTicketResponse::class, $response);

        $this->assertCount(1, $this->repository->objects);

        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_TICKET,
            Mockery::type(CreateTicketEvent::class)
        );
    }

    private function createSampleProject()
    {
        $project = new Project();
        $project->name = 'Sample Project';

        return $this->projectRepository->persistProject($project);
    }
}