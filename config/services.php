<?php

return [
    'YNAB' => [
        'api_key' => env('YNAB_API_KEY'),
        // The id of the budget. "last-used" can be used to specify the last used budget and "default" can be used if
        // default budget selection is enabled (see: https://api.ynab.com/#oauth-default-budget)
        'default_budget' => 'last-used',
    ],
];
