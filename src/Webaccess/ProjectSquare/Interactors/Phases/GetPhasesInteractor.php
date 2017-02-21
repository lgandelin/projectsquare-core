<?php

namespace Webaccess\ProjectSquare\Interactors\Phases;

use Webaccess\ProjectSquare\Repositories\PhaseRepository;
use Webaccess\ProjectSquare\Requests\Phases\GetPhasesRequest;

class GetPhasesInteractor
{
    public function __construct(PhaseRepository $phaseRepository)
    {
        $this->repository = $phaseRepository;
    }

    public function execute(GetPhasesRequest $request)
    {
        return $this->repository->getPhases($request->projectID);
    }
}