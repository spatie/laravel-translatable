<?php

use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\Exceptions\AttributeIsNotTranslatable;
use Spatie\Translatable\Facades\Translatable;
use Spatie\Translatable\Test\TestSupport\TestModel;

beforeEach(function () {
    $this->testModel = new TestModel();
});

it('will return package fallback locale translation when getting an unknown locale', function () {
    config()->set('app.fallback_locale', 'nl');
    Translatable::fallback(
        fallbackLocale: 'en',
    );

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    expect($this->testModel->getTranslation('name', 'fr'))->toBe('testValue_en');
});

it('will return default fallback locale translation when getting an unknown locale', function () {
    config()->set('app.fallback_locale', 'en');

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    expect($this->testModel->getTranslation('name', 'fr'))->toBe('testValue_en');
});

it('provides a flog to not return fallback locale translation when getting an unknown locale', function () {
    config()->set('app.fallback_locale', 'en');

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    expect($this->testModel->getTranslation('name', 'fr', false))->toBe('');
});

it('will return fallback locale translation when getting an unknown locale and fallback is true', function () {
    config()->set('app.fallback_locale', 'en');

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    expect($this->testModel->getTranslationWithFallback('name', 'fr'))->toBe('testValue_en');
});

it('will execute callback fallback when getting an unknown locale and fallback callback is enabled', function () {
    Storage::fake();

    Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
        //something assertable outside the closure
        Storage::put("test.txt", "test");
    });

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    expect($this->testModel->getTranslationWithFallback('name', 'fr'))->toBe('testValue_en');

    Storage::assertExists("test.txt");
});

it('will use callback fallback return value as translation', function () {
    Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
        return "testValue_fallback_callback";
    });

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    expect($this->testModel->getTranslationWithFallback('name', 'fr'))->toBe('testValue_fallback_callback');
});

it('wont use callback fallback return value as translation if it is not a string', function () {
    Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
        return 123456;
    });

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    expect($this->testModel->getTranslationWithFallback('name', 'fr'))->toBe('testValue_en');
});

it('wont execute callback fallback when getting an existing translation', function () {
    Storage::fake();

    Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
        //something assertable outside the closure
        Storage::put("test.txt", "test");
    });

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    expect($this->testModel->getTranslationWithFallback('name', 'en'))->toBe('testValue_en');

    Storage::assertMissing("test.txt");
});

it('wont fail if callback fallback throw exception', function () {
    Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
        throw new \Exception();
    });

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    expect($this->testModel->getTranslationWithFallback('name', 'fr'))->toBe('testValue_en');
});

it('will return an empty string when getting an unknown locale and fallback is not set', function () {
    config()->set('app.fallback_locale', '');

    Translatable::fallback(
        fallbackLocale: '',
    );

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    expect($this->testModel->getTranslationWithoutFallback('name', 'fr'))->toBe('');
});

it('will return an empty string when getting an unknown locale and fallback is empty', function () {
    config()->set('app.fallback_locale', '');

    Translatable::fallback(
        fallbackLocale: '',
    );

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    expect($this->testModel->getTranslation('name', 'fr'))->toBe('');
});

it('can save a translated attribute', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    expect($this->testModel->name)->toBe('testValue_en');
});

it('can set translated values when creating a model', function () {
    $model = TestModel::create([
        'name' => ['en' => 'testValue_en'],
    ]);

    expect($model->name)->toBe('testValue_en');
});

it('can save multiple translations', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->save();

    expect($this->testModel->name)->toBe('testValue_en');
    expect($this->testModel->getTranslation('name', 'fr'))->toBe('testValue_fr');
});

it('will return the value of the current locale when using the property', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->save();

    app()->setLocale('fr');

    expect($this->testModel->name)->toBe('testValue_fr');
});

it('can get all translations in one go', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->save();

    $this->assertSame([
        'en' => 'testValue_en',
        'fr' => 'testValue_fr',
    ], $this->testModel->getTranslations('name'));
});

it('can get specified translations in one go', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->save();

    $this->assertSame([
        'en' => 'testValue_en',
    ], $this->testModel->getTranslations('name', ['en']));
});

it('can get all translations for all translatable attributes in one go', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');

    $this->testModel->setTranslation('other_field', 'en', 'testValue_en');
    $this->testModel->setTranslation('other_field', 'fr', 'testValue_fr');

    $this->testModel->setTranslation('field_with_mutator', 'en', 'testValue_en');
    $this->testModel->setTranslation('field_with_mutator', 'fr', 'testValue_fr');
    $this->testModel->save();

    $this->assertSame([
        'name' => [
            'en' => 'testValue_en',
            'fr' => 'testValue_fr',
        ],
        'other_field' => [
            'en' => 'testValue_en',
            'fr' => 'testValue_fr',
        ],
        'field_with_mutator' => [
            'en' => 'testValue_en',
            'fr' => 'testValue_fr',
        ],
    ], $this->testModel->getTranslations());
});

