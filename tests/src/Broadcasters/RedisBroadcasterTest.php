<?php

namespace EdwinLuijten\Ekko\Broadcast\Tests\Broadcasters;

use EdwinLuijten\Ekko\Broadcast\Broadcasters\BroadcasterInterface;
use EdwinLuijten\Ekko\Broadcast\Broadcasters\RedisBroadcaster;
use EdwinLuijten\Ekko\Broadcast\BroadcastManager;
use EdwinLuijten\Ekko\Broadcast\Tests\AbstractTest;
use EdwinLuijten\Ekko\Broadcast\Tests\Stubs;
use Predis\Client;

class RedisBroadcasterTest extends AbstractTest
{
    private function getPusherBroadcaster()
    {
        return new RedisBroadcaster(new Client());
    }

    private function getBroadcaster()
    {
        $broadcaster = new BroadcastManager();

        return $broadcaster;
    }

    public function testRedisBroadcaster()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->add('redis', $this->getPusherBroadcaster());

        $connection = $broadcaster->connection('redis');

        $this->assertInstanceOf(BroadcasterInterface::class, $connection);
    }

    public function testRedisBroadcasterBroadcast()
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

    public function testRedisNoAuthorizationParametersWithWildcard()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

        $broadcaster->channel('chat.*', function () {
            return true;
        });

        $response = json_decode($broadcaster->auth(Stubs::getIdentity(1, 'private', 'chat.')), true);

        $this->assertTrue($response);
    }

    public function testRedisNoAuthorizationParametersWithoutWildcard()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

        $broadcaster->channel('chat.1', function () {
            return true;
        });

        $response = json_decode($broadcaster->auth(Stubs::getIdentity(1, 'private', 'chat.1')), true);

        $this->assertTrue($response);
    }

    public function testRedisAuthenticateSuccess()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

        $broadcaster->channel('orders.*', function () {
            return true;
        });

        $response = json_decode($broadcaster->auth(Stubs::getIdentity(), 'private', 'orders.1'), true);

        $this->assertTrue($response);
    }

    /**
     * @expectedException \Exception
     */
    public function testRedisAuthenticateErrorOnCorrectChannel()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

        $broadcaster->channel('orders.*', function ($identity, $invoiceId) {
            return in_array($invoiceId, $identity->orders);
        });

        $response = json_decode($broadcaster->auth(Stubs::getIdentity(1, 'private', 9)), true);

        $this->assertTrue($response);
    }

    /**
     * @expectedException \Exception
     */
    public function testRedisAuthenticateErrorOnEmptyIdentifier()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

        $broadcaster->channel('orders.*', function ($identity, $invoiceId) {
            return in_array($invoiceId, $identity->orders);
        });

        $response = json_decode($broadcaster->auth(Stubs::getIdentity(null, 'private', 9)), true);

        $this->assertTrue($response);
    }

    public function testRedisTestThatIdentityCanJoinRoom()
    {
        $broadcaster = $this->getBroadcaster();
        $broadcaster->setDefaultBroadcaster($this->getPusherBroadcaster());

        $broadcaster->channel('chat.*', function ($identity, $roomId) {
            return [
                'id' => $identity->user->id, 'name' => $identity->user->name
            ];
        });

        $response = json_decode($broadcaster->auth(Stubs::getIdentity(1, 'presence', 'chat.1')), true);

        $this->assertArrayHasKey('channel_data', $response);
        $this->assertNotEmpty($response['channel_data']);
    }
}