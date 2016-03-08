<?php

namespace Webaccess\ProjectSquare\Interactors\Messages;

use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Messages\GetUnreadMessagesCountRequest;
use Webaccess\ProjectSquare\Responses\Messages\GetUnreadMessagesCountResponse;

class GetUnreadMessagesCountInteractor
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(GetUnreadMessagesCountRequest $request)
    {
        $this->validate($request);

        return new GetUnreadMessagesCountResponse([
            'count' => count($this->repository->getUnreadMessages($request->userID))
        ]);
    }

    private function validate(GetUnreadMessagesCountRequest $request)
    {
        $this->validateUser($request);
    }

    private function validateUser(GetUnreadMessagesCountRequest $request)
    {
        if (!$user = $this->repository->getUser($request->userID)) {
            throw new \Exception('User not found');
        }
    }
}