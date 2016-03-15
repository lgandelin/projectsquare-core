<?php

namespace Webaccess\ProjectSquare\Interactors\Messages;

use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Messages\GetUnreadMessagesRequest;
use Webaccess\ProjectSquare\Responses\Messages\GetUnreadMessagesResponse;

class GetUnreadMessagesInteractor
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(GetUnreadMessagesRequest $request)
    {
        $this->validateRequest($request);

        return new GetUnreadMessagesResponse([
            'messages' => $this->repository->getUnreadMessages($request->userID),
        ]);
    }

    private function validateRequest(GetUnreadMessagesRequest $request)
    {
        $this->validateUser($request);
    }

    private function validateUser(GetUnreadMessagesRequest $request)
    {
        if (!$user = $this->repository->getUser($request->userID)) {
            throw new \Exception('User not found');
        }
    }
}
