<?php

use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;
use Spatie\Translatable\Test\TestSupport\TestModelWithAttributeDefinition;

it('can get translatable attributes defined via php attribute', function () {
    $testModel = new TestModelWithAttributeDefinition;

    expect($testModel->getTranslatableAttributes())->toBe(['name', 'other_field']);
});

it('supports both array and variadic syntax in translatable attribute', function () {
    $attribute = new Translatable(['name', 'other_field']);
    expect($attribute->columns)->toBe(['name', 'other_field']);

    $attribute = new Translatable('name', 'other_field');
    expect($attribute->columns)->toBe(['name', 'other_field']);
});

it('merges property and attribute definitions', function () {
    $testModel = new #[Translatable('name', 'other_field')] class extends \Illuminate\Database\Eloquent\Model
    {
        use HasTranslations;

        protected $table = 'test_models';

        protected $guarded = [];

        public $timestamps = false;

        public $translatable = ['name', 'field_with_mutator'];
    };

    expect($testModel->getTranslatableAttributes())->toBe(['name', 'field_with_mutator', 'other_field']);
});

it('inherits translatable attribute from parent class', function () {
    $childModel = new class extends TestModelWithAttributeDefinition {};

    expect($childModel->getTranslatableAttributes())->toBe(['name', 'other_field']);
});
