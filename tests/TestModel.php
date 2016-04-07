<?php

namespace Spatie\MediaLibrary\Test;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Translatable\Translatable;

class TestModel extends Model implements Translatable
{
    use HasTranslations;

    protected $table = 'test_models';
    protected $guarded = [];
    public $timestamps = false;

    public function getTranslatableFields() : array
    {
        return ['name'];
    }
}
