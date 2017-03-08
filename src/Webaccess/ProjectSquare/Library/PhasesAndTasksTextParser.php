<?php

namespace Webaccess\ProjectSquare\Library;

class PhasesAndTasksTextParser
{
    public static function parse($text)
    {
        $phases = [];

        $lines = explode(PHP_EOL, $text);
        if (is_array($lines) && sizeof($lines) > 0) {
            foreach ($lines as $line) {
                if (self::isEmptyLine($line)) {

                } elseif (self::isLineAPhase($line)) {
                    $phases[]= ['name' => self::extractPhaseName($line)];
                } elseif (self::isLineATask($line)) {
                    list($taskName, $taskDuration) = self::extractTaskNameAndDuration($line);
                    $phases[sizeof($phases) - 1]['tasks'][]= ['name' => $taskName, 'duration' => $taskDuration];
                }
            }
        }

        return $phases;
    }

    /**
     * @param $line
     * @return mixed
     */
    private static function extractPhaseName($line)
    {
        preg_match('/^#\s?(.*)/', $line, $matches);
        $phaseName = $matches[1];

        return $phaseName;
    }

    /**
     * @param $line
     * @return int
     */
    private static function isLineAPhase($line)
    {
        return preg_match('/^#\s?(.*)/', $line);
    }

    /**
     * @param $line
     * @return int
     */
    private static function isLineATask($line)
    {
        return preg_match('/([^;]*);?(.*)/', $line);
    }

    /**
     * @param $line
     * @return mixed
     */
    private static function extractTaskNameAndDuration($line)
    {
        preg_match('/([^;]*);?(.*)/', $line, $matches);
        $taskName = $matches[1];
        $taskDuration = (isset($matches[2]) && $matches[2] != "") ? (float) trim($matches[2]) : 0;

        return array($taskName, $taskDuration);
    }

    private static function isEmptyLine($line)
    {
        return $line == "" || preg_match('/^[\s]+$/', $line);
    }
}