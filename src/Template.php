<?php

declare(strict_types=1);

namespace Studio24\StagingSite;

use Studio24\StagingSite\Exception\StagingSiteException;

/**
 * Parse a template
 *
 * This only supports passing string placeholders with {{ placeholder }} syntax
 */
class Template
{
    /**
     * Array of default placeholder values
     */
    protected array $placeholders = [];

    /**
     * Set a placeholder
     * @param string $placeholder
     * @param ?string $value
     * @return void
     */
    public function setPlaceholder(string $placeholder, ?string $value): void
    {
        if (null === $value) {
            $value = '';
        }
        $this->placeholders[$placeholder] = $value;
    }

    /**
     * Set multiple placeholders
     * @param array<string, string> $placeholders Array of placeholders (placeholder => value)
     * @return void
     */
    public function setPlaceholders(array $placeholders): void
    {
        foreach ($placeholders as $placeholder => $value) {
            $this->setPlaceholder($placeholder, $value);
        }
    }

    public function getPlaceholders(): array
    {
        return $this->placeholders;
    }

    /**
     * Parse a template
     * @param string $template Template path relative to templates/
     * @param array $placeholders Array of placeholders to replace in the template
     * @return string The parsed template HTML
     * @throws StagingSiteException
     */
    public function parseTemplate(string $template, ?array $placeholders = null): string
    {
        if (null !== $placeholders) {
            $this->setPlaceholders($placeholders);
        }

        $path = __DIR__ . '/../templates/' . $template;
        if (!file_exists($path)) {
            throw new StagingSiteException(sprintf('Template file %s not found', $path));
        }
        $html = file_get_contents(__DIR__ . '/../templates/' . $template);

        foreach ($this->getPlaceholders() as $placeholder => $value) {
            $html = str_replace('{{ ' . $placeholder . ' }}', $value, $html);
        }

        return $html;
    }
}
