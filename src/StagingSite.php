<?php
declare(strict_types=1);

namespace Studio24\StagingSite;

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
    private LoginPage $loginPage;
    private ?bool $staging = null;
    private ?string $environment = null;

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
    public string|array $stagingEnvironment = 'staging';

    public function __construct()
    {
        $this->auth = new Authenticate();
    }

    /**
     * Return possible staging environments as an array
     * @return array
     */
    public function getStagingEnvironments(): array
    {
        if (is_string($this->stagingEnvironment)) {
            return [$this->stagingEnvironment];
        }
        return $this->stagingEnvironment;
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

        // try getenv
        $env = getenv($this->environmentVariable);
        if ($env !== false) {
            $this->environment = $env;
            return $this->environment;
        }

        throw new StagingSiteException(sprintf('Cannot determine current environment (environmentVariable: %s)', $this->environmentVariable));
    }

    /**
     * Check if request is authenticated, if not display login page to user
     * @return void
     */
    public function authenticate()
    {
        // If authenticated, return back to the application
        if ($this->auth->authenticate()) {
            return;
        }

        // Display login page
        $loginPage = new LoginPage($this->auth);
        $loginPage->displayPageAndExit();
    }

    /**
     * Return staging banner HTML
     * @param string|null $details Additional details to display in the banner
     * @return string|null
     */
    public function getBannerHtml(?string $details = null): ?string
    {
        if (!$this->isStaging()) {
            return null;
        }
        $template = new Template();
        $template->setPlaceholders([
            'environment' => $this->environment,
            'details' => $details,
        ]);
        return $template->parseTemplate('banner.html');
    }

    /**
     * Return a single instance of this class
     * @return StagingSite
     */
    public static function singleton(): StagingSite
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
     * @return StagingSite
     */
    public static function run(?string $environmentVariable = null, string|array|null $stagingEnvironment = null, ?string $passwordHash = null): StagingSite
    {
        $instance = StagingSite::singleton();
        if (null !== $passwordHash) {
            $instance->auth->setPasswordHash($passwordHash);
        }
        if (null !== $environmentVariable) {
            $instance->environmentVariable = $environmentVariable;
        }
        if (null !== $stagingEnvironment) {
            $instance->stagingEnvironment = $stagingEnvironment;
        }

        if ($instance->isStaging()) {
            $instance->authenticate();
        }
        return $instance;
    }

    /**
     * Output staging site HTTP headers
     */
    public static function headers(): void
    {
        $instance = StagingSite::singleton();
        if (!$instance->isStaging()) {
            return;
        }

        // Output staging HTTP headers
        $headers = new Headers();
        $headers->outputHeaders();
    }

    /**
     * Static method to return the staging banner HTML
     * @param string|null $details Additional details to display in the banner
     * @return string
     */
    public static function banner(?string $details = null): ?string
    {
        $instance = StagingSite::singleton();
        return $instance->getBannerHtml($details);
    }

}
