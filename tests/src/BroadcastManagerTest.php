<?php

namespace EdwinLuijten\Ekko\Test;

use EdwinLuijten\Ekko\Broadcasters\BroadcasterInterface;
use EdwinLuijten\Ekko\Broadcasters\LogBroadcaster;
use EdwinLuijten\Ekko\Broadcasters\PusherBroadcaster;
use EdwinLuijten\Ekko\Broadcasters\RedisBroadcaster;
use EdwinLuijten\Ekko\BroadcastManager;
use Mockery as m;

class BroadcastManagerTest extends AbstractTest
{
    public function testThatPusherBroadcasterImplementsInterface()
    {
        $broadcaster = m::mock(PusherBroadcaster::class, new LogBroadcaster($this->getLogger()));

        $this->assertInstanceOf(BroadcasterInterface::class, $broadcaster);
    }

    public function testThatLogBroadcasterImplementsInterface()
    {
        $broadcaster = m::mock(LogBroadcaster::class, new LogBroadcaster($this->getLogger()));

        $this->assertInstanceOf(BroadcasterInterface::class, $broadcaster);
    }

    public function testThatRedisBroadcasterImplementsInterface()
    {
        $broadcaster = m::mock(RedisBroadcaster::class, new LogBroadcaster($this->getLogger()));

        $this->assertInstanceOf(BroadcasterInterface::class, $broadcaster);
    }

    public function testBroadcastManagerWithDefaultBroadcaster()
    {
        $broadcaster = new BroadcastManager();
        $broadcaster->setDefaultBroadcaster(new LogBroadcaster($this->getLogger()));
        $connection = $broadcaster->connection();

        $this->assertInstanceOf(LogBroadcaster::class, $connection);
    }

    public function testBroadcastManagerWithSpecifiedBroadcaster()
    {
        $broadcaster = new BroadcastManager();
        $broadcaster->add('logger', new LogBroadcaster($this->getLogger()));
        $connection  = $broadcaster->connection('logger');

        $this->assertInstanceOf(LogBroadcaster::class, $connection);
    }

    /**
     * @expectedException \InvalidArgumentException
     **/
    public function testBroadcastManagerWithUndefinedBroadcaster()
    {
        $broadcaster = new BroadcastManager();
        $broadcaster->add('logger', new LogBroadcaster($this->getLogger()));
        $broadcaster->connection('undefined');
    }

    public function testCallMethodOnDefaultDriverTroughBroadcastManager()
    {
        $broadcaster = new BroadcastManager();
        $broadcaster->setDefaultBroadcaster(new LogBroadcaster($this->getLogger()));
        $broadcaster->broadcast(
            [
                'channel_1',
                'channel_2',
            ],
            'UnitTest',
            [
                'some' => 'payload',
            ]);
    }

    public function testGetDefaultBroadcaster()
    {
        $broadcaster = new BroadcastManager();
        $broadcaster->setDefaultBroadcaster(new LogBroadcaster($this->getLogger()));
        $default = $broadcaster->getDefaultBroadcaster();

        $this->assertInstanceOf(LogBroadcaster::class, $default);
    }

    /**
     * @expectedException \InvalidArgumentException
     **/
    public function testInvalidDefaultBroadcaster()
    {
        $broadcaster = new BroadcastManager();
        $broadcaster->getDefaultBroadcaster();
    }

    public function testGetAllBroadcasters()
    {
        $broadcaster = new BroadcastManager();
        $broadcaster->add('logger', new LogBroadcaster($this->getLogger()));
        $broadcasters = $broadcaster->getBroadcasters();

        $this->assertArrayHasKey('logger', $broadcasters);
    }
}