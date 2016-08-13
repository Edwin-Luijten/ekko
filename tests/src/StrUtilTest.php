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

    public function testStringLength()
    {
        $result = StrUtil::length('foo');

        $this->assertEquals(3, $result);

        $result = StrUtil::length('åoo');

        $this->assertEquals(3, $result);
    }

    public function testStringStartsWith()
    {
        $result = StrUtil::startsWith('foo-bar', 'foo');

        $this->assertTrue($result);
    }

    public function testStringStartsWithMultiple()
    {
        $result = StrUtil::startsWith('foo-bar', ['bar', 'foo']);

        $this->assertTrue($result);
    }

    public function testStringStartsNotWith()
    {
        $result = StrUtil::startsWith('foo-bar', 'bar');

        $this->assertFalse($result);
    }

    public function testStringStartsNotWithMultiple()
    {
        $result = StrUtil::startsWith('foo-bar', ['bar', 'bar']);

        $this->assertFalse($result);
    }

    public function testStringEndsWith()
    {
        $result = StrUtil::endsWith('foo-bar', 'bar');

        $this->assertTrue($result);
    }

    public function testStringEndsWithMultiple()
    {
        $result = StrUtil::endsWith('foo-bar', ['bar', 'foo']);

        $this->assertTrue($result);
    }

    public function testStringEndsNotWith()
    {
        $result = StrUtil::endsWith('foo-bar', 'foo');

        $this->assertFalse($result);
    }

    public function testStringEndsNotWithMultiple()
    {
        $result = StrUtil::endsWith('foo-bar', ['foo', 'foo']);

        $this->assertFalse($result);
    }
}