<?php

namespace Webaccess\ProjectSquare\Interactors\Calendar;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Step;
use Webaccess\ProjectSquare\Events\Calendar\CreateStepEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\StepRepository;
use Webaccess\ProjectSquare\Requests\Calendar\CreateStepRequest;
use Webaccess\ProjectSquare\Responses\Calendar\CreateStepResponse;

class CreateStepInteractor
{
    public function __construct(StepRepository $repository, ProjectRepository $projectRepository)
    {
        $this->repository = $repository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(CreateStepRequest $request)
    {
        $this->validateRequest($request);
        $step = $this->createStep($request);
        $this->dispatchEvent($step);

        return new CreateStepResponse([
            'step' => $step,
        ]);
    }

    private function validateRequest(CreateStepRequest $request)
    {
        $this->validateProject($request);
        $this->validateDates($request);
        $this->validateRequesterPermissions($request);
    }

    private function validateProject(CreateStepRequest $request)
    {
        if (!$project = $this->projectRepository->getProject($request->projectID)) {
            throw new \Exception(Context::get('translator')->translate('projects.project_not_found'));
        }
    }

    private function validateDates(CreateStepRequest $request)
    {
        if (!$request->startTime instanceof \DateTime || !$request->endTime instanceof \DateTime) {
            throw new \Exception(Context::get('translator')->translate('planning.invalid_step_dates'));
        }
    }

    private function validateRequesterPermissions(CreateStepRequest $request)
    {
        if (!$this->isUserAuthorizedToCreateStep($request)) {
            throw new \Exception(Context::get('translator')->translate('planning.step_creation_not_allowed'));
        }
    }

    private function isUserAuthorizedToCreateStep(CreateStepRequest $request)
    {
        $project = $this->projectRepository->getProject($request->projectID);

        return $this->projectRepository->isUserInProject($project, $request->requesterUserID);
    }

    private function createStep(CreateStepRequest $request)
    {
        $step = new Step();
        $step->name = $request->name;
        $step->projectID = $request->projectID;
        $step->startTime = $request->startTime;
        $step->endTime = $request->endTime;
        $step->color = $request->color;

        return $this->repository->persistStep($step);
    }

    private function dispatchEvent(Step $step)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::CREATE_STEP,
            new CreateStepEvent($step)
        );
    }
}
