<?php

namespace Webaccess\ProjectSquare\Interactors\Todos;

use Webaccess\ProjectSquare\Repositories\TodoRepository;
use Webaccess\ProjectSquare\Requests\Todos\GetTodosRequest;

class GetTodosInteractor
{
    protected $repository;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(GetTodosRequest $request)
    {
        return $this->repository->getTodos($request->userID);
    }
}
