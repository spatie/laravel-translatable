<?php

namespace Spatie\Translatable\Test\TestSupport;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Translatable('name', 'other_field')]
class TestModelWithAttributeDefinition extends Model
{
    use HasTranslations;

    protected $table = 'test_models';

    protected $guarded = [];

    public $timestamps = false;
}