it('can get specified translations for all translatable attributes in one go', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');

    $this->testModel->setTranslation('other_field', 'en', 'testValue_en');
    $this->testModel->setTranslation('other_field', 'fr', 'testValue_fr');

    $this->testModel->setTranslation('field_with_mutator', 'en', 'testValue_en');
    $this->testModel->setTranslation('field_with_mutator', 'fr', 'testValue_fr');
    $this->testModel->save();

    $this->assertSame([
        'name' => ['en' => 'testValue_en'],
        'other_field' => ['en' => 'testValue_en'],
        'field_with_mutator' => ['en' => 'testValue_en'],
    ], $this->testModel->getTranslations(null, ['en']));
});

it('can get the locales which have a translation', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->save();

    expect($this->testModel->getTranslatedLocales('name'))->toBe(['en', 'fr']);
});

it('can forget a translation', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->save();

    $this->assertSame([
        'en' => 'testValue_en',
        'fr' => 'testValue_fr',
    ], $this->testModel->getTranslations('name'));

    $this->testModel->forgetTranslation('name', 'en');

    $this->assertSame([
        'fr' => 'testValue_fr',
    ], $this->testModel->getTranslations('name'));
});

it('can forget all translations of field', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->save();

    $this->assertSame([
        'en' => 'testValue_en',
        'fr' => 'testValue_fr',
    ], $this->testModel->getTranslations('name'));

    $this->testModel->forgetTranslations('name');

    expect($this->testModel->getAttributes()['name'])->toBe('[]');
    expect($this->testModel->getTranslations('name'))->toBe([]);

    $this->testModel->save();

    expect($this->testModel->fresh()->getAttributes()['name'])->toBe('[]');
    expect($this->testModel->fresh()->getTranslations('name'))->toBe([]);
});

it('can forget all translations of field and make field null', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->save();

    $this->assertSame([
        'en' => 'testValue_en',
        'fr' => 'testValue_fr',
    ], $this->testModel->getTranslations('name'));

    $this->testModel->forgetTranslations('name', true);

    expect($this->testModel->getAttributes()['name'])->toBeNull();
    expect($this->testModel->getTranslations('name'))->toBe([]);

    $this->testModel->save();

    expect($this->testModel->fresh()->getAttributes()['name'])->toBeNull();
    expect($this->testModel->fresh()->getTranslations('name'))->toBe([]);
});

it('can forget a field with mutator translation', function () {
    $this->testModel->setTranslation('field_with_mutator', 'en', 'testValue_en');
    $this->testModel->setTranslation('field_with_mutator', 'fr', 'testValue_fr');
    $this->testModel->save();

    $this->assertSame([
        'en' => 'testValue_en',
        'fr' => 'testValue_fr',
    ], $this->testModel->getTranslations('field_with_mutator'));

    $this->testModel->forgetTranslation('field_with_mutator', 'en');

    $this->assertSame([
        'fr' => 'testValue_fr',
    ], $this->testModel->getTranslations('field_with_mutator'));
});

it('can forget all translations', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');

    $this->testModel->setTranslation('other_field', 'en', 'testValue_en');
    $this->testModel->setTranslation('other_field', 'fr', 'testValue_fr');

    $this->testModel->setTranslation('field_with_mutator', 'en', 'testValue_en');
    $this->testModel->setTranslation('field_with_mutator', 'fr', 'testValue_fr');
    $this->testModel->save();

    $this->assertSame([
        'en' => 'testValue_en',
        'fr' => 'testValue_fr',
    ], $this->testModel->getTranslations('name'));

    $this->assertSame([
        'en' => 'testValue_en',
        'fr' => 'testValue_fr',
    ], $this->testModel->getTranslations('other_field'));

    $this->assertSame([
        'en' => 'testValue_en',
        'fr' => 'testValue_fr',
    ], $this->testModel->getTranslations('field_with_mutator'));

    $this->testModel->forgetAllTranslations('en');

    $this->assertSame([
        'fr' => 'testValue_fr',
    ], $this->testModel->getTranslations('name'));

    $this->assertSame([
        'fr' => 'testValue_fr',
    ], $this->testModel->getTranslations('other_field'));

    $this->assertSame([
        'fr' => 'testValue_fr',
    ], $this->testModel->getTranslations('field_with_mutator'));
});

it('will throw an exception when trying to translate an untranslatable attribute', function () {
    $this->expectException(AttributeIsNotTranslatable::class);

    $this->testModel->setTranslation('untranslated', 'en', 'value');
});

