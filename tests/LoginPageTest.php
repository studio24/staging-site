<?php

declare(strict_types=1);

namespace Studio24\StagingSite\Tests;

use PHPUnit\Framework\TestCase;
use Studio24\StagingSite\Authenticate;
use Studio24\StagingSite\LoginPage;

class LoginPageTest extends TestCase
{
    protected function setUp(): void
    {
        $_SERVER['REQUEST_URI'] = '/';
    }

    public function testDefaultPlaceholdersAreSet(): void
    {
        $auth = new Authenticate();
        $loginPage = new LoginPage($auth);
        $placeholders = $loginPage->getPlaceholders();

        $this->assertArrayHasKey('title', $placeholders);
        $this->assertArrayHasKey('password_field_label', $placeholders);
        $this->assertArrayHasKey('submit_field_label', $placeholders);
        $this->assertArrayHasKey('error_message', $placeholders);
        $this->assertSame('Login to staging website', $placeholders['title']);
        $this->assertSame('Login', $placeholders['submit_field_label']);
    }

    public function testSetPlaceholderOverridesDefault(): void
    {
        $auth = new Authenticate();
        $loginPage = new LoginPage($auth);
        $loginPage->setPlaceholder('title', 'My Staging Site');
        $this->assertSame('My Staging Site', $loginPage->getPlaceholders()['title']);
    }

    public function testDisplayPageAndExitOutputsHtml(): void
    {
        $auth = new Authenticate();
        $loginPage = new LoginPage($auth);

        $html = $loginPage->displayPageAndExit(false);

        $this->assertStringContainsString('<form', $html);
        $this->assertStringContainsString('Login to staging website', $html);

        // Test empty placeholders
        $this->assertStringNotContainsString('{{', $html);
    }

    public function testDisplayPageAndExitWithErrorShowsErrorBlock(): void
    {
        $auth = $this->createStub(Authenticate::class);
        $auth->method('hasError')->willReturn(true);

        $loginPage = new LoginPage($auth);

        $html = $loginPage->displayPageAndExit(false);

        $this->assertStringContainsString('There is a problem', $html);
        $this->assertStringContainsString('The password is incorrect', $html);

        // Test error appears in input field
        $this->assertStringContainsString('<span class="visuallyhidden">Error:</span> Enter a password', $html);
    }

}
