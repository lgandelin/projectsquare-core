<?php

namespace Webaccess\ProjectSquare\Interactors\Phases;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Phase;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Phases\CreatePhaseEvent;
use Webaccess\ProjectSquare\Repositories\PhaseRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Phases\CreatePhaseRequest;
use Webaccess\ProjectSquare\Responses\Phases\CreatePhaseResponse;

class CreatePhaseInteractor
{

    public function __construct(PhaseRepository $phaseRepository, ProjectRepository $projectRepository, UserRepository $userRepository)
    {
        $this->repository = $phaseRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
    }

    public function execute(CreatePhaseRequest $request)
    {
        $this->validateRequest($request);
        $phase = $this->createPhase($request);
        $this->dispatchEvent($phase->id);

        return new CreatePhaseResponse([
            'phase' => $phase,
        ]);
    }

    private function validateRequest(CreatePhaseRequest $request)
    {
        if ($request->projectID) {
            $this->validateProject($request->projectID);
        }
        $this->validRequesterPermissions($request);
    }

    private function validateProject($projectID)
    {
        if (!$project = $this->projectRepository->getProject($projectID)) {
            throw new \Exception(Context::get('translator')->translate('projects.project_not_found'));
        }
    }

    private function validRequesterPermissions(CreatePhaseRequest $request)
    {
        if ($user = $this->userRepository->getUser($request->requesterUserID)) {
            if (!$user->isAdministrator) {
                throw new \Exception(Context::get('translator')->translate('projects.user_not_authorized'));
            }
        }

        return false;
    }

    private function createPhase(CreatePhaseRequest $request)
    {
        $phase = new Phase();
        $phase->name = $request->name;
        $phase->projectID = $request->projectID;
        $phase->order = $request->order;
        $phase->dueDate = $request->dueDate;

        return $this->repository->persistPhase($phase);
    }

    private function dispatchEvent($phaseID)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::CREATE_PHASE,
            new CreatePhaseEvent($phaseID)
        );
    }
}