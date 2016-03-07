<?php

use Webaccess\ProjectSquare\Interactors\Messages\ViewMessageInteractor;
use Webaccess\ProjectSquare\Requests\Messages\ViewMessageRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class ViewMessageInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new ViewMessageInteractor($this->messageRepository, $this->conversationRepository, $this->userRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testViewMessageWithNonExistingMessage()
    {
        $this->interactor->execute(new ViewMessageRequest([]));
    }

    /**
     * @expectedException Exception
     */
    public function testViewMessageWithNonExistingUser()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $conversation = $this->createSampleConversation($project->id);
        $this->projectRepository->addUserToProject($project, $user, null);
        $message = $this->createSampleMessage($conversation->id, $user->id);
        $this->interactor->execute(new ViewMessageRequest([
            'messageID' => $message->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testViewMessageWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $conversation = $this->createSampleConversation($project->id);
        $message = $this->createSampleMessage($conversation->id, $user->id);
        $this->interactor->execute(new ViewMessageRequest([
            'messageID' => $message->id,
            'requesterUserID' => $user->id
        ]));
    }
}