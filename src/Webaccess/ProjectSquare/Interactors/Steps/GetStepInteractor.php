<?php

namespace Webaccess\ProjectSquare\Interactors\Steps;

use Webaccess\ProjectSquare\Repositories\StepRepository;
use Webaccess\ProjectSquare\Requests\Steps\GetStepRequest;

class GetStepInteractor
{
    protected $repository;

    public function __construct(StepRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(GetStepRequest $request)
    {
        return $this->repository->getStep($request->stepID);
    }
}
