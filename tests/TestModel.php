<?php

namespace Spatie\Translatable\Test;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Translatable\Translatable;

class TestModel extends Model implements Translatable
{
    use HasTranslations;

    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'name' => 'array',
    ];

    public function getTranslatableAttributes() : array
    {
        return ['name'];
    }
}
