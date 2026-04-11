<?php
declare(strict_types=1);

namespace Studio24\StagingSite;

/**
 * Simple class to manage HTTP headers
 */
class Headers
{

    /**
     * Set default headers
     * @link https://xclacksoverhead.org/home/about
     */
    private array $headers = [
        'X-Robots-Tag' => 'noindex, nofollow',
        'X-Powered-By' => 'studio24/staging-site',
        'X-Clacks-Overhead' => 'GNU Terry Pratchett',
    ];

    /**
     * Whether to replace HTTP header when outputted
     * @var array<string>
     */
    private array $replace = [
        'X-Robots-Tag'
    ];

    /**
     * Set a header
     * @param string $name HTTP header name
     * @param string|null $value HTTP header value, if null removes that header
     * @param bool $replace Whether to replace the HTTP header if it already exists
     */
    public function setHeader(string $name, ?string $value = null, bool $replace = false)
    {
        if (null === $value) {
            unset($this->headers[$name]);
        } else {
            $this->headers[$name] = $value;
            if ($replace) {
                $this->replace[] = $name;
            }
        }
    }

    /**
     * Return HTTP headers as an array (name => value)
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Whether this header should replace previous HTTP headers
     * @param string $name HTTP header name
     * @return bool
     */
    public function replace(string $name): bool
    {
        return in_array($name, $this->replace);
    }

    /**
     * Output HTTP headers via header() function
     */
    public function outputHeaders(): void
    {
        foreach ($this->getHeaders() as $name => $value) {
            header(sprintf('%s: %s', $name, $value), $this->replace($name));
        }
    }
}
