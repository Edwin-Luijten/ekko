<?php

namespace EdwinLuijten\Ekko\Broadcast;

class Channel
{
    /**
     * @var string
     */
    public $name;

    /**
     * Channel constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
