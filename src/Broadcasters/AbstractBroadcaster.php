<?php

namespace EdwinLuijten\Ekko\Broadcast\Broadcasters;

use EdwinLuijten\Ekko\Broadcast\Identity;
use EdwinLuijten\Ekko\Broadcast\StrUtil;

abstract class AbstractBroadcaster
{
    /**
     * @var array
     */
    protected $channels = [];

    /**
     * @param string $channel
     * @param callable $callback
     * @return $this
     */
    public function channel($channel, callable $callback)
    {
        $this->channels[$channel] = $callback;

        return $this;
    }

    /**
     * @param Identity $identity
     * @param string $channel
     * @return mixed
     * @throws \Exception
     */
    protected function verifyThatIdentityCanAccessChannel(Identity $identity, $channel)
    {
        foreach ($this->channels as $key => $callback) {
            if (!StrUtil::is($key, $channel)) {
                continue;
            }

            $parameters = $this->getAuthenticationParameters($key, $channel);

            if ($response = $callback($identity, ...$parameters)) {
                return $this->validAuthenticationResponse($identity, $response);
            }
        }

        throw new \Exception('Unauthorized', 403);
    }

    /**
     * @param string $key
     * @param string $channel
     * @return array
     */
    protected function getAuthenticationParameters($key, $channel)
    {

        if (!StrUtil::contains($key, '*')) {
            return [];
        }

        $pattern = str_replace('\*', '([^\.]+)', preg_quote($key));

        if (preg_match('/^' . $pattern . '/', $channel, $keys)) {
            array_shift($keys);

            return $keys;
        }

        return [];
    }

    /**
     * @param array $channels
     * @return array
     */
    protected function formatChannels(array $channels)
    {
        return array_map(function ($channel) {
            return (string)$channel;
        }, $channels);
    }
}
