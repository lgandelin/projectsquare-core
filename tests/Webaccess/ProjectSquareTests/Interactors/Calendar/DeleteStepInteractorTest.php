<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Step;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Calendar\DeleteStepEvent;
use Webaccess\ProjectSquare\Interactors\Calendar\DeleteStepInteractor;
use Webaccess\ProjectSquare\Requests\Calendar\DeleteStepRequest;
use Webaccess\ProjectSquare\Responses\Calendar\DeleteStepResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class DeleteStepInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DeleteStepInteractor($this->stepRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteNonExistingStep()
    {
        $this->interactor->execute(new DeleteStepRequest([
            'stepID' => 1,
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteStepWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $step = $this->createSampleStep($project->id, $user->id);
        $this->interactor->execute(new DeleteStepRequest([
            'stepID' => $step->id,
            'requesterUserID' => 2
        ]));
    }

    public function testDeleteStep()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $step = $this->createSampleStep($project->id, $user->id);
        $response = $this->interactor->execute(new DeleteStepRequest([
            'stepID' => $step->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(DeleteStepResponse::class, $response);
        $this->assertInstanceOf(Step::class, $response->step);
        $this->assertEquals($step->id, $response->step->id);

        //Check deletion
        $this->assertCount(0, $this->stepRepository->objects);

        //Check step
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::DELETE_STEP,
            Mockery::type(DeleteStepEvent::class)
        );
    }
}