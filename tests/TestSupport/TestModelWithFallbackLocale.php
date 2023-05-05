<?php

namespace Spatie\Translatable\Test\TestSupport;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TestModelWithFallbackLocale extends Model
{
    use HasTranslations;

    public static $fallbackLocale;

    protected $table = 'test_models';

    protected $guarded = [];
    public $timestamps = false;

    public $translatable = ['name', 'other_field', 'field_with_mutator'];

    public function getFallbackLocale() : string
    {
        return static::$fallbackLocale;
    }

    public function setFieldWithMutatorAttribute($value)
    {
        $this->attributes['field_with_mutator'] = $value;
    }
}
