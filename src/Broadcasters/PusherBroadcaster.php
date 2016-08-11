<?php

namespace EdwinLuijten\Ekko\Broadcast\Broadcasters;

use Pusher;

class PusherBroadcaster implements BroadcasterInterface
{
    /**
     * @var Pusher
     */
    private $pusher;

    /**
     * PusherBroadcaster constructor.
     * @param Pusher $pusher
     */
    public function __construct(Pusher $pusher)
    {
        $this->pusher = $pusher;
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
     * @return Pusher
     */
    public function getPusher()
    {
        return $this->pusher;
    }
}
