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
        $broadcaster = m::mock(PusherBroadcaster::class);

        $this->assertInstanceOf(BroadcasterInterface::class, $broadcaster);
    }

    public function testThatLogBroadcasterImplementsInterface()
    {
        $broadcaster = m::mock(LogBroadcaster::class);

        $this->assertInstanceOf(BroadcasterInterface::class, $broadcaster);
    }

    public function testThatRedisBroadcasterImplementsInterface()
    {
        $broadcaster = m::mock(RedisBroadcaster::class);

        $this->assertInstanceOf(BroadcasterInterface::class, $broadcaster);
    }

    public function testBroadcastManagerWithDefaultBroadcaster()
    {
        $broadcaster = new BroadcastManager($this->config['broadcasters']);
        $broadcaster->setDefaultBroadcaster($this->config['default']);
        $connection = $broadcaster->connection();

        $this->assertInstanceOf(LogBroadcaster::class, $connection);
    }

    public function testBroadcastManagerWithSpecifiedBroadcaster()
    {
        $broadcaster = new BroadcastManager($this->config['broadcasters']);
        $connection  = $broadcaster->connection('logger');

        $this->assertInstanceOf(LogBroadcaster::class, $connection);
    }

    /**
     * @expectedException \InvalidArgumentException
     **/
    public function testBroadcastManagerWithEmptyConfig()
    {
        $broadcaster = new BroadcastManager([]);
        $broadcaster->connection();
    }

    /**
     * @expectedException \InvalidArgumentException
     **/
    public function testBroadcastManagerWithUndefinedBroadcaster()
    {
        $broadcaster = new BroadcastManager($this->config['broadcasters']);
        $broadcaster->connection('undefined');
    }

    /**
     * @expectedException \InvalidArgumentException
     **/
    public function testBroadcastManagerWithUnsupportedBroadcaster()
    {
        $broadcaster = new BroadcastManager([
            'mongo' => [
                'driver' => 'mongo',
            ]
        ]);

        $broadcaster->connection('mongo');
    }

    public function testCallMethodOnDefaultDriverTroughBroadcastManager()
    {
        $broadcaster = new BroadcastManager($this->config['broadcasters']);
        $broadcaster->setDefaultBroadcaster('logger');
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
}