<?php

namespace EdwinLuijten\Ekko\Broadcast\Broadcasters;

use Pusher;

class PusherBroadcaster extends AbstractBroadcaster implements BroadcasterInterface
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
     * @param Identity $identity
     * @return mixed
     * @throws \HttpException
     */
    public function auth(Identity $identity)
    {
        if (mb_strpos('private-', $identity->channel) || mb_strpos('presence-',
                $identity->channel) && !empty($identity->identifier)
        ) {
            throw new \HttpException('Unauthorized', 403);
        }

        return parent::verifyThatIdentityCanAccessChannel($identity,
            str_replace(['private-', 'presence-'], '', $identity->channel));
    }

    /**
     * @param Identity $identity
     * @param $response
     * @return string
     */
    public function validAuthenticationResponse(Identity $identity, $response)
    {
        if (mb_strpos('private', $identity->channel)) {
            return $this->pusher->socket_auth($identity->channel, $identity->sockerId);
        } else {
            return $this->pusher->presence_auth(
                $identity->channel,
                $identity->sockerId,
                $identity->identifier,
                $response
            );
        }
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
        $socket = isset($payload['socket']) ? $payload['socket'] : null;

        $this->pusher->trigger($this->formatChannels($channels), $event, $payload, $socket);
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
