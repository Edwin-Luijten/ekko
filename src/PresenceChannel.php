<?php

namespace EdwinLuijten\Ekko\Broadcast;

class PresenceChannel extends Channel
{
    /**
     * PresenceChannel constructor.
     * @param $name
     */
    public function __construct($name)
    {
        parent::__construct('presence-' . $name);
    }
}
