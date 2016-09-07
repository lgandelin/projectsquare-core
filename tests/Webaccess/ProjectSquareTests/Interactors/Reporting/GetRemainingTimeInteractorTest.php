<?php


use Webaccess\ProjectSquare\Interactors\Reporting\GetRemainingTimeInteractor;
use Webaccess\ProjectSquare\Responses\Reporting\GetRemainingTimeResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetRemainingTimeInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetRemainingTimeInteractor();
    }

    public function testGetRemainingTime()
    {
        $estimatedTime = new \StdClass();
        $estimatedTime->days = 10;
        $estimatedTime->hours = 0;

        $spentTime = new \StdClass();
        $spentTime->days = 8;
        $spentTime->hours = 0;

        $this->assertEquals(new GetRemainingTimeResponse(['days' => 2, 'hours' => 0]), $this->interactor->getRemainingTime($estimatedTime, $spentTime));
    }

    public function testGetRemainingTime2()
    {
        $estimatedTime = new \StdClass();
        $estimatedTime->days = 10;
        $estimatedTime->hours = 8;

        $spentTime = new \StdClass();
        $spentTime->days = 8;
        $spentTime->hours = 6;

        $this->assertEquals(new GetRemainingTimeResponse(['days' => 2, 'hours' => 2]), $this->interactor->getRemainingTime($estimatedTime, $spentTime));
    }

    public function testGetRemainingTime3()
    {
        $estimatedTime = new \StdClass();
        $estimatedTime->days = 10;
        $estimatedTime->hours = 3;

        $spentTime = new \StdClass();
        $spentTime->days = 8;
        $spentTime->hours = 5;

        $this->assertEquals(new GetRemainingTimeResponse(['days' => 1, 'hours' => 5]), $this->interactor->getRemainingTime($estimatedTime, $spentTime));
    }

    public function testGetRemainingTime4()
    {
        $estimatedTime = new \StdClass();
        $estimatedTime->days = 0;
        $estimatedTime->hours = 2;

        $spentTime = new \StdClass();
        $spentTime->days = 0;
        $spentTime->hours = 4;

        $this->assertEquals(new GetRemainingTimeResponse(['days' => 0, 'hours' => 0]), $this->interactor->getRemainingTime($estimatedTime, $spentTime));
    }

    public function testGetRemainingTime5()
    {
        $estimatedTime = new \StdClass();
        $estimatedTime->days = 7;
        $estimatedTime->hours = 0;

        $spentTime = new \StdClass();
        $spentTime->days = 7;
        $spentTime->hours = 2;

        $this->assertEquals(new GetRemainingTimeResponse(['days' => 0, 'hours' => 0]), $this->interactor->getRemainingTime($estimatedTime, $spentTime));
    }
}