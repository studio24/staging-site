<?php

declare(strict_types=1);

namespace Studio24\StagingSite\Tests;

use PHPUnit\Framework\TestCase;
use Studio24\StagingSite\Headers;

class HeadersTest extends TestCase
{
    private Headers $headers;

    protected function setUp(): void
    {
        $this->headers = new Headers();
    }

    public function testDefaultHeadersAreSet(): void
    {
        $headers = $this->headers->getHeaders();
        $this->assertArrayHasKey('X-Robots-Tag', $headers);
        $this->assertArrayHasKey('X-Clacks-Overhead', $headers);
    }

    public function testSetHeaderAddsHeader(): void
    {
        $this->headers->setHeader('X-Foo', 'bar');
        $headers = $this->headers->getHeaders();
        $this->assertArrayHasKey('X-Foo', $headers);
        $this->assertSame('bar', $headers['X-Foo']);
    }

    public function testSetHeaderNullRemovesHeader(): void
    {
        $this->headers->setHeader('X-Robots-Tag', null);
        $this->assertArrayNotHasKey('X-Robots-Tag', $this->headers->getHeaders());
    }

    public function testSetHeaderWithReplaceFlag(): void
    {
        $this->headers->setHeader('X-Foo', 'bar', true);
        $this->assertTrue($this->headers->replace('X-Foo'));
    }

    public function testDefaultReplaceHeaders(): void
    {
        $this->assertTrue($this->headers->replace('X-Robots-Tag'));
        $this->assertFalse($this->headers->replace('X-Clacks-Overhead'));
    }
}
