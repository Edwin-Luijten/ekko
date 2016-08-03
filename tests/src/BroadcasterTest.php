<?php

namespace EdwinLuijten\Ekko\Test;

use EdwinLuijten\Ekko\Broadcasters\BroadcasterInterface;
use EdwinLuijten\Ekko\BroadcastManager;

class BroadcasterTest extends AbstractTest
{

    public function testRedisBroadcaster()
    {
        $broadcaster = new BroadcastManager($this->config['broadcasters']);
        $connection  = $broadcaster->connection('redis');

        $this->assertInstanceOf(BroadcasterInterface::class, $connection);
    }

    public function testRedisBroadcasterBroadcast()
    {
        $broadcaster = new BroadcastManager($this->config['broadcasters']);
        $broadcaster->setDefaultBroadcaster('redis');
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

    public function testPusherBroadcaster()
    {
        $broadcaster = new BroadcastManager($this->config['broadcasters']);
        $connection  = $broadcaster->connection('pusher');

        $this->assertInstanceOf(BroadcasterInterface::class, $connection);
    }

    public function testPusherBroadcasterGetClient()
    {
        $broadcaster = new BroadcastManager($this->config['broadcasters']);
        $connection  = $broadcaster->connection('pusher');
        $client = $connection->getPusher();

        $this->assertInstanceOf(\Pusher::class, $client);
    }

    public function testPusherBroadcasterBroadcast()
    {
        $broadcaster = new BroadcastManager($this->config['broadcasters']);
        $broadcaster->setDefaultBroadcaster('pusher');
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

    public function testLogBroadcaster()
    {
        $broadcaster = new BroadcastManager($this->config['broadcasters']);
        $connection  = $broadcaster->connection('logger');

        $this->assertInstanceOf(BroadcasterInterface::class, $connection);
    }
}