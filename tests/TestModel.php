<?php

namespace Spatie\Translatable\Test;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Translatable\Translatable;

class TestModel extends Model implements Translatable
{
    use HasTranslations;

    protected $table = 'test_models';

    protected $guarded = [];
    public $timestamps = false;

    public function getTranslatableAttributes() : array
    {
        return ['name'];
    }
}
