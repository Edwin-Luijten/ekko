<?php

namespace EdwinLuijten\Ekko\Test;

use Mockery as m;

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

}