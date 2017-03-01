<?php

namespace Webaccess\ProjectSquare\Interactors\Phases;

use Webaccess\ProjectSquare\Repositories\PhaseRepository;
use Webaccess\ProjectSquare\Requests\Phases\GetPhaseRequest;

class GetPhaseInteractor
{
    public function __construct(PhaseRepository $phaseRepository)
    {
        $this->repository = $phaseRepository;
    }

    public function execute(GetPhaseRequest $request)
    {
        return $this->repository->getPhase($request->phaseID);
    }
}