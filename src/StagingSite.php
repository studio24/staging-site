<?php

declare(strict_types=1);

namespace Studio24\StagingSite;

use Studio24\StagingSite\Exception\StagingSiteException;

/**
 * @todo set password via .env
 * @todo customise login page via placeholders
 */
class StagingSite
{
    /**
     * Authentication object
     * @var Authenticate
     */
    public Authenticate $auth;

    /**
     * Staging headers
     * @var Headers
     */
    public Headers $headers;

    public LoginPage $loginPage;

    private ?bool $staging = null;
    private ?string $environment = null;
    private string $loginPageHtml = '';

    /**
     * Environment variable to read current environment
     * @var string
     */
    public string $environmentVariable = 'ENVIRONMENT';
    /**
     * Environment names that are considered staging
     * Either one name or an array of names
     * @var string|array
     */
    public string|array $stagingEnvironments = 'staging';

    public function __construct()
    {
        $this->auth = new Authenticate();
        $this->headers = new Headers();
        $this->loginPage = new LoginPage($this->auth);
    }

    /**
     * Return possible staging environments as an array
     * @return array
     */
    public function getStagingEnvironments(): array
    {
        if (is_string($this->stagingEnvironments)) {
            return [$this->stagingEnvironments];
        }
        return $this->stagingEnvironments;
    }

    public function setStagingEnvironments(string|array $stagingEnvironment): void
    {
        $this->stagingEnvironments = $stagingEnvironment;
    }

    /**
     * Detect environment and return whether this is staging
     * @return bool
     */
    public function isStaging(): bool
    {
        if (null !== $this->staging) {
            return $this->staging;
        }

        $this->staging = in_array($this->getEnvironment(), $this->getStagingEnvironments());
        return $this->staging;
    }

    /**
     * Set the environment name
     * @param string $environment
     */
    public function setEnvironment(string $environment): void
    {
        $this->environment = $environment;
    }

    /**
     * Return current environment
     * @return string
     */
    public function getEnvironment(): string
    {
        if (null !== $this->environment) {
            return $this->environment;
        }

        // try constant
        if (defined($this->environmentVariable)) {
            $this->environment = constant($this->environmentVariable);
            return $this->environment;
        }

        // try $_ENV
        if (isset($_ENV[$this->environmentVariable])) {
            $this->environment = (string) $_ENV[$this->environmentVariable];
            return $this->environment;
        }

        throw new StagingSiteException(sprintf('Cannot determine current environment (environmentVariable: %s)', $this->environmentVariable));
    }

    /**
     * Check if request is authenticated, if not display login page to user
     *
     * @param bool $displayPageAndExit If true, display login page and exit, if false, return HTML
     * @return bool Whether the user is authenticated
     */
    public function authenticate(bool $displayPageAndExit = true): bool
    {
        // If authenticated, return back to the application
        if ($this->auth->authenticate()) {
            return true;
        }

        // Display login page
        if ($displayPageAndExit) {
            $this->loginPage->displayPageAndExit();
            return false;
        } else {
            $this->loginPageHtml = $this->loginPage->displayPageAndExit(false);
            return false;
        }
    }

    public function getLoginPageHtml(): string
    {
        return $this->loginPageHtml;
    }

    /**
     * Return a single instance of this class
     * @return StagingSite
     */
    public static function getInstance(): StagingSite
    {
        static $instance = null;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Static method to instantiate the class and run authentication
     *
     * @param string $environmentVariable The environment variable to read the current environment from
     * @param string|array $stagingEnvironment The environment(s) that are considered staging
     * @param string|null $passwordHash The hashed password
     * @return StagingSite|null
     */
    public static function run(?string $environmentVariable = null, string|array|null $stagingEnvironment = null, ?string $passwordHash = null): ?StagingSite
    {
        $instance = StagingSite::getInstance();

        // Check environment and exit if not staging
        if (null !== $environmentVariable) {
            $instance->environmentVariable = $environmentVariable;
        }
        if (null !== $stagingEnvironment) {
            $instance->stagingEnvironments = $stagingEnvironment;
        }
        if (!$instance->isStaging()) {
            return null;
        }

        // Continue with staging authentication
        if (null !== $passwordHash) {
            $instance->auth->setPasswordHash($passwordHash);
        }
        $instance->authenticate();

        return $instance;
    }

    /**
     * Output staging site HTTP headers
     */
    public static function headers(): void
    {
        $instance = StagingSite::getInstance();
        if (!$instance->isStaging()) {
            return;
        }

        // Output staging HTTP headers
        $instance->headers->outputHeaders();
    }
}
