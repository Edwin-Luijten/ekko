<?php

namespace EdwinLuijten\Ekko\Broadcast\Broadcasters;

use EdwinLuijten\Ekko\Broadcast\Identity;
use EdwinLuijten\Ekko\Broadcast\StrUtil;
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
     * @throws \Exception
     */
    public function auth(Identity $identity)
    {
        if (StrUtil::startsWith($identity->channel, ['private-', 'presence-']) && empty($identity->identifier)) {
            throw new \Exception('Unauthorized', 403);
        }

        return parent::verifyThatIdentityCanAccessChannel(
            $identity,
            str_replace(['private-', 'presence-'], '', $identity->channel)
        );
    }

    /**
     * @param Identity $identity
     * @param $response
     * @return string signature
     */
    public function validAuthenticationResponse(Identity $identity, $response)
    {
        if (StrUtil::startsWith($identity->channel, 'private')) {
            return $this->pusher->socket_auth($identity->channel, $identity->socketId);
        } else {
            return $this->pusher->presence_auth(
                $identity->channel,
                $identity->socketId,
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
