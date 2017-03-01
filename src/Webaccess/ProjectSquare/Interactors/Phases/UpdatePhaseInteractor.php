<?php

namespace Webaccess\ProjectSquare\Interactors\Phases;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Phase;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Phases\UpdatePhaseEvent;
use Webaccess\ProjectSquare\Repositories\PhaseRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Phases\UpdatePhaseRequest;
use Webaccess\ProjectSquare\Responses\Phases\UpdatePhaseResponse;

class UpdatePhaseInteractor
{
    public function __construct(PhaseRepository $repository, ProjectRepository $projectRepository, UserRepository $userRepository)
    {
        $this->repository = $repository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
    }

    public function execute(UpdatePhaseRequest $request)
    {
        $phase = $this->getPhase($request->phaseID);
        $this->validateRequest($request);
        $this->updatePhase($phase, $request);
        $this->dispatchEvent($phase->id);

        return new UpdatePhaseResponse([
            'phase' => $phase
        ]);
    }

    private function getPhase($phaseID)
    {
        if (!$phase = $this->repository->getPhase($phaseID)) {
            throw new \Exception(Context::get('translator')->translate('phases.phase_not_found'));
        }

        return $phase;
    }

    private function validateRequest(UpdatePhaseRequest $request)
    {
        $this->validateRequesterPermissions($request);
    }

    private function validateRequesterPermissions(UpdatePhaseRequest $request)
    {
        if ($user = $this->userRepository->getUser($request->requesterUserID)) {
            if (!$user->isAdministrator) {
                throw new \Exception(Context::get('translator')->translate('projects.user_not_authorized'));
            }
        }

        return false;
    }

    private function updatePhase(Phase $phase, UpdatePhaseRequest $request)
    {
        if ($request->name) {
            $phase->name = $request->name;
        }

        if ($request->order) {
            $phase->order = $request->order;
        }

        if ($request->dueDate) {
            $phase->dueDate = $request->dueDate;
        }

        $this->repository->persistPhase($phase);
    }

    private function dispatchEvent($projectID)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::UPDATE_PHASE,
            new UpdatePhaseEvent($projectID)
        );
    }
}