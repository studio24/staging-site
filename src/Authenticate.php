<?php

declare(strict_types=1);

namespace Studio24\StagingSite;

use Studio24\StagingSite\Exception\PasswordNotSetException;
use Studio24\StagingSite\Exception\StagingSiteException;

class Authenticate
{
    private bool $error = false;

    /**
     * @var int Cookie lifetime in seconds, defaults to 7 days
     */
    private int $cookieLifetime = 604800;

    private string $cookieName = 'staging_site_remember_login';

    private ?string $passwordHash = null;

    /**
     * Set password hash for authentication
     * @param string $passwordHash
     */
    public function setPasswordHash(string $passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * Return the password hash, if not set retrieve from env variable
     * @return string|null
     */
    protected function getPasswordHash(): ?string
    {
        if (null !== $this->passwordHash) {
            return $this->passwordHash;
        }

        // Try to get password from environment variable or PHP constant
        $password = $_ENV['STAGING_SITE_PASSWORD'] ?? false;
        if ($password !== false) {
            $this->passwordHash = $password;
            return $this->passwordHash;
        }
        if (defined('STAGING_SITE_PASSWORD')) {
            $this->passwordHash = STAGING_SITE_PASSWORD;
            return $this->passwordHash;
        }

        throw new StagingSiteException('Staging site password not set');
    }

    /**
     * Set the cookie name to use for storing the login cookie
     * @param string $cookieName
     */
    public function setCookieName(string $cookieName)
    {
        $this->cookieName = $cookieName;
    }

    /**
     * Set the cookie lifetime in seconds
     * @param int $seconds
     */
    public function setCookieLifetime(int $seconds)
    {
        $this->cookieLifetime = $seconds;
    }

    /**
     * Set the cookie lifetime in days
     * @param int $days
     */
    public function setCookieLifetimeInDays(int $days)
    {
        $this->cookieLifetime = $days * 86400;
    }

    /**
     * Return GET or POST variables
     */
    public function get(string $variable, string $type = 'get'): ?string
    {
        switch ($type) {
            case 'get':
                if (isset($_GET[$variable])) {
                    return $_GET[$variable];
                }
                break;
            case 'post':
                if (isset($_POST[$variable])) {
                    return $_POST[$variable];
                }
                break;
        }
        return null;
    }

    /**
     * Check authentication for the staging site
     * @param string|null $password Password value from login form
     * @param string|null $stagingSiteLogin staging_site_login value fro login form
     * @param string|null $stagingSiteLogout staging_site_logout value fro login form
     * @return bool
     * @throws StagingSiteException
     */
    public function authenticate(?string $password = null, ?string $stagingSiteLogin = null, ?string $stagingSiteLogout = null): bool
    {
        // Set form values
        if (null === $password) {
            $password = $this->get('password', 'post');
        }
        if (null === $stagingSiteLogin) {
            $stagingSiteLogin = $this->get('staging_site_login', 'post');
        }
        if (null === $stagingSiteLogout) {
            $stagingSiteLogout = $this->get('staging_site_logout');
        }

        // Logout attempt
        if (null !== $this->get('staging_site_logout')) {
            $this->clearLoginCookie();
            return false;
        }

        // Login attempt
        if ((null !== $this->get('staging_site_login', 'post')) && (null !== $this->get('password', 'post'))) {
            if ($this->checkPassword($_POST['password'])) {
                $this->storeLoginInCookie();
                return true;
            } else {
                $this->error = true;
            }
        }

        // Check if user is already logged in
        if ($this->checkLoginCookie()) {
            // Update cookie (don't do this on every request)
            if (mt_rand(1, 10) > 7) {
                $this->storeLoginInCookie();
            }
            return true;
        }

        // User is not logged in
        $this->clearLoginCookie();
        return false;
    }

    /**
     * Generate unique string based on the user agent and hashed password
     * @return string
     */
    protected function getUniqueStringForUser(): string
    {
        $identifier = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $identifier .= $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $identifier .= $_SERVER['REMOTE_ADDR'];
        }
        $identifier .= ':' . $_SERVER['HTTP_USER_AGENT'] . ':' . md5($this->getPasswordHash());
        return $identifier;
    }

    /**
     * Store a unique value in the cookie
     * @return void
     */
    protected function storeLoginInCookie()
    {
        $hash = password_hash($this->getUniqueStringForUser(), PASSWORD_DEFAULT);
        setcookie($this->cookieName, $hash, time() + $this->cookieLifetime, '/', '', true, true);
    }

    protected function clearLoginCookie()
    {
        setcookie($this->cookieName, '', time() - 3600, '/', '', true, true);
    }

    protected function checkLoginCookie(): bool
    {
        if (!isset($_COOKIE[$this->cookieName])) {
            return false;
        }
        $cookieHash = $_COOKIE[$this->cookieName];
        return password_verify($this->getUniqueStringForUser(), $cookieHash);
    }

    protected function clearLoginSession()
    {
        unset($_SESSION[$this->cookieName]);
    }

    public function checkPassword(string $password): bool
    {
        if (empty($this->getPasswordHash())) {
            throw new PasswordNotSetException('Staging site password not set');
        }
        return password_verify($password, $this->getPasswordHash());
    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function hasError(): bool
    {
        return $this->error;
    }
}
