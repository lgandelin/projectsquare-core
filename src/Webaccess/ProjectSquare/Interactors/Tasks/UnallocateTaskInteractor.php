<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Interactors\Planning\DeleteEventInteractor;
use Webaccess\ProjectSquare\Interactors\Planning\GetEventsInteractor;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Planning\DeleteEventRequest;
use Webaccess\ProjectSquare\Requests\Planning\GetEventsRequest;
use Webaccess\ProjectSquare\Requests\Tasks\UnallocateTaskRequest;
use Webaccess\ProjectSquare\Requests\Tasks\UpdateTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\UnallocateTaskResponse;

class UnallocateTaskInteractor
{
    public function __construct(TaskRepository $taskRepository, ProjectRepository $projectRepository, EventRepository $eventRepository, NotificationRepository $notificationRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
        $this->eventRepository = $eventRepository;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @param UnallocateTaskRequest $request
     * @return UnallocateTaskResponse
     */
    public function execute(UnallocateTaskRequest $request)
    {
        $this->validateRequest($request);
        $this->deleteLinkedEvents($request);
        $this->unallocateTask($request);

        return new UnallocateTaskResponse([
            'success' => true,
        ]);
    }

    /**
     * @param UnallocateTaskRequest $request
     * @throws \Exception
     */
    private function validateRequest(UnallocateTaskRequest $request)
    {
        $this->validateTask($request);
    }

    /**
     * @param UnallocateTaskRequest $request
     * @throws \Exception
     */
    private function validateTask(UnallocateTaskRequest $request)
    {
        if (!$task = $this->taskRepository->getTask($request->taskID)) {
            throw new \Exception(Context::get('translator')->translate('tasks.task_not_found'));
        }
    }

    /**
     * @param UnallocateTaskRequest $request
     */
    private function deleteLinkedEvents(UnallocateTaskRequest $request)
    {
        $events = (new GetEventsInteractor($this->eventRepository))->execute(new GetEventsRequest([
            'taskID' => $request->taskID
        ]));

        if (is_array($events) && sizeof($events) > 0) {
            foreach ($events as $event) {
                (new DeleteEventInteractor($this->eventRepository, $this->notificationRepository))->execute(new DeleteEventRequest([
                    'eventID' => $event->id
                ]));
            }
        }
    }

    /**
     * @param UnallocateTaskRequest $request
     */
    private function unallocateTask(UnallocateTaskRequest $request)
    {
        (new UpdateTaskInteractor($this->taskRepository, $this->projectRepository))->execute(new UpdateTaskRequest([
            'taskID' => $request->taskID,
            'requesterUserID' => $request->requesterUserID,
            'allocatedUserID' => null
        ]));
    }
}