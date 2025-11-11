<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'templates';

    protected $fillable = [
        'code',
        'language',
        'title',
        'body',
        'version',
        'active',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'active' => 'boolean',
    ];
}
