<?php

namespace Raspberry\TodoList;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Raspberry\TodoList\VO\TodoItemVO;

/**
 * @Service(public=false)
 */
class TodoReminder
{

    use EventDispatcherTrait;

    /**
     * @var TodoList
     */
    private $todoList;

    /**
     * @Inject({"@TodoList"})
     * @param TodoList $todoList
     */
    public function __construct(TodoList $todoList)
    {
        $this->todoList = $todoList;
    }

    public function sendNotification()
    {
        $issuesPerState = $this->getGroupedIssues();

        if (empty($issuesPerState)) {
            return;
        }

        $this->doSendNotification($issuesPerState);
    }

    /**
     * @param $issuesPerState
     */
    private function doSendNotification($issuesPerState)
    {
        $text = _('Erinnerung');
        $text .= ': ';

        foreach ($issuesPerState as $state => $issuesPerStatus) {
            $text .= $this->getStateName(count($issuesPerStatus), $state);
            $text .= ': ';

            /** @var TodoItemVO $todo */
            foreach ($issuesPerStatus as $todo) {
                $text .= sprintf('%s: %s. ', $todo->userName, $todo->name);
            }
        }

        $espeakVo = new EspeakVO($text);
        $event    = new EspeakEvent($espeakVo);

        $this->dispatchInBackground($event);
    }

    /**
     * @return array[]
     */
    private function getGroupedIssues()
    {
        $todos = $this->todoList->getList();

        $issuesPerState = [];
        foreach ($todos as $todo) {
            if (TodoItemVO::STATUS_COMPLETED === $todo->status) {
                continue;
            }

            $issuesPerState[$todo->status][] = $todo;
        }

        return $issuesPerState;
    }

    /**
     * @param integer $count
     * @param string $state
     * @return string
     */
    private function getStateName($count, $state)
    {
        switch ($state) {
            case TodoItemVO::STATUS_PROGRESS:
                return sprintf(ngettext('%d Aufgabe in Arbeit', '%d offene Aufgaben in Arbeit', $count), $count);
            case TodoItemVO::STATUS_PENDING:
            default:
                return sprintf(ngettext('%d offene Aufgabe', '%d offene Aufgaben', $count), $count);
        }
    }
}
