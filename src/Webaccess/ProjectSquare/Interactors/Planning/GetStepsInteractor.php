<?php

namespace Webaccess\ProjectSquare\Interactors\Planning;

use Webaccess\ProjectSquare\Repositories\StepRepository;
use Webaccess\ProjectSquare\Requests\Planning\GetStepsRequest;

class GetStepsInteractor
{
    protected $repository;

    public function __construct(StepRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(GetStepsRequest $request)
    {
        return $this->repository->getSteps($request->projectID);
    }
}
