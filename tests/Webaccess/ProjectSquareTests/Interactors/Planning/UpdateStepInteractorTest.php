<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Planning\UpdateStepEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Interactors\Planning\UpdateStepInteractor;
use Webaccess\ProjectSquare\Requests\Planning\UpdateStepRequest;
use Webaccess\ProjectSquare\Responses\Planning\UpdateStepResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class UpdateStepInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdateStepInteractor($this->stepRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateInvalidStep()
    {
        $this->interactor->execute(new UpdateStepRequest([
            'stepID' => 1,
            'startTime' => new \DateTime('2016-03-16 14:30:00'),
            'requesterUserID' => 2
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $step = $this->createSampleStep($project->id, $user->id);

        $this->interactor->execute(new UpdateStepRequest([
            'stepID' => $step->id,
            'startTime' => new \DateTime('2016-03-16 14:30:00'),
            'requesterUserID' => 2
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateWithInvalidProject()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $step = $this->createSampleStep($project->id, $user->id);

        $this->interactor->execute(new UpdateStepRequest([
            'stepID' => $step->id,
            'projectID' => null,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateStepWithInvalidDates()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->projectRepository->addUserToProject($project, $user, null);
        $step = $this->createSampleStep($project->id, $user->id);

        $this->interactor->execute(new UpdateStepRequest([
            'stepID' => $step->id,
            'name' => 'Sample step',
            'startTime' => null,
            'endTime' => 'invalid date',
            'projectID' => $project->id,
            'requesterUserID' => $user->id,
        ]));
    }

    public function testUpdateStep()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->projectRepository->addUserToProject($project, $user, null);
        $step = $this->createSampleStep($project->id, $user->id);

        $response = $this->interactor->execute(new UpdateStepRequest([
            'stepID' => $step->id,
            'startTime' => new \DateTime('2016-03-16 14:30:00'),
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(UpdateStepResponse::class, $response);

        //Check update
        $this->assertCount(1, $this->stepRepository->objects);
        $this->assertEquals(new \DateTime('2016-03-16 14:30:00'), $response->step->startTime);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_STEP,
            Mockery::type(UpdateStepEvent::class)
        );
    }
}