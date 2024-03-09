<?php declare(strict_types=1);

return [
    'default' => env('OPENSEARCH_CONNECTION', 'default'),
    'connections' => [
        'default' => [
            'hosts' => [
                env('OPENSEARCH_HOST', 'https://localhost:9200'),
            ],
            'sslVerification' => env('OPENSEARCH_SSL_VERIFICATION', false),
            'basicAuthentication' => [
                env('OPENSEARCH_USER', 'admin'),
                env('OPENSEARCH_PASSWORD', 'admin')
            ],
            'retries' => (int) env('OPENSEARCH_RETRYS', 2),
        ],
    ],
];
