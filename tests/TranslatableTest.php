<?php

use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\Exceptions\AttributeIsNotTranslatable;
use Spatie\Translatable\Facades\Translatable;

uses(TestCase::class);

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

    $this->assertSame('testValue_en', $this->testModel->getTranslation('name', 'fr'));
});

it('will return default fallback locale translation when getting an unknown locale', function () {
    config()->set('app.fallback_locale', 'en');

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    $this->assertSame('testValue_en', $this->testModel->getTranslation('name', 'fr'));
});

it('provides a flog to not return fallback locale translation when getting an unknown locale', function () {
    config()->set('app.fallback_locale', 'en');

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    $this->assertSame('', $this->testModel->getTranslation('name', 'fr', false));
});

it('will return fallback locale translation when getting an unknown locale and fallback is true', function () {
    config()->set('app.fallback_locale', 'en');

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    $this->assertSame('testValue_en', $this->testModel->getTranslationWithFallback('name', 'fr'));
});

it('will execute callback fallback when getting an unknown locale and fallback callback is enabled', function () {
    Storage::fake();

    Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
        //something assertable outside the closure
        Storage::put("test.txt", "test");
    });

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    $this->assertSame('testValue_en', $this->testModel->getTranslationWithFallback('name', 'fr'));

    Storage::assertExists("test.txt");
});

it('will use callback fallback return value as translation', function () {
    Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
        return "testValue_fallback_callback";
    });

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    $this->assertSame('testValue_fallback_callback', $this->testModel->getTranslationWithFallback('name', 'fr'));
});

it('wont use callback fallback return value as translation if it is not a string', function () {
    Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
        return 123456;
    });

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    $this->assertSame('testValue_en', $this->testModel->getTranslationWithFallback('name', 'fr'));
});

it('wont execute callback fallback when getting an existing translation', function () {
    Storage::fake();

    Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
        //something assertable outside the closure
        Storage::put("test.txt", "test");
    });

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    $this->assertSame('testValue_en', $this->testModel->getTranslationWithFallback('name', 'en'));

    Storage::assertMissing("test.txt");
});

it('wont fail if callback fallback throw exception', function () {
    Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
        throw new \Exception();
    });

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    $this->assertSame('testValue_en', $this->testModel->getTranslationWithFallback('name', 'fr'));
});

it('will return an empty string when getting an unknown locale and fallback is not set', function () {
    config()->set('app.fallback_locale', '');

    Translatable::fallback(
        fallbackLocale: '',
    );

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    $this->assertSame('', $this->testModel->getTranslationWithoutFallback('name', 'fr'));
});

it('will return an empty string when getting an unknown locale and fallback is empty', function () {
    config()->set('app.fallback_locale', '');

    Translatable::fallback(
        fallbackLocale: '',
    );

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    $this->assertSame('', $this->testModel->getTranslation('name', 'fr'));
});

it('can save a translated attribute', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    $this->assertSame('testValue_en', $this->testModel->name);
});

it('can set translated values when creating a model', function () {
    $model = TestModel::create([
        'name' => ['en' => 'testValue_en'],
    ]);

    $this->assertSame('testValue_en', $model->name);
});

it('can save multiple translations', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->save();

    $this->assertSame('testValue_en', $this->testModel->name);
    $this->assertSame('testValue_fr', $this->testModel->getTranslation('name', 'fr'));
});

it('will return the value of the current locale when using the property', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue');
    $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
    $this->testModel->save();

    app()->setLocale('fr');

    $this->assertSame('testValue_fr', $this->testModel->name);
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

    $this->assertSame(['en', 'fr'], $this->testModel->getTranslatedLocales('name'));
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

    $this->assertSame('[]', $this->testModel->getAttributes()['name']);
    $this->assertSame([], $this->testModel->getTranslations('name'));

    $this->testModel->save();

    $this->assertSame('[]', $this->testModel->fresh()->getAttributes()['name']);
    $this->assertSame([], $this->testModel->fresh()->getTranslations('name'));
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

    $this->assertNull($this->testModel->getAttributes()['name']);
    $this->assertSame([], $this->testModel->getTranslations('name'));

    $this->testModel->save();

    $this->assertNull($this->testModel->fresh()->getAttributes()['name']);
    $this->assertSame([], $this->testModel->fresh()->getTranslations('name'));
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

    $this->assertEquals((new $testModel())->otherField, 'accessorName');
});

it('can use accessors on translated attributes', function () {
    $testModel = new class () extends TestModel {
        public function getNameAttribute($value): string
        {
            return "I just accessed {$value}";
        }
    };

    $testModel->setTranslation('name', 'en', 'testValue_en');

    $this->assertEquals($testModel->name, 'I just accessed testValue_en');
});

it('can use mutators on translated attributes', function () {
    $testModel = new class () extends TestModel {
        public function setNameAttribute($value) {
            $this->attributes['name'] = "I just mutated {$value}";
        }
    };

    $testModel->setTranslation('name', 'en', 'testValue_en');

    $this->assertEquals($testModel->name, 'I just mutated testValue_en');
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
    $this->assertEquals('updated_en', $model->name);
    $this->assertEquals('testValue_fr', $model->getTranslation('name', 'fr'));

    app()->setLocale('fr');
    $model->name = 'updated_fr';
    $this->assertEquals('updated_fr', $model->name);
    $this->assertEquals('updated_en', $model->getTranslation('name', 'en'));
});

