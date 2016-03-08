<?php

namespace Webaccess\ProjectSquare\Interactors\Messages;

use Webaccess\ProjectSquare\Repositories\ConversationRepository;
use Webaccess\ProjectSquare\Repositories\MessageRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Messages\GetUnreadMessagesCountRequest;
use Webaccess\ProjectSquare\Responses\Messages\GetUnreadMessagesCountResponse;

class GetUnreadMessagesCountInteractor
{
    protected $repository;
    protected $conversationRepository;
    protected $projectRepository;
    protected $userRepository;

    public function __construct(MessageRepository $repository, ConversationRepository $conversationRepository, UserRepository $userRepository, ProjectRepository $projectRepository)
    {
        $this->repository = $repository;
        $this->conversationRepository = $conversationRepository;
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(GetUnreadMessagesCountRequest $request)
    {
        $this->validate($request);

        return new GetUnreadMessagesCountResponse([
            'count' => count($this->userRepository->getUnreadMessages($request->userID))
        ]);
    }

    private function validate(GetUnreadMessagesCountRequest $request)
    {
        $this->validateUser($request);
    }

    private function validateUser(GetUnreadMessagesCountRequest $request)
    {
        if (!$user = $this->userRepository->getUser($request->userID)) {
            throw new \Exception('User not found');
        }
    }
}