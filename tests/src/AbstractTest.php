<?php

namespace EdwinLuijten\Ekko\Broadcast\Tests;

use Mockery as m;
use Monolog\Logger;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $config;

    protected function setUp()
    {
        $this->config = Config::get();
    }

    public function tearDown()
    {
        m::close();

        @unlink(reset($this->config['broadcasters']['logger']['handlers']['stream']['arguments']));

        parent::tearDown();
    }

    public function getLogger()
    {
        $logger = new Logger($this->config['broadcasters']['logger']['name']);
        $handler = $this->config['broadcasters']['logger']['handlers']['stream']['class'];
        $logger->pushHandler(new $handler(reset($this->config['broadcasters']['logger']['handlers']['stream']['arguments'])));

        return $logger;
    }
}