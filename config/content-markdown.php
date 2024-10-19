<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Set the database configuration for your content
    |
    */

    'database' => [
        'connection' => env('CONTENT_DATABASE_CONNECTION', 'sqlite'),
        'content_table_name' => env('CONTENT_DATABASE_TABLE', 'contents'),
        'tags_table_name' => env('TAG_DATABASE_TABLE', 'tags'),
        'content_tags_table_name' => env('CONTENT_TAGS_DATABASE_TABLE', 'content_tag'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Filesystem Configuration
    |--------------------------------------------------------------------------
    |
    | Set the filesystem disk used for your content
    |
    */

    'filesystem' => [
        'disk' => env('CONTENT_FILESYSTEM_DISK', 'content'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Drafts Configuration
    |--------------------------------------------------------------------------
    |
    | Set the prefix for draft files. If any files start with this prefix, they
    | will not be published
    |
    */

    'drafts' => [
        'prefix' => '.',
    ],

    /*
    |--------------------------------------------------------------------------
    | CommonMark
    |--------------------------------------------------------------------------
    |
    | Configure the CommonMark Markdown parser.
    |
    */
    'commonmark' => [
        'config' => [
            'heading_permalink' => [
                'html_class' => 'heading-permalink',
                'id_prefix' => 'content',
                'apply_id_to_heading' => true,
                'heading_class' => '',
                'fragment_prefix' => 'content',
                'insert' => 'after',
                'min_heading_level' => 1,
                'max_heading_level' => 6,
                'title' => 'Permalink',
                'symbol' => '#',
                'aria_hidden' => true,
            ],
        ],
    ],
];
