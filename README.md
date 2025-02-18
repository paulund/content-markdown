# Content Markdown

[![Latest Version on Packagist](https://img.shields.io/packagist/v/paulund/content-markdown.svg?style=flat-square)](https://packagist.org/packages/paulund/content-markdown)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/paulund/content-markdown/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/paulund/content-markdown/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/paulund/content-markdown/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/paulund/content-markdown/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/paulund/content-markdown.svg?style=flat-square)](https://packagist.org/packages/paulund/content-markdown)

---

![Content Markdown Laravel Package](https://raw.githubusercontent.com/paulund/content-markdown/refs/heads/main/assets/images/content-markdown.webp)

## Table of Contents

- [Content Markdown](#content-markdown)
- [Installation](#installation)
- [Setup](#setup)
    - [Add Filesystem Disk](#add-filesystem-disk)
    - [Setup Database Configuration](#setup-database-configuration)
    - [Draft Posts](#draft-posts)
    - [Commonmark Configuration](#commonmark-configuration)
- [Content Properties](#content-properties)
- [Usage](#usage)
    - [Index Command](#index-command)
    - [Get All Content](#get-all-content)
    - [Get All Content In Folder](#get-all-content-in-folder)
    - [Get The Latest 10 Content](#get-the-latest-10-content)
    - [Get Content By Slug](#get-content-by-slug)
    - [Get Content By Tag](#get-content-by-tag)
    - [Content Cache](#content-cache)
- [Testing](#testing)
- [Credits](#credits)
- [License](#license)

This Laravel package with allow you to add a lightweight CMS to your Laravel application. This package will allow you to
create markdown files in your Laravel application and then display the content on the front end of your website.

It works by writing in markdown files and then index the slug into the database. This will allow you to query on this
slug to get the content of the markdown file.

## Installation

Install the package via composer:

```bash
composer require paulund/content-markdown
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Paulund\ContentMarkdown\ContentMarkdownServiceProvider"
php artisan migrate 
```

## Setup

### Add Filesystem Disk

Add the following to your `config/filesystems.php` file:

```php
'disks' => [
    'content' => [
        'driver' => 'local',
        'root' => storage_path('content'),
        'visibility' => 'private',
        'serve' => false,
        'throw' => false,
    ],
],
```

This means you need to create a folder in the storage directory called `content` where you can store your markdown files.

```
├── storage/
│   └── content/
```

You can also add sub categories to the content folder to organise your markdown files.

```
├── storage/
│   └── content/
│       └── blog/
│       └── portfolio/
```

### Setup Database Configuration

Content markdown consists of 3 database tables, content, tags and content_tags. You can create these tables by running the migration:

```bash
php artisan migrate
```

In the config file you can customise the database table names.

```php
'database' => [
    'connection' => env('CONTENT_DATABASE_CONNECTION', 'sqlite'),
    'content_table_name' => env('CONTENT_DATABASE_TABLE', 'contents'),
    'tags_table_name' => env('TAG_DATABASE_TABLE', 'tags'),
    'content_tags_table_name' => env('CONTENT_TAGS_DATABASE_TABLE', 'content_tag'),
],
```

### Draft Posts

There are a few ways that you can define a post as a draft.

- In the frontmatter you can use `published: false`
- Prefixing the file with `.` will make the file a draft

You can customise the prefix in the config file.

```php
'drafts' => [
    'prefix' => '.',
],
```

### Commonmark Configuration

This package uses commonmark to parse the markdown files. You can customise the configuration of commonmark in the config file.

```php
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
```

## Content Properties

- title - The title of the content
- slug - The slug of the content (optional)
- tags - The tags of the content in array format, can also be string format
- published - A boolean to define if the content is published
- createdAt - The date the content was created
- description - Meta description of the post

```markdown
---
title: Content Title
slug: content-slug
tags:
    - blog
    - writing
published: true
createdAt: 2022-09-03 15:00:00
description: This is the meta description of the post
---
```

```markdown
---
title: Content Title
slug: content-slug
tags: blog
published: true
createdAt: 2022-09-03 15:00:00
description: This is the meta description of the post
---
```

## Usage

### Index Command
The index command will take the markdown files in the filesystem disk and index the file in the database.

Whenever a new markdown files is created you need to run this command to index the file.

```bash
php artisan content:index
```

This will also run nightly to ensure the index stays up to date.

### Get All Content
In order to display all of the content you can use the `Content` model to fetch the content.

```php
Content::get();
```

### Get All Content In Folder

You can organise your markdown files into different folders in the filesystem disk. You can query the content by folder.

```php
Content::folder('blog')->get();
```

### Get The Latest 10 Content

You can limit the number of content that is returned by using the `limit` method.

```php
Content::latest()->limit(10)->get();
```

### Get Content By Slug

You can query the content by the slug of the markdown file.

```php
Content::slug('content-slug')->first();
```

### Get Content By Tag

You can query the content by the tag of the markdown file.

```php
Content::hasTag('blog')->get();
```

### Display Content

Once you have found your content model in the database you can fetch the model and use the following properties.

```php
$content = Content::slug('content-slug')->first();

echo $content->title;
echo $content->description;
echo $content->content;
```

### Content Cache

Whenever you make a request the application will need to lookup the database but the slug of the file, then
lookup the markdown file, then parse the markdown to display it. This can add some latency to the request. To
speed up the request you can cache the content using the `CacheResponse` middleware.

```php
Route::get('/content/{slug}', function ($slug) {
    $content = Content::slug($slug)->first();

    return response()->json($content);
})->middleware(\Paulund\ContentMarkdown\Http\Middleware\CacheResponse::class);
```

This will cache the response for 1 hour using the file store.

If you want to customise the cache settings you can change the store and the ttl.

```php
/*
|--------------------------------------------------------------------------
| Cache Configuration
|--------------------------------------------------------------------------
|
| Configure the Cache for your content. This is enabled by middleware
|
*/
'cache' => [
    /**
     * Enable or disable the cache
     */
    'enabled' => env('CONTENT_CACHE_ENABLED', true),
        
    /**
     * Cache store to use
     */
    'store' => env('CONTENT_CACHE_STORE', 'file'),

    /**
     * Seconds to cache the content for 3600 = 1 hour
     */
    'ttl' => env('CONTENT_CACHE_TTL', 3600),
]
```

By default content cache is enabled, but there is a config option to disable it, in your .env file you can add:

```php
CONTENT_CACHE_ENABLED=false
```

## On Deployment

After deployment of your application you can run the index command to index all the markdown files, you will also want to
run a few other commands to optimise the application.

```bash
php artisan optimize
php artisan cache:clear file
php artisan content:index
```

## Testing

```bash
vendor/bin/testbench workbench:install
composer test
```

## Credits

- [paulund](https://paulund.co.uk/projects/content-markdown)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
