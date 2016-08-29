<?php

use Webaccess\ProjectSquare\Entities\Project;
use Webaccess\ProjectSquare\Interactors\Projects\DeleteProjectInteractor;
use Webaccess\ProjectSquare\Requests\Projects\DeleteProjectRequest;
use Webaccess\ProjectSquare\Responses\Projects\DeleteProjectResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class DeleteProjectInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DeleteProjectInteractor($this->projectRepository);
    }

    public function testDeleteProject()
    {
        $project = $this->createSampleProject();
        $response = $this->interactor->execute(new DeleteProjectRequest([
            'projectID' => $project->id,
            //'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(DeleteProjectResponse::class, $response);
        $this->assertInstanceOf(Project::class, $response->project);
        $this->assertEquals($project->id, $response->project->id);

        //Check deletion
        $this->assertCount(0, $this->projectRepository->objects);
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteNonExistingProject()
    {
        $this->interactor->execute(new DeleteProjectRequest([
            'projectID' => 2,
            //'requesterUserID' => $user->id
        ]));
    }
}