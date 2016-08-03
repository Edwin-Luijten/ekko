<?php

namespace EdwinLuijten\Ekko\Broadcasters;

use Pusher;

class PusherBroadcaster implements BroadcasterInterface
{
    /**
     * @var Pusher
     */
    private $pusher;

    /**
     * PusherBroadcaster constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->pusher = new \Pusher(
            $config['key'],
            $config['secret'],
            $config['app_id'],
            isset($config['options']) ? $config['options'] : []
        );
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
        $this->pusher->trigger($channels, $event, $payload);
    }

    /**
     * Get the Pusher instance.
     *
     * @return \Pusher
     */
    public function getPusher()
    {
        return $this->pusher;
    }
}
