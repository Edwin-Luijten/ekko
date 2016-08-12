<?php

namespace EdwinLuijten\Ekko\Broadcast\Tests;

use EdwinLuijten\Ekko\Broadcast\Channel;
use EdwinLuijten\Ekko\Broadcast\Identity;
use EdwinLuijten\Ekko\Broadcast\PresenceChannel;
use EdwinLuijten\Ekko\Broadcast\PrivateChannel;

class Stubs
{
    /**
     * @param int $orderId
     * @param int $userId
     * @return \stdClass
     */
    public static function getOrder($orderId = 1, $userId = 1)
    {
        $order       = new \stdClass();
        $order->id   = $orderId;
        $order->user = $userId;

        return $order;
    }

    /**
     * @param int $identifier
     * @param $channelType
     * @param string $channel
     * @return Identity
     */
    public static function getIdentity($identifier = 1, $channelType = '', $channel = 'orders.1')
    {
        $identity             = new Identity();
        $identity->user       = new \stdClass();
        $identity->user->id   = $identifier;
        $identity->user->name = 'John Do';
        $identity->channel    = self::getChannel($channelType, $channel);
        $identity->identifier = $identifier;
        $identity->orders     = [
            1,
            2,
            3,
        ];

        return $identity;
    }

    /**
     * @param $type
     * @param $channel
     * @return Channel|PresenceChannel|PrivateChannel
     */
    public function getChannel($type, $channel)
    {
        switch ($type) {
            case ('private'):
                return new PrivateChannel($channel);
                break;
            case ('presence'):
                return new PresenceChannel($channel);
                break;
            default:
                return new Channel($channel);
        }
    }
}
