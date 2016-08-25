<?php

use Webaccess\ProjectSquare\Interactors\Projects\CreateProjectInteractor;
use Webaccess\ProjectSquare\Requests\Projects\CreateProjectRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class CreateProjectInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateProjectInteractor($this->projectRepository);
    }

    public function testCreateProject()
    {
        $this->assertCount(0, $this->projectRepository->objects);

        $this->interactor->execute(new CreateProjectRequest([
            'name' => 'Nouveau projet',
            'clientID' => 1,
            'status' => 1,
        ]));

        $this->assertCount(1, $this->projectRepository->objects);
    }
}