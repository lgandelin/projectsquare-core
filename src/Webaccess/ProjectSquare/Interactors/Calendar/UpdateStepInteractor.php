<?php

namespace Webaccess\ProjectSquare\Interactors\Calendar;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Step;
use Webaccess\ProjectSquare\Events\Calendar\UpdateStepEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\StepRepository;
use Webaccess\ProjectSquare\Requests\Calendar\UpdateStepRequest;
use Webaccess\ProjectSquare\Responses\Calendar\UpdateStepResponse;

class UpdateStepInteractor
{
    public function __construct(StepRepository $repository, ProjectRepository $projectRepository)
    {
        $this->repository = $repository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(UpdateStepRequest $request)
    {
        $step = $this->getStep($request->stepID);
        $this->validateRequest($request);
        $this->updateStep($step, $request);
        $this->dispatchEvent($step);

        return new UpdateStepResponse([
            'step' => $step,
        ]);
    }

    private function getStep($stepID)
    {
        if (!$step = $this->repository->getStep($stepID)) {
            throw new \Exception(Context::get('translator')->translate('Calendar.step_not_found'));
        }

        return $step;
    }

    private function validateRequest(UpdateStepRequest $request)
    {
        $this->validateRequesterPermissions($request);
        $this->validateDates($request);
        $this->validateProject($request);
    }

    private function validateRequesterPermissions(UpdateStepRequest $request)
    {
        if (!$this->isUserAuthorizedToUpdateStep($request)) {
            throw new \Exception(Context::get('translator')->translate('planning.step_update_not_allowed'));
        }
    }

    private function isUserAuthorizedToUpdateStep(UpdateStepRequest $request)
    {
        $project = $this->projectRepository->getProject($request->projectID);

        return $this->projectRepository->isUserInProject($project, $request->requesterUserID);
    }

    private function validateDates(UpdateStepRequest $request)
    {
        if (($request->startTime && !$request->startTime instanceof \DateTime) || ($request->endTime && !$request->endTime instanceof \DateTime)) {
            throw new \Exception(Context::get('translator')->translate('planning.invalid_step_dates'));
        }
    }

    private function validateProject(UpdateStepRequest $request)
    {
        if (!$project = $this->projectRepository->getProject($request->projectID)) {
            throw new \Exception(Context::get('translator')->translate('projects.project_not_found'));
        }
    }

    private function dispatchEvent(Step $step)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::UPDATE_STEP,
            new UpdateStepEvent($step)
        );
    }

    private function updateStep(Step $step, UpdateStepRequest $request)
    {
        if ($request->name) {
            $step->name = $request->name;
        }
        if ($request->startTime) {
            $step->startTime = $request->startTime;
        }
        if ($request->endTime) {
            $step->endTime = $request->endTime;
        }
        if ($request->projectID) {
            $step->projectID = $request->projectID;
        }
        if ($request->color) {
            $step->color = $request->color;
        }

        $this->repository->persistStep($step);
    }
}
