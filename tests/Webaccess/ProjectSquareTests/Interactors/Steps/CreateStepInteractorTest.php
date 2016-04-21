<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Steps\CreateStepEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Interactors\Steps\CreateStepInteractor;
use Webaccess\ProjectSquare\Requests\Steps\CreateStepRequest;
use Webaccess\ProjectSquare\Responses\Steps\CreateStepResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class CreateStepInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateStepInteractor($this->stepRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testCreateStepWithoutPermission()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();

        $this->interactor->execute(new CreateStepRequest([
            'name' => 'Sample step',
            'startTime' => new \DateTime('2016-03-15 10:30:00'),
            'endTime' => new \DateTime('2016-03-15 18:30:00'),
            'projectID' => $project->id,
            'requesterUserID' => $user->id,
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateStepWithInvalidDates()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->projectRepository->addUserToProject($project, $user, null);

        $this->interactor->execute(new CreateStepRequest([
            'name' => 'Sample step',
            'startTime' => null,
            'endTime' => 'invalid date',
            'projectID' => $project->id,
            'requesterUserID' => $user->id,
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateStepWithInvalidProject()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->projectRepository->addUserToProject($project, $user, null);

        $this->interactor->execute(new CreateStepRequest([
            'name' => 'Sample step',
            'startTime' => new \DateTime('2016-03-15 10:30:00'),
            'endTime' => new \DateTime('2016-03-15 18:30:00'),
            'projectID' => null,
            'requesterUserID' => $user->id,
        ]));
    }

    public function testCreateStep()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->projectRepository->addUserToProject($project, $user, null);

        $response = $this->interactor->execute(new CreateStepRequest([
            'name' => 'Sample step',
            'startTime' => new \DateTime('2016-03-15 10:30:00'),
            'endTime' => new \DateTime('2016-03-15 18:30:00'),
            'projectID' => $project->id,
            'requesterUserID' => $user->id,
        ]));

        //Check response
        $this->assertInstanceOf(CreateStepResponse::class, $response);
        $this->assertEquals('Sample step', $response->step->name);

        //Check insertion
        $this->assertCount(1, $this->stepRepository->objects);

        //Check step
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_STEP,
            Mockery::type(CreateStepEvent::class)
        );
    }
}