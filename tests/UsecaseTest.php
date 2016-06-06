<?php


namespace Extasy\Usecase\tests;

use Extasy\Usecase\Usecase;
use PHPUnit_Framework_TestCase;

class UsecaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SampleUsecase
     */
    protected $usecase;

    public function setUp()
    {
        parent::setUp();
        $this->usecase = new SampleUsecase();
    }

    public function testAfter()
    {
        $increment = 0;
        $calledAction = 0;
        $calledAfter = 0;
        $this->usecase->setAction(function () use (&$increment, &$calledAction) {
            $increment++;
            $calledAction = $increment;
        });
        $this->usecase->after(function () use (&$increment, &$calledAfter) {
            $increment++;
            $calledAfter = $increment;
        });

        $this->usecase->execute();
        $this->assertEquals(1, $calledAction);
        $this->assertEquals(2, $calledAfter);
    }

    public function testBefore()
    {
        $increment = 0;
        $calledAction = 0;
        $calledBefore = 0;
        $this->usecase->setAction(function () use (&$increment, &$calledAction) {
            $increment++;
            $calledAction = $increment;
        });
        $this->usecase->before(function () use (&$increment, &$calledBefore) {
            $increment++;
            $calledBefore = $increment;
        });

        $this->usecase->execute();
        $this->assertEquals(2, $calledAction);
        $this->assertEquals(1, $calledBefore);
    }

    public function testAround()
    {
        $this->usecase->around(function ($usecase, $response) {
            return 'Hello ' . $response;
        });
        $this->usecase->setAction(function () {
            return 'world!';
        });
        $response = $this->usecase->execute();

        $this->assertEquals('Hello world!', $response);
    }

    public function testInstead()
    {
        $called = false;
        $fixture = 'Hello world!';
        //
        $this->usecase->setAction(function () {
            $this->fail('Usaces should not be called by default');
        });
        $this->usecase->instead(function () use (&$called, $fixture) {
            $called = true;

            return $fixture;
        });
        //
        $response = $this->usecase->execute();
        //
        $this->assertEquals($fixture, $response);
    }

    public function testAdvicesQueue()
    {
        $this->usecase->instead(function ($usecase, $response) {
            return $response . 'Hello ';
        });
        $this->usecase->instead(function ($usecase, $response) {
            return $response . 'world!';
        });
        $response = $this->usecase->execute();
        $this->assertEquals('Hello world!', $response);
    }


    public function testAdvicesQueueBreakable()
    {
        $this->usecase->after(function ( $usecase) {
            $usecase->stopAdvices();
        });
        $this->usecase->after(function () use (&$called) {
            $this->fail('Could not be called');
        });
        $this->usecase->execute();
    }


    public function testInsteadAndActionCalled()
    {
        $calledInstead = false;
        $calledAction = false;

        $this->usecase->setAction(function () use (&$calledAction) {
            $calledAction = true;
        });
        $this->usecase->instead(function ( $usecase) use (&$calledInstead) {
            $calledInstead = true;
            $usecase->enableDefaultAction(true);
        });
        $this->usecase->execute();
        $this->assertTrue($calledInstead);
        $this->assertTrue($calledAction);
    }
}