<?php
declare(strict_types=1);

namespace Studio24\StagingSite;

class Environment
{
    private ?string $platform = null;

    /**
     * Array of supported platforms with:
     *
     * environmentVariable - env var where the current environment is set
     * ignore - array of environment names we do not want the staging site password to be used on
     *
     * @var array|array[]
     */
    private array $platforms = [
        'wordpress' => [
            'environmentVariable' => 'WP_ENVIRONMENT_TYPE',
            'ignore' => ['production', 'local']
        ],
        'craftcms' => [
            'environmentVariable' => 'CRAFT_ENVIRONMENT',
            'ignore' => ['production', 'dev']
        ],
        'laravel' => [
            'environmentVariable' => 'APP_ENV',
            'ignore' => ['production', 'local']
        ],
        'symfony' => [
            'environmentVariable' => 'APP_ENV',
            'ignore' => ['prod', 'dev']
        ],
        'generic' => [
            'environmentVariable' => 'ENVIRONMENT',
            'staging' => ['production', 'development']
        ],
    ];

    public function registerPlatform (string $name, string $environmentVariable, array $ignore)
    {
        $this->platforms[$name] = [
            'environmentVariable' => $environmentVariable,
            'ignore' => $ignore
        ];
    }

    public function setPlatform (string $platform)
    {
        if (!array_key_exists($platform, $this->platforms)) {
            throw new Exception(sprintf('Platform %s not supported', $platform));
        }
        $this->platform = $platform;
    }

    public function getPlatform (): ?array
    {
        if (null !== $this->platform) {
            return $this->platforms[$this->platform];
        }
        return null;
    }

    /**
     * Try to work out whether the staging environment is active, based on environment variable or hostname
     *
     * @return bool
     * @throws Exception
     */
    public function isStaging(): bool
    {
        $platform = $this->getPlatform();
        if (null !== $platform) {
            $environmentVariable = $platform['environmentVariable'];
            $ignore = $platform['ignore'];

            // try constant
            if (defined($environmentVariable)) {
                return !in_array(constant($environmentVariable), $ignore);
            }

            // try getenv
            $env = getenv($environmentVariable);
            if ($env !== false) {
                return !in_array($env, $ignore);
            }
        }

        throw new Exception('Cannot determine environment');
    }


}