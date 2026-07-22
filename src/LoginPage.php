<?php

declare(strict_types=1);

namespace Studio24\StagingSite;

class LoginPage extends Template
{
    /**
     * Array of default placeholder values
     */
    protected array $placeholders = [
        'title' => 'Login to staging website',
        'title_prefix_on_error' => 'Error: ',
        'password_field_label' => 'Password',
        'show' => 'Show',
        'hide' => 'Hide',
        'submit_field_label' => 'Login',
        'error_message_title' => 'There is a problem',
        'error_message' => 'The password is incorrect',
        'footer' => '',
    ];

    private string $errorMessage = '';

    private Authenticate $auth;

    public function __construct(Authenticate $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Display login page and optionally exit
     *
     * @param bool $exit If true, display login page and exit, if false, return HTML
     */
    public function displayPageAndExit(bool $exit = true): string
    {
        if ($this->auth->hasError()) {
            $this->placeholders['error_message'] = $this->parseTemplate('error.html', $this->placeholders);
        } else {
            $this->placeholders['error_message'] = '';
            $this->placeholders['title_prefix_on_error'] = '';
        }
        $this->placeholders['form_action'] = str_replace('staging_site_logout', '', $_SERVER['REQUEST_URI']);
        $html = $this->parseTemplate('login.html', $this->placeholders);

        // This has been moved to Headers class for now, leaving this here in case it's removed in the future and we re-add this code
        //$headers = new Headers();
        //$headers->setHeader('Cache-Control', 'no-cache, no-store');

        if (!$exit) {
            return $html;
        }

        // 401 Unauthorized
        http_response_code(401);

        // HTTP headers
        $headers = new Headers();
        $headers->outputHeaders();

        // Exit or return HTML
        echo $html;
        exit(0);
    }
}