it('can set multiple translations at once', function () {
    $translations = ['nl' => 'hallo', 'en' => 'hello', 'kh' => 'សួរស្តី'];

    $this->testModel->setTranslations('name', $translations);
    $this->testModel->save();

    $this->assertEquals($translations, $this->testModel->getTranslations('name'));
});

it('can check if an attribute is translatable', function () {
    $this->assertTrue($this->testModel->isTranslatableAttribute('name'));

    $this->assertFalse($this->testModel->isTranslatableAttribute('other'));
});

it('can check if an attribute has translation', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->setTranslation('name', 'nl', null);
    $this->testModel->save();

    $this->assertTrue($this->testModel->hasTranslation('name', 'en'));

    $this->assertFalse($this->testModel->hasTranslation('name', 'pt'));
});

it('can correctly set a field when a mutator is defined', function () {
    $testModel = (new class () extends TestModel {
        public function setNameAttribute($value) {
            $this->attributes['name'] = "I just mutated {$value}";
        }
    });

    $testModel->name = 'hello';

    $expected = ['en' => 'I just mutated hello'];
    $this->assertEquals($expected, $testModel->getTranslations('name'));
});

it('can set multiple translations when a mutator is defined', function () {
    $testModel = (new class () extends TestModel {
        public function setNameAttribute($value) {
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

    $this->assertEquals($expected, $testModel->getTranslations('name'));
});

it('can set multiple translations on field when a mutator is defined', function () {
    $translations = [
        'nl' => 'hallo',
        'en' => 'hello',
    ];

    $testModel = $this->testModel;
    $testModel->field_with_mutator = $translations;
    $testModel->save();

    $this->assertEquals($translations, $testModel->getTranslations('field_with_mutator'));
});

it('can translate a field based on the translations of another one', function () {
    $testModel = (new class () extends TestModel {
        public function setOtherFieldAttribute($value, $locale = 'en') {
            $this->attributes['other_field'] = $value.' '.$this->getTranslation('name', $locale);
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

    $this->assertEquals($expected, $testModel->getTranslations('other_field'));
});

it('handle null value from database', function () {
    $testModel = (new class () extends TestModel {
        public function setAttributesExternally(array $attributes) {
            $this->attributes = $attributes;
        }
    });

    $testModel->setAttributesExternally(['name' => json_encode(null), 'other_field' => null]);

    $this->assertEquals('', $testModel->name);
    $this->assertEquals('', $testModel->other_field);
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

    $this->assertSame('testValue_en', $this->testModel->getTranslation('name', 'nl'));
});

it('will return correct translation value if value is set to zero', function () {
    $this->testModel->setTranslation('name', 'nl', '0');
    $this->testModel->save();

    $this->assertSame('0', $this->testModel->getTranslation('name', 'nl'));
});

it('will not return fallback value if value is set to zero', function () {
    config()->set('app.fallback_locale', 'en');

    $this->testModel->setTranslation('name', 'en', '1');
    $this->testModel->setTranslation('name', 'nl', '0');
    $this->testModel->save();

    $this->assertSame('0', $this->testModel->getTranslation('name', 'nl'));
});

it('will not remove zero value of other locale in database', function () {
    config()->set('app.fallback_locale', 'en');

    $this->testModel->setTranslation('name', 'nl', '0');
    $this->testModel->setTranslation('name', 'en', '1');
    $this->testModel->save();

    $this->assertSame('0', $this->testModel->getTranslation('name', 'nl'));
});

it('can be translated based on given locale', function () {
    $value = 'World';

    $this->testModel = TestModel::usingLocale('en')->fill([
        'name' => $value,
    ]);
    $this->testModel->save();

    $this->assertSame($value, $this->testModel->getTranslation('name', 'en'));
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
    $this->assertSame($en, $this->testModel->name);
    $this->testModel->setLocale('fr');
    $this->assertSame($fr, $this->testModel->name);
});

it('can replace translations', function () {
    $translations = ['nl' => 'hallo', 'en' => 'hello', 'kh' => 'សួរស្តី'];

    $this->testModel->setTranslations('name', $translations);
    $this->testModel->save();

    $newTranslations = ['es' => 'hola'];
    $this->testModel->replaceTranslations('name', $newTranslations);

    $this->assertEquals($newTranslations, $this->testModel->getTranslations('name'));
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
    $this->assertSame('testValue_fr', $this->testModel->name);
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
    $this->assertSame('testValue_de', $this->testModel->name);
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
    $this->assertSame('testValue_en', $this->testModel->name);
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
    $this->assertSame('', $this->testModel->getTranslation('name', 'it', false));
});

it('will return default fallback locale translation when getting an unknown locale with fallback any', function () {
    config()->set('app.fallback_locale', 'en');

    Translatable::fallback(
        fallbackAny: true,
    );

    $this->testModel->setTranslation('name', 'en', 'testValue_en');
    $this->testModel->save();

    $this->assertSame('testValue_en', $this->testModel->getTranslation('name', 'fr'));
});
