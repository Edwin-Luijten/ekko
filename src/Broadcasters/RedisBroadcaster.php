<?php

namespace EdwinLuijten\Ekko\Broadcasters;

use Predis\Client;

class RedisBroadcaster implements BroadcasterInterface
{
    /**
     * @var Client
     */
    private $redis;

    /**
     * RedisBroadcaster constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->redis = new Client($config['parameters'], $config['options']);
    }

    /**
     * Broadcast the given event.
     *
     * @param  array $channels
     * @param  string $event
     * @param  array $payload
     * @return void
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        $payload = json_encode(['event' => $event, 'data' => $payload]);

        foreach ($channels as $channel) {
            $this->redis->publish($channel, $payload);
        }
    }
}
