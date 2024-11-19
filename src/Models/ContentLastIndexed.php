<?php

namespace Paulund\ContentMarkdown\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $last_indexed
 */
class ContentLastIndexed extends Model
{
    public $timestamps = false;

    protected $table = 'content_last_indexed';

    protected $fillable = [
        'last_indexed',
    ];

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('content-markdown.database.content_table_name', 'contents').'_last_indexed';
    }
}
