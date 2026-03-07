<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Central Domains
    |--------------------------------------------------------------------------
    |
    | The domains that are used for the central application.
    |
    */
    'central_domains' => [
        env('CENTRAL_DOMAIN', 'tpst-app.test'),
        'localhost',
        '127.0.0.1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Subdomain Suffix
    |--------------------------------------------------------------------------
    |
    | The suffix added to subdomains when creating new tenants.
    |
    */
    'subdomain_suffix' => env('TENANT_SUBDOMAIN_SUFFIX', '.tpst-app.test'),
];
