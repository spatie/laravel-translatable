<?php

namespace Spatie\Translatable\Test\TestSupport;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TestModel extends Model
{
    use HasTranslations;

    protected $table = 'test_models';

    protected $guarded = [];
    public $timestamps = false;

    public $translatable = ['name', 'other_field', 'field_with_mutator', 'field_with_mutator_attribute'];

    public function setFieldWithMutatorAttribute($value)
    {
        $this->attributes['field_with_mutator'] = $value;
    }

    protected function fieldWithMutatorAttribute(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value,
        );
    }
}
