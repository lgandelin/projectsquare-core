<?php

use Webaccess\ProjectSquare\Library\PhasesAndTasksTextParser;
use Webaccess\ProjectSquareTests\BaseTestCase;

class PhasesAndTasksTextParserTest extends BaseTestCase {

    public function testParse1Phase()
    {
        $phases = PhasesAndTasksTextParser::parse('# Phase 1');

        $expected = [
            ['name' => 'Phase 1']
        ];

        $this->assertEquals($expected, $phases);
    }

    public function testParse2Phases()
    {
        $phases = PhasesAndTasksTextParser::parse('# Phase 1
# Phase 2');

        $expected = [
            ['name' => 'Phase 1'],
            ['name' => 'Phase 2']
        ];

        $this->assertEquals($expected, $phases);
    }

    public function testParse2PhasesWithTasks()
    {
        $phases = PhasesAndTasksTextParser::parse('# Phase 1
Tâche 1

# Phase 2

Tâche 2
Tâche 3');

        $expected = [
            ['name' => 'Phase 1', 'tasks' => [
                ['name' => 'Tâche 1', 'duration' => 0]
            ]],
            ['name' => 'Phase 2', 'tasks' => [
                ['name' => 'Tâche 2', 'duration' => 0],
                ['name' => 'Tâche 3', 'duration' => 0]
            ]]
        ];

        $this->assertEquals($expected, $phases);
    }

    public function testParse2PhasesWithTasksAndDuration()
    {
        $phases = PhasesAndTasksTextParser::parse('# Phase 1
Tâche 1;0.5

# Phase 2

Tâche 2;3
Tâche 3;5.5');

        $expected = [
            ['name' => 'Phase 1', 'tasks' => [
                ['name' => 'Tâche 1', 'duration' => 0.5]
            ]],
            ['name' => 'Phase 2', 'tasks' => [
                ['name' => 'Tâche 2', 'duration' => 3.0],
                ['name' => 'Tâche 3', 'duration' => 5.5]
            ]]
        ];

        $this->assertEquals($expected, $phases);
    }

    public function testParse2PhasesWithDifferentFormat()
    {
        $phases = PhasesAndTasksTextParser::parse('# Webdesign

Création des mockups; 1
Ergonomie; 1
Déclinaisons webdesign; 3.5

# Développement

Installation et configuration du site web; 1
Création de la structure du site et des pages; 0.5
Intégration; 3.5
Tests et livraison; 0.5
');

        $expected = [
            ['name' => 'Webdesign', 'tasks' => [
                ['name' => 'Création des mockups', 'duration' => 1.0],
                ['name' => 'Ergonomie', 'duration' => 1.0],
                ['name' => 'Déclinaisons webdesign', 'duration' => 3.5],
            ]],
            ['name' => 'Développement', 'tasks' => [
                ['name' => 'Installation et configuration du site web', 'duration' => 1.0],
                ['name' => 'Création de la structure du site et des pages', 'duration' => 0.5],
                ['name' => 'Intégration', 'duration' => 3.5],
                ['name' => 'Tests et livraison', 'duration' => 0.5],
            ]]
        ];

        $this->assertEquals($expected, $phases);
    }
}