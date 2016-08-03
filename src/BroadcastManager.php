<?php

namespace EdwinLuijten\Ekko;

use EdwinLuijten\Ekko\Broadcasters\BroadcasterInterface;

class BroadcastManager
{
    /**
     * @var array
     */
    protected $broadcasters = [];

    /**
     * @var BroadcasterInterface
     */
    private $default;

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
        return $this->broadcasters[$broadcaster] = $this->get($broadcaster);
    }

    /**
     * @param string $broadcaster
     * @return BroadcasterInterface
     */
    protected function get($broadcaster)
    {
        if (is_null($broadcaster)) {
            return $this->default;
        }

        if (!isset($this->broadcasters[$broadcaster])) {
            throw new \InvalidArgumentException(sprintf('Broadcaster [%s] is not defined.', $broadcaster));
        }

        return $this->broadcasters[$broadcaster];
    }

    /**
     * @param string $name
     * @param BroadcasterInterface $broadcaster
     */
    public function add($name, BroadcasterInterface $broadcaster)
    {
        $this->broadcasters[$name] = $broadcaster;
    }

    /**
     * @return BroadcasterInterface
     */
    public function getDefaultBroadcaster()
    {
        return $this->default;
    }

    /**
     * @param BroadcasterInterface $broadcaster
     */
    public function setDefaultBroadcaster(BroadcasterInterface $broadcaster)
    {
        $this->default = $broadcaster;
    }

    /**
     * @return array
     */
    public function getBroadcasters()
    {
        return $this->broadcasters;
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->default, $method], $arguments);
    }
}
