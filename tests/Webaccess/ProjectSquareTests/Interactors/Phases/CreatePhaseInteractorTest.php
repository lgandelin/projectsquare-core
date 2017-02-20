<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Phase;
use Webaccess\ProjectSquare\Events\Phases\CreatePhaseEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Interactors\Phases\CreatePhaseInteractor;
use Webaccess\ProjectSquare\Requests\Phases\CreatePhaseRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class CreatePhaseInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreatePhaseInteractor($this->phaseRepository, $this->projectRepository, $this->userRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testCreatePhaseWithNonExistingProject()
    {
        $user = $this->createSampleUser(true);
        $this->interactor->execute(new CreatePhaseRequest([
            'name' => 'Nouvelle phase',
            'projectID' => 1,
            'requesterUserID' => $user->id,
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateProjectWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->interactor->execute(new CreatePhaseRequest([
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testCreatePhase()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser(true);

        $response = $this->interactor->execute(new CreatePhaseRequest([
            'projectID' => $project->id,
            'name' => 'Nouvelle phase',
            'order' => 1,
            'dueDate' => new DateTime('2029-01-01'),
            'requesterUserID' => $user->id
        ]));

        $this->assertCount(1, $this->phaseRepository->objects);
        $this->assertInstanceOf(Phase::class, $response->phase);
        $this->assertEquals('Nouvelle phase', $response->phase->name);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_PHASE,
            Mockery::type(CreatePhaseEvent::class)
        );
    }

}