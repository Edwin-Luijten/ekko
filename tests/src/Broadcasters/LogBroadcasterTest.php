<?php

namespace EdwinLuijten\Ekko\Broadcast\Tests\Broadcasters;

use EdwinLuijten\Ekko\Broadcast\Broadcasters\BroadcasterInterface;
use EdwinLuijten\Ekko\Broadcast\Broadcasters\LogBroadcaster;
use EdwinLuijten\Ekko\Broadcast\BroadcastManager;
use EdwinLuijten\Ekko\Broadcast\Tests\AbstractTest;


class LogBroadcasterTest extends AbstractTest
{
    public function testLogBroadcaster()
    {
        $broadcaster = new BroadcastManager();
        $broadcaster->setDefaultBroadcaster(new LogBroadcaster($this->getLogger()));
        $connection = $broadcaster->connection();

        $this->assertInstanceOf(BroadcasterInterface::class, $connection);
    }
}