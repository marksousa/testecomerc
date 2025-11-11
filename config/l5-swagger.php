<?php

return [

    'default' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Documentations
    |--------------------------------------------------------------------------
    */
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'API Docs',
            ],

            'routes' => [
                'api' => 'api/documentation',
                'docs' => 'docs',
                'asset' => 'api-docs/assets',
                'oauth2_callback' => 'api/oauth2-callback',
            ],

            'paths' => [
                'use_absolute_path' => true,
                'swagger_ui_assets_path' => 'vendor/swagger-api/swagger-ui/dist/',
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'format_to_use_for_docs' => 'yaml',
                'annotations' => [
                    base_path('storage/api-docs'),
                ],
            ],

            'source' => [
                'type' => 'yaml',
                'path' => storage_path('api-docs/api-docs.yaml'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'paths' => [
            'docs' => storage_path('api-docs'),
        ],

        'scanOptions' => [
            'exclude' => [base_path()],
            'pattern' => '*.yaml',
        ],

        'proxy' => false,
        'generate_always' => false,
        'generate_yaml_copy' => false,
        'operations_sort' => null,
        'validator_url' => null,
        'additional_config_url' => null,

        'ui' => [
            'display' => [
                'doc_expansion' => 'none',
                'filter' => true,
            ],
            'authorization' => [
                'persist_authorization' => false,
                'oauth2' => [
                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],
    ],
];