it('is compatible with accessors on non translatable attributes', function () {
    $testModel = new class () extends TestModel {
        public function getOtherFieldAttribute(): string
        {
            return 'accessorName';
        }
    };

    expect('accessorName')->toEqual((new $testModel())->otherField);
});

it('can use accessors on translated attributes', function () {
    $testModel = new class () extends TestModel {
        public function getNameAttribute($value): string
        {
            return "I just accessed {$value}";
        }
    };

    $testModel->setTranslation('name', 'en', 'testValue_en');

    expect('I just accessed testValue_en')->toEqual($testModel->name);
});

it('can use mutators on translated attributes', function () {
    $testModel = new class () extends TestModel {
        public function setNameAttribute($value)
        {
            $this->attributes['name'] = "I just mutated {$value}";
        }
    };

    $testModel->setTranslation('name', 'en', 'testValue_en');

    expect('I just mutated testValue_en')->toEqual($testModel->name);
});

it('can set translations for default language', function () {
    $model = TestModel::create([
        'name' => [
            'en' => 'testValue_en',
            'fr' => 'testValue_fr',
        ],
    ]);

    app()->setLocale('en');

    $model->name = 'updated_en';
    expect($model->name)->toEqual('updated_en');
    expect($model->getTranslation('name', 'fr'))->toEqual('testValue_fr');

    app()->setLocale('fr');
    $model->name = 'updated_fr';
    expect($model->name)->toEqual('updated_fr');
    expect($model->getTranslation('name', 'en'))->toEqual('updated_en');
});

it('can set multiple translations at once', function () {
    $translations = ['nl' => 'hallo', 'en' => 'hello', 'kh' => 'សួរស្តី'];

    $this->testModel->setTranslations('name', $translations);
    $this->testModel->save();

    expect($this->testModel->getTranslations('name'))->toEqual($translations);
});

it('can check if an attribute is translatable', function () {
    expect($this->testModel->isTranslatableAttribute('name'))->toBeTrue();

    expect($this->testModel->isTranslatableAttribute('other'))->toBeFalse();
});

it('can check if an attribute has translation', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'nl', null);
    $this->testModel->save();

    expect($this->testModel->hasTranslation('name', 'en'))->toBeTrue();

    expect($this->testModel->hasTranslation('name', 'pt'))->toBeFalse();
});

it('can correctly set a field when a mutator is defined', function () {
    $testModel = (new class () extends TestModel {
        public function setNameAttribute($value)
        {
            $this->attributes['name'] = "I just mutated {$value}";
        }
    });

    $testModel->name = 'hello';

    $expected = ['en' => 'I just mutated hello'];
    expect($testModel->getTranslations('name'))->toEqual($expected);
});

it('can set multiple translations when a mutator is defined', function () {
    $testModel = (new class () extends TestModel {
        public function setNameAttribute($value)
        {
            $this->attributes['name'] = "I just mutated {$value}";
        }
    });

    $translations = [
        'nl' => 'hallo',
        'en' => 'hello',
        'kh' => 'សួរស្តី',
    ];

    $testModel->setTranslations('name', $translations);

    $testModel->save();

    $expected = [
        'nl' => 'I just mutated hallo',
        'en' => 'I just mutated hello',
        'kh' => 'I just mutated សួរស្តី',
    ];

    expect($testModel->getTranslations('name'))->toEqual($expected);
});

it('can set multiple translations on field when a mutator is defined', function () {
    $translations = [
        'nl' => 'hallo',
        'en' => 'hello',
    ];

    $testModel = $this->testModel;
    $testModel->field_with_mutator = $translations;
    $testModel->save();

    expect($testModel->getTranslations('field_with_mutator'))->toEqual($translations);
});

it('can translate a field based on the translations of another one', function () {
    $testModel = (new class () extends TestModel {
        public function setOtherFieldAttribute($value, $locale = 'en')
        {
            $this->attributes['other_field'] = $value . ' ' . $this->getTranslation('name', $locale);
        }
    });

    $testModel->setTranslations('name', [
        'nl' => 'wereld',
        'en' => 'world',
    ]);

    $testModel->setTranslations('other_field', [
        'nl' => 'hallo',
        'en' => 'hello',
    ]);

    $testModel->save();

    $expected = [
        'nl' => 'hallo wereld',
        'en' => 'hello world',
    ];

    expect($testModel->getTranslations('other_field'))->toEqual($expected);
});

it('handle null value from database', function () {
    $testModel = (new class () extends TestModel {
        public function setAttributesExternally(array $attributes)
        {
            $this->attributes = $attributes;
        }
    });

    $testModel->setAttributesExternally(['name' => json_encode(null), 'other_field' => null]);

    expect($testModel->name)->toEqual('');
    expect($testModel->other_field)->toEqual('');
});

