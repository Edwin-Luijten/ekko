<?php

namespace EdwinLuijten\Ekko\Broadcast\Tests;

use EdwinLuijten\Ekko\Broadcast\StrUtil;

class StrUtilTest extends AbstractTest
{
    public function testStringContains()
    {
        $result = StrUtil::contains('string', 'str');

        $this->assertTrue($result);
    }

    public function testStringNotContains()
    {
        $result = StrUtil::contains('string', 'foo');

        $this->assertFalse($result);
    }

    public function testStringEquals()
    {
        $result = StrUtil::is('A', 'A');

        $this->assertTrue($result);
    }

    public function testStringNotEquals()
    {
        $result = StrUtil::is('A', 'a');

        $this->assertFalse($result);
    }

    public function testStringStartsWith()
    {

    }
}