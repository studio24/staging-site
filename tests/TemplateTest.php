<?php

declare(strict_types=1);

namespace Studio24\StagingSite\Tests;

use PHPUnit\Framework\TestCase;
use Studio24\StagingSite\Exception\StagingSiteException;
use Studio24\StagingSite\Template;

class TemplateTest extends TestCase
{
    private Template $template;

    protected function setUp(): void
    {
        $this->template = new Template();
    }

    public function testSetPlaceholderStoresValue(): void
    {
        $this->template->setPlaceholder('foo', 'bar');
        $this->assertSame(['foo' => 'bar'], $this->template->getPlaceholders());
    }

    public function testSetPlaceholderNullBecomesEmptyString(): void
    {
        $this->template->setPlaceholder('foo', null);
        $this->assertSame(['foo' => ''], $this->template->getPlaceholders());
    }

    public function testSetPlaceholders(): void
    {
        $this->template->setPlaceholders(['foo' => 'bar', 'baz' => 'qux']);
        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], $this->template->getPlaceholders());
    }

    public function testGetPlaceholders(): void
    {
        $this->template->setPlaceholder('key', 'value');
        $placeholders = $this->template->getPlaceholders();
        $this->assertArrayHasKey('key', $placeholders);
    }

    public function testParseTemplateReplacesPlaceholders(): void
    {
        $html = $this->template->parseTemplate('error.html', [
            'error_message_title' => 'Test error',
            'error_message' => 'Error details',
        ]);
        $this->assertStringContainsString('Test error', $html);
        $this->assertStringContainsString('Error details', $html);
    }

    public function testParseTemplateThrowsForMissingFile(): void
    {
        $this->expectException(StagingSiteException::class);
        $this->template->parseTemplate('does-not-exist.html');
    }
}
