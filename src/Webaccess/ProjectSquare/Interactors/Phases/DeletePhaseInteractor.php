<?php

namespace Webaccess\ProjectSquare\Interactors\Phases;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Phases\DeletePhaseEvent;
use Webaccess\ProjectSquare\Repositories\PhaseRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Phases\DeletePhaseRequest;
use Webaccess\ProjectSquare\Responses\Phases\DeletePhaseResponse;

class DeletePhaseInteractor
{
    public function __construct(PhaseRepository $phaseRepository, UserRepository $userRepository, TaskRepository $taskRepository)
    {
        $this->repository = $phaseRepository;
        $this->userRepository = $userRepository;
        $this->taskRepository = $taskRepository;
    }

    public function execute(DeletePhaseRequest $request)
    {
        $phase = $this->getPhase($request->phaseID);
        $this->validateRequest($request);
        $this->deleteTasksByPhase($request->phaseID);
        $this->deletePhase($phase);
        $this->dispatchEvent($phase->id);

        return new DeletePhaseResponse([
            'phaseID' => $request->phaseID,
        ]);
    }

    private function getPhase($phaseID)
    {
        if (!$phase = $this->repository->getPhase($phaseID)) {
            throw new \Exception(Context::get('translator')->translate('phases.phase_not_found'));
        }

        return $phase;
    }

    private function validateRequest(DeletePhaseRequest $request)
    {
        $this->validateRequesterPermissions($request);
    }

    private function validateRequesterPermissions(DeletePhaseRequest $request)
    {
        if ($user = $this->userRepository->getUser($request->requesterUserID)) {
            if (!$user->isAdministrator) {
                throw new \Exception(Context::get('translator')->translate('projects.user_not_authorized'));
            }
        }

        return false;
    }

    private function deletePhase($phase)
    {
        $this->repository->removePhase($phase->id);
    }

    private function dispatchEvent($projectID)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::DELETE_PHASE,
            new DeletePhaseEvent($projectID)
        );
    }

    private function deleteTasksByPhase($phaseID)
    {
        $this->taskRepository->deleteTasksByPhaseID($phaseID);
    }
}