it('can get all translations', function () {
    $translations = ['nl' => 'hallo', 'en' => 'hello'];

    $this->testModel->setTranslations('name', $translations);
    $this->testModel->setTranslations('field_with_mutator', $translations);
    $this->testModel->save();

    $this->assertEquals([
        'name' => ['nl' => 'hallo', 'en' => 'hello'],
        'other_field' => [],
        'field_with_mutator' => ['nl' => 'hallo', 'en' => 'hello'],
    ], $this->testModel->translations);
});

it('will return fallback locale translation when getting an empty translation from the locale', function () {
    config()->set('app.fallback_locale', 'en');

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'nl', null);
    $this->testModel->save();

    expect($this->testModel->getTranslation('name', 'nl'))->toBe('testValue_en');
});

it('will return correct translation value if value is set to zero', function () {
    $this->testModel->setTranslation('name', 'nl', '0');
    $this->testModel->save();

    expect($this->testModel->getTranslation('name', 'nl'))->toBe('0');
});

it('will not return fallback value if value is set to zero', function () {
    config()->set('app.fallback_locale', 'en');

    $this->testModel->setTranslation('name', 'en', '1');
    $this->testModel->setTranslation('name', 'nl', '0');
    $this->testModel->save();

    expect($this->testModel->getTranslation('name', 'nl'))->toBe('0');
});

it('will not remove zero value of other locale in database', function () {
    config()->set('app.fallback_locale', 'en');

    $this->testModel->setTranslation('name', 'nl', '0');
    $this->testModel->setTranslation('name', 'en', '1');
    $this->testModel->save();

    expect($this->testModel->getTranslation('name', 'nl'))->toBe('0');
});

it('can be translated based on given locale', function () {
    $value = 'World';

    $this->testModel = TestModel::usingLocale('en')->fill([
        'name' => $value,
    ]);
    $this->testModel->save();

    expect($this->testModel->getTranslation('name', 'en'))->toBe($value);
});

it('can set and fetch attributes based on set locale', function () {
    $en = 'World';
    $fr = 'Monde';

    $this->testModel->setLocale('en');
    $this->testModel->name = $en;
    $this->testModel->setLocale('fr');
    $this->testModel->name = $fr;

    $this->testModel->save();

    $this->testModel->setLocale('en');
    expect($this->testModel->name)->toBe($en);
    $this->testModel->setLocale('fr');
    expect($this->testModel->name)->toBe($fr);
});

it('can replace translations', function () {
    $translations = ['nl' => 'hallo', 'en' => 'hello', 'kh' => 'សួរស្តី'];

    $this->testModel->setTranslations('name', $translations);
    $this->testModel->save();

    $newTranslations = ['es' => 'hola'];
    $this->testModel->replaceTranslations('name', $newTranslations);

    expect($this->testModel->getTranslations('name'))->toEqual($newTranslations);
});

it('can use any locale if given locale not set', function () {
    config()->set('app.fallback_locale', 'en');

    Translatable::fallback(
        fallbackAny: true,
    );

    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->setTranslation('name', 'de', 'testValue_de');
    $this->testModel->save();

    $this->testModel->setLocale('it');
    expect($this->testModel->name)->toBe('testValue_fr');
});

it('will return set translation when fallback any set', function () {
    config()->set('app.fallback_locale', 'en');

    Translatable::fallback(
        fallbackAny: true,
    );

    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->setTranslation('name', 'de', 'testValue_de');
    $this->testModel->save();

    $this->testModel->setLocale('de');
    expect($this->testModel->name)->toBe('testValue_de');
});

it('will return fallback translation when fallback any set', function () {
    config()->set('app.fallback_locale', 'en');

    Translatable::fallback(
        fallbackAny: true,
    );

    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    $this->testModel->setLocale('de');
    expect($this->testModel->name)->toBe('testValue_en');
});

it('provides a flog to not return any translation when getting an unknown locale', function () {
    config()->set('app.fallback_locale', 'en');

    Translatable::fallback(
        fallbackAny: true,
    );

    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->setTranslation('name', 'de', 'testValue_de');
    $this->testModel->save();

    $this->testModel->setLocale('it');
    expect($this->testModel->getTranslation('name', 'it', false))->toBe('');
});

it('will return default fallback locale translation when getting an unknown locale with fallback any', function () {
    config()->set('app.fallback_locale', 'en');

    Translatable::fallback(
        fallbackAny: true,
    );

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    expect($this->testModel->getTranslation('name', 'fr'))->toBe('testValue_en');
});

it('will return all locales when getting all translations', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->setTranslation('name', 'tr', 'testValue_tr');
    $this->testModel->save();

    expect($this->testModel->locales())->toEqual([
        'en',
        'fr',
        'tr',
    ]);
});
