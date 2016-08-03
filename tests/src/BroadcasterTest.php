<?php

namespace EdwinLuijten\Ekko\Test;

use EdwinLuijten\Ekko\Broadcasters\BroadcasterInterface;
use EdwinLuijten\Ekko\Broadcasters\LogBroadcaster;
use EdwinLuijten\Ekko\Broadcasters\PusherBroadcaster;
use EdwinLuijten\Ekko\Broadcasters\RedisBroadcaster;
use EdwinLuijten\Ekko\BroadcastManager;
use Predis\Client;

class BroadcasterTest extends AbstractTest
{

    public function testRedisBroadcaster()
    {
        $broadcaster = new BroadcastManager();
        $broadcaster->add('redis', new RedisBroadcaster(new Client()));
        $connection = $broadcaster->connection('redis');

        $this->assertInstanceOf(BroadcasterInterface::class, $connection);
    }

    public function testRedisBroadcasterBroadcast()
    {
        $broadcaster = new BroadcastManager();
        $broadcaster->setDefaultBroadcaster(new RedisBroadcaster(new Client()));
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
        $broadcaster = new BroadcastManager();
        $broadcaster->add('pusher', new PusherBroadcaster(
            new \Pusher(
                $this->config['broadcasters']['pusher']['key'],
                $this->config['broadcasters']['pusher']['secret'],
                $this->config['broadcasters']['pusher']['app_id'],
                []
            )
        ));

        $connection = $broadcaster->connection('pusher');

        $this->assertInstanceOf(BroadcasterInterface::class, $connection);
    }

    public function testPusherBroadcasterGetClient()
    {
        $broadcaster = new BroadcastManager();
        $broadcaster->setDefaultBroadcaster(
            new PusherBroadcaster(
                new \Pusher(
                    $this->config['broadcasters']['pusher']['key'],
                    $this->config['broadcasters']['pusher']['secret'],
                    $this->config['broadcasters']['pusher']['app_id'],
                    []
                )
            )
        );
        $client = $broadcaster->getPusher();

        $this->assertInstanceOf(\Pusher::class, $client);
    }

    public function testPusherBroadcasterBroadcast()
    {
        $broadcaster = new BroadcastManager();
        $broadcaster->setDefaultBroadcaster(
            new PusherBroadcaster(
                new \Pusher(
                    $this->config['broadcasters']['pusher']['key'],
                    $this->config['broadcasters']['pusher']['secret'],
                    $this->config['broadcasters']['pusher']['app_id'],
                    []
                )
            )
        );
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
        $broadcaster = new BroadcastManager();
        $broadcaster->setDefaultBroadcaster(new LogBroadcaster($this->getLogger()));
        $connection  = $broadcaster->connection();

        $this->assertInstanceOf(BroadcasterInterface::class, $connection);
    }
}