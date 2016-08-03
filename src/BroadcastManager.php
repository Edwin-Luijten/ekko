<?php

namespace EdwinLuijten\Ekko;

use EdwinLuijten\Ekko\Broadcasters\BroadcasterInterface;
use EdwinLuijten\Ekko\Broadcasters\LogBroadcaster;
use EdwinLuijten\Ekko\Broadcasters\PusherBroadcaster;
use EdwinLuijten\Ekko\Broadcasters\RedisBroadcaster;

class BroadcastManager
{
    /**
     * @var array
     */
    protected $broadcasters = [];

    /**
     * @var array
     */
    private $config;

    /**
     * BroadcastManager constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (empty($config)) {
            throw new \InvalidArgumentException('Config can not be empty.');
        }
        
        $this->config = $config;
    }

    /**
     * @param string $broadcaster
     * @return BroadcasterInterface
     */
    public function connection($broadcaster = null)
    {
        return $this->broadcaster($broadcaster);
    }

    /**
     * @param string $broadcaster
     * @return BroadcasterInterface
     */
    public function broadcaster($broadcaster = null)
    {
        $broadcaster = $broadcaster ?: $this->getDefaultBroadcaster();

        return $this->broadcasters[$broadcaster] = $this->get($broadcaster);
    }

    /**
     * @param string $broadcaster
     * @return BroadcasterInterface
     */
    protected function get($broadcaster)
    {
        return isset($this->broadcasters[$broadcaster]) ? $this->broadcasters[$broadcaster] : $this->resolve(
            $broadcaster
        );
    }

    /**
     * @param string $broadcaster
     * @return BroadcasterInterface
     */
    protected function resolve($broadcaster)
    {
        $config = $this->getConfig($broadcaster);

        if (is_null($config)) {
            throw new \InvalidArgumentException(sprintf('Broadcaster [%s] is not defined.', $broadcaster));
        }

        $method = 'create' . ucfirst($config['driver']) . 'Broadcaster';

        if (method_exists($this, $method)) {
            return $this->{$method}($config);
        } else {
            throw new \InvalidArgumentException(sprintf('Broadcaster [%s] is not supported.', $broadcaster));
        }
    }

    /**
     * @param array $config
     * @return BroadcasterInterface
     */
    protected function createPusherBroadcaster(array $config)
    {
        return new PusherBroadcaster($config);
    }

    /**
     * @param array $config
     * @return BroadcasterInterface
     */
    protected function createRedisBroadcaster(array $config)
    {
        return new RedisBroadcaster($config);
    }

    /**
     * @param array $config
     * @return BroadcasterInterface
     */
    protected function createLogBroadcaster(array $config)
    {
        return new LogBroadcaster($config);
    }

    /**
     * @param string $broadcaster
     * @return array|null
     */
    protected function getConfig($broadcaster)
    {
        return isset($this->config[$broadcaster]) ? $this->config[$broadcaster] : null;
    }

    /**
     * @return mixed
     */
    public function getDefaultBroadcaster()
    {
        return $this->config['default'];
    }

    /**
     * @param $broadcaster
     */
    public function setDefaultBroadcaster($broadcaster)
    {
        $this->config['default'] = $broadcaster;
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->broadcaster(), $method], $arguments);
    }
}
