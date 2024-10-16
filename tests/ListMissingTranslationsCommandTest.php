<?php

namespace Spatie\Translatable\Test;

use Illuminate\Support\Facades\Artisan;
use Spatie\Translatable\Test\TestSupport\TestModel;
use InvalidArgumentException;

beforeEach(function () {
    $this->testModel = new TestModel();
});

it('lists missing translations', function () {
    $model = $this->testModel::create([
        'name' => [
            'en' => 'Hello',
            'fr' => '',
            'es' => null,
        ],
    ]);

    Artisan::call('translatable:missing', [
        'model' => TestModel::class,
        '--locales' => 'en,fr,es',
        '--attributes' => 'name',
    ]);

    $output = Artisan::output();

    expect($output)->toContain('Missing Translations');
    expect($output)->toContain(TestModel::class);
    expect($output)->toContain((string)$model->getKey());
    expect($output)->toContain('name');
    expect($output)->toContain('fr, es');
    expect($output)->not->toContain('en');
});

it('reports all translations are complete when there are no missing translations', function () {
    $model = $this->testModel::create([
        'name' => [
            'en' => 'Hello',
            'fr' => 'Bonjour',
            'es' => 'Hola',
        ],
    ]);

    Artisan::call('translatable:missing', [
        'model' => TestModel::class,
        '--locales' => 'en,fr,es',
        '--attributes' => 'name',
    ]);

    $output = Artisan::output();

    expect($output)->toContain('All translations are complete for model');
    expect($output)->toContain(TestModel::class);
    expect($output)->not->toContain('Missing Translations');
});

it('reports when no records are found for the model', function () {
    $this->testModel::truncate();

    expect(fn () => Artisan::call('translatable:missing', [
        'model' => TestModel::class,
        '--locales' => 'en,fr,es',
        '--attributes' => 'name',
    ]))->toThrow(
        InvalidArgumentException::class,
        "No records found for model " . TestModel::class
    );
});

