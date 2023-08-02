<?php

return [
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'project_id' => env('GOOGLE_PROJECT_ID'),
        'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
        'token_uri' => 'https://oauth2.googleapis.com/token',
        'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
        'redirect_uris' => ['http://localhost'],
    ],
    'YNAB' => [
        'api_key' => env('YNAB_API_KEY'),
        // The id of the budget. "last-used" can be used to specify the last used budget and "default" can be used if
        // default budget selection is enabled (see: https://api.ynab.com/#oauth-default-budget)
        'default_budget' => 'last-used',
    ],
];
