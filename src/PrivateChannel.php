<?php

namespace EdwinLuijten\Ekko\Broadcast;

class PrivateChannel extends Channel
{
    /**
     * PrivateChannel constructor.
     * @param $name
     */
    public function __construct($name)
    {
        parent::__construct('private-' . $name);
    }
}
