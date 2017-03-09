<?php

namespace Webaccess\ProjectSquare\Interactors\Phases;

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Tasks\CreateTaskInteractor;
use Webaccess\ProjectSquare\Library\PhasesAndTasksTextParser;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\PhaseRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Phases\CreatePhaseRequest;
use Webaccess\ProjectSquare\Requests\Phases\CreatePhasesAndTasksFromTextRequest;
use Webaccess\ProjectSquare\Requests\Tasks\CreateTaskRequest;

class CreatePhasesAndTasksFromTextInteractor
{
    public function __construct(PhaseRepository $phaseRepository, TaskRepository $taskRepository, ProjectRepository $projectRepository, UserRepository $userRepository, NotificationRepository $notificationRepository)
    {
        $this->phaseRepository = $phaseRepository;
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute(CreatePhasesAndTasksFromTextRequest $request)
    {
        $data = PhasesAndTasksTextParser::parse($request->text);
        if (is_array($data) && sizeof($data) > 0) {
            foreach ($data as $i => $phase) {
                $response = (new CreatePhaseInteractor($this->phaseRepository, $this->projectRepository, $this->userRepository))->execute(new CreatePhaseRequest([
                    'name' => $phase['name'],
                    'projectID' => $request->projectID,
                    'order' => ($i + 1),
                    'requesterUserID' => $request->requesterUserID
                ]));

                if ($response->phase) {
                    if (isset($phase['tasks']) && is_array($phase['tasks']) && sizeof($phase['tasks']) > 0) {
                        foreach ($phase['tasks'] as $j => $task) {
                            (new CreateTaskInteractor($this->taskRepository, $this->projectRepository, $this->phaseRepository, $this->userRepository, $this->notificationRepository))->execute(new CreateTaskRequest([
                                'title' => $task['name'],
                                'statusID' => Task::TODO,
                                'order' => ($j + 1),
                                'estimatedTimeDays' => (isset($task['duration']) && $task['duration'] != "" && is_numeric($task['duration'])) ? $task['duration'] : 0,
                                'phaseID' => $response->phase->id,
                                'projectID' => $request->projectID,
                                'requesterUserID' => $request->requesterUserID
                            ]));
                        }
                    }
                }
            }
        }
    }
}