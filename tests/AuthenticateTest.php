<?php

declare(strict_types=1);

namespace Studio24\StagingSite\Tests;

use PHPUnit\Framework\TestCase;
use Studio24\StagingSite\Authenticate;

class AuthenticateTest extends TestCase
{
    private string $testPassword = 'correct horse battery staple';
    private string $testHash;

    protected function setUp(): void
    {
        $this->testHash = password_hash($this->testPassword, PASSWORD_DEFAULT);
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit Test Agent';
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_GET = [];
        $_COOKIE = [];
    }

    public function testHashPasswordIsVerifiable(): void
    {
        $auth = new Authenticate();
        $hash = $auth->hashPassword('test');
        $this->assertTrue(password_verify('test', $hash));
    }

    public function testSetCookieLifetimeInDays(): void
    {
        $auth = new Authenticate();
        $auth->setCookieLifetimeInDays(7);
        $prop = new \ReflectionProperty(Authenticate::class, 'cookieLifetime');
        $this->assertSame(604800, $prop->getValue($auth));
    }

    public function testCheckPasswordReturnsTrueForCorrectPassword(): void
    {
        $auth = new Authenticate();
        $auth->setPasswordHash($this->testHash);
        $this->assertTrue($auth->checkPassword($this->testPassword));
    }

    public function testCheckPasswordReturnsFalseForWrongPassword(): void
    {
        $auth = new Authenticate();
        $auth->setPasswordHash($this->testHash);
        $this->assertFalse($auth->checkPassword('wrongpassword'));
    }

    public function testAuthenticateReturnsTrueOnValidPostLogin(): void
    {
        $auth = new Authenticate();
        $auth->setPasswordHash($this->testHash);
        $_POST['staging_site_login'] = '1';
        $_POST['password'] = $this->testPassword;
        $this->assertTrue($auth->authenticate());
    }

    public function testAuthenticateReturnsFalseAndSetsErrorOnBadPassword(): void
    {
        $auth = new Authenticate();
        $auth->setPasswordHash($this->testHash);
        $_POST['staging_site_login'] = '1';
        $_POST['password'] = 'wrongpassword';
        $this->assertFalse($auth->authenticate());
        $this->assertTrue($auth->hasError());
    }

    public function testAuthenticateReturnsFalseOnLogout(): void
    {
        $auth = new Authenticate();
        $auth->setPasswordHash($this->testHash);
        $_POST['staging_site_login'] = '1';
        $_POST['password'] = $this->testPassword;
        $this->assertTrue($auth->authenticate());
        $_GET['staging_site_logout'] = '1';
        $this->assertFalse($auth->authenticate());
    }

    public function testAuthenticateReturnsFalseWithInvalidCookie(): void
    {
        $auth = new Authenticate();
        $auth->setPasswordHash($this->testHash);
        $_COOKIE['staging_site_remember_login'] = 'garbage-value-that-wont-verify';
        $this->assertFalse($auth->authenticate());
    }

    public function testUserAgentInCookie(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit Test Agent';

        // Set cookie
        $identifier = $_SERVER['HTTP_USER_AGENT'] . ':' . md5($this->testHash);
        $_COOKIE['staging_site_remember_login'] = password_hash($identifier, PASSWORD_DEFAULT);

        // Login via cookie
        $auth = new Authenticate();
        $auth->setPasswordHash($this->testHash);
        $this->assertTrue($auth->authenticate());

        // Change user agent
        $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit Test Agent 2';
        $auth = new Authenticate();
        $auth->setPasswordHash($this->testHash);
        $this->assertFalse($auth->authenticate());
    }

    public function testIpAddressInCookie(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'PHPUnit Test Agent';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        // Set cookie
        $identifier = $_SERVER['HTTP_USER_AGENT'] . ':' . md5($this->testHash);
        $_COOKIE['staging_site_remember_login'] = password_hash($identifier, PASSWORD_DEFAULT);

        // Login via cookie
        $auth = new Authenticate();
        $auth->setPasswordHash($this->testHash);
        $this->assertTrue($auth->authenticate());

        // Change user agent
        // We no longer use IP address in the cookie, so changing it should have no effect
        $_SERVER['REMOTE_ADDR'] = '127.10.10.24';
        $auth = new Authenticate();
        $auth->setPasswordHash($this->testHash);
        $this->assertTrue($auth->authenticate());
    }
}
