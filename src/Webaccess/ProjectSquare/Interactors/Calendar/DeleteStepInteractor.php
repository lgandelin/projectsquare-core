<?php

namespace Webaccess\ProjectSquare\Interactors\Calendar;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Step;
use Webaccess\ProjectSquare\Events\Calendar\DeleteStepEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\StepRepository;
use Webaccess\ProjectSquare\Requests\Calendar\DeleteStepRequest;
use Webaccess\ProjectSquare\Responses\Calendar\DeleteStepResponse;

class DeleteStepInteractor
{
    public function __construct(StepRepository $repository, ProjectRepository $projectRepository)
    {
        $this->repository = $repository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(DeleteStepRequest $request)
    {
        $step = $this->getStep($request->stepID);
        $this->validateRequest($request, $step);
        $this->deleteStep($step);
        $this->dispatchEvent($step);

        return new DeleteStepResponse([
            'step' => $step,
        ]);
    }

    private function validateRequest(DeleteStepRequest $request, Step $step)
    {
        $this->validateRequesterPermissions($request, $step);
    }

    private function validateRequesterPermissions(DeleteStepRequest $request, Step $step)
    {
        if (!$this->isUserAuthorizedToDeleteStep($request->requesterUserID, $step)) {
            throw new \Exception(Context::get('translator')->translate('users.step_deletion_not_allowed'));
        }
    }

    private function isUserAuthorizedToDeleteStep($userID, Step $step)
    {
        return $this->projectRepository->isUserInProject($step->projectID, $userID);
    }

    private function getStep($stepID)
    {
        if (!$step = $this->repository->getStep($stepID)) {
            throw new \Exception(Context::get('translator')->translate('Calendar.step_not_found'));
        }

        return $step;
    }

    private function deleteStep($step)
    {
        $this->repository->removeStep($step->id);
    }

    private function dispatchEvent($event)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::DELETE_STEP,
            new DeleteStepEvent($event)
        );
    }
}
