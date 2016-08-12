<?php

namespace EdwinLuijten\Ekko\Broadcast\Broadcasters;

use EdwinLuijten\Ekko\Broadcast\Identity;
use EdwinLuijten\Ekko\Broadcast\StrUtil;
use Predis\ClientInterface;

class RedisBroadcaster extends AbstractBroadcaster implements BroadcasterInterface
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
     * @param Identity $identity
     * @return mixed
     * @throws \Exception
     */
    public function auth(Identity $identity)
    {
        if (StrUtil::startsWith($identity->channel, ['private-', 'presence-']) && empty($identity->identifier)) {
            throw new \Exception('Unauthorized', 403);
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
        if (is_bool($response)) {
            return json_encode($response);
        }

        return json_encode([
            'channel_data' => [
                'identifier' => $identity->identifier,
                'identity'   => $response,
            ]
        ]);
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
        $socket  = isset($payload['socket']) ? $payload['socket'] : null;
        $payload = json_encode(['event' => $event, 'data' => $payload, 'socket' => $socket]);

        foreach ($this->formatChannels($channels) as $channel) {
            $this->redis->publish($channel, $payload);
        }
    }
}
