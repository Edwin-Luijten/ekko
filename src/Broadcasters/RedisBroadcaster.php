<?php

namespace EdwinLuijten\Ekko\Broadcast\Broadcasters;

use Predis\ClientInterface;

class RedisBroadcaster implements BroadcasterInterface
{
    /**
     * @var ClientInterface
     */
    private $redis;

    /**
     * RedisBroadcaster constructor.
     * @param ClientInterface $redis
     */
    public function __construct(ClientInterface $redis)
    {
        $this->redis = $redis;
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
