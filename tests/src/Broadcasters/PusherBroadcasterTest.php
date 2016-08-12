<?php

namespace EdwinLuijten\Ekko\Broadcast\Tests\Broadcasters;

use EdwinLuijten\Ekko\Broadcast\Broadcasters\BroadcasterInterface;
use EdwinLuijten\Ekko\Broadcast\Broadcasters\PusherBroadcaster;
use EdwinLuijten\Ekko\Broadcast\BroadcastManager;
use EdwinLuijten\Ekko\Broadcast\Tests\AbstractTest;
use EdwinLuijten\Ekko\Broadcast\Tests\Stubs;

class PusherBroadcasterTest extends AbstractTest
{
    private function getPusherBroadcaster()
    {
        return new PusherBroadcaster(
            new \Pusher(
                $this->config['broadcasters']['pusher']['key'],
                $this->config['broadcasters']['pusher']['secret'],
                $this->config['broadcasters']['pusher']['app_id'],
                []
            )
        );
    }

    private function getBroadcaster()
    {
        $broadcaster = new BroadcastManager();

        return $broadcaster;
    }

    public function testPusherBroadcaster()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->add('pusher', $this->getPusherBroadcaster());

        $connection = $broadcaster->connection('pusher');

        $this->assertInstanceOf(BroadcasterInterface::class, $connection);
    }

    public function testPusherBroadcasterGetClient()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

        $client = $broadcaster->getPusher();

        $this->assertInstanceOf(\Pusher::class, $client);
    }

    public function testPusherBroadcasterBroadcast()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

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

    public function testPusherNoAuthorizationParametersWithWildcard()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

        $broadcaster->channel('chat.*', function () {
            return true;
        });

        $response = json_decode($broadcaster->auth(Stubs::getIdentity(1, 'private', 'chat.')), true);

        $this->assertArrayHasKey('auth', $response);
        $this->assertNotEmpty($response['auth']);
    }

    public function testPusherNoAuthorizationParametersWithoutWildcard()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

        $broadcaster->channel('chat.1', function () {
            return true;
        });

        $response = json_decode($broadcaster->auth(Stubs::getIdentity(1, 'private', 'chat.1')), true);

        $this->assertArrayHasKey('auth', $response);
        $this->assertNotEmpty($response['auth']);
    }

    public function testPusherAuthenticateSuccess()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

        $broadcaster->channel('orders.*', function () {
            return true;
        });

        $response = json_decode($broadcaster->auth(Stubs::getIdentity(), 'private', 'orders.1'), true);
        $this->assertArrayHasKey('auth', $response);
        $this->assertNotEmpty($response['auth']);
    }

    /**
     * @expectedException \Exception
     */
    public function testPusherAuthenticateErrorOnCorrectChannel()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

        $broadcaster->channel('orders.*', function ($identity, $invoiceId) {
            return in_array($invoiceId, $identity->orders);
        });

        $response = json_decode($broadcaster->auth(Stubs::getIdentity(1, 'private', 9)), true);
        $this->assertArrayHasKey('auth', $response);
        $this->assertNotEmpty($response['auth']);
    }

    /**
     * @expectedException \Exception
     */
    public function testPusherAuthenticateErrorOnEmptyIdentifier()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

        $broadcaster->channel('orders.*', function ($identity, $invoiceId) {
            return in_array($invoiceId, $identity->orders);
        });

        $response = json_decode($broadcaster->auth(Stubs::getIdentity(null, 'private', 9)), true);
        $this->assertArrayHasKey('auth', $response);
        $this->assertNotEmpty($response['auth']);
    }

    public function testPusherTestThatIdentityCanJoinRoom()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

        $broadcaster->channel('chat.*', function ($identity, $roomId) {
            return [
                'id' => $identity->user->id, 'name' => $identity->user->name
            ];
        });

        $response = json_decode($broadcaster->auth(Stubs::getIdentity(1, 'presence', 'chat.1')), true);

        $this->assertArrayHasKey('auth', $response);
        $this->assertNotEmpty($response['auth']);

        $this->assertArrayHasKey('channel_data', $response);
        $this->assertNotEmpty($response['channel_data']);
    }
}