<?php

namespace Webaccess\ProjectSquare\Interactors\Steps;

use Webaccess\ProjectSquare\Repositories\StepRepository;
use Webaccess\ProjectSquare\Requests\Steps\GetStepsRequest;

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
