<?php

namespace Spatie\Translatable\Test;

use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\Exceptions\AttributeIsNotTranslatable;
use Spatie\Translatable\Facades\Translatable;

class TranslatableTest extends TestCase
{
    protected TestModel $testModel;

    public function setUp(): void
    {
        parent::setUp();

        $this->testModel = new TestModel();
    }

    /** @test */
    public function it_will_return_package_fallback_locale_translation_when_getting_an_unknown_locale()
    {
        config()->set('app.fallback_locale', 'nl');
        Translatable::fallback(
            fallbackLocale: 'en',
        );

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->getTranslation('name', 'fr'));
    }

    /** @test */
    public function it_will_return_default_fallback_locale_translation_when_getting_an_unknown_locale()
    {
        config()->set('app.fallback_locale', 'en');

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->getTranslation('name', 'fr'));
    }

    /** @test */
    public function it_provides_a_flog_to_not_return_fallback_locale_translation_when_getting_an_unknown_locale()
    {
        config()->set('app.fallback_locale', 'en');

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('', $this->testModel->getTranslation('name', 'fr', false));
    }

    /** @test */
    public function it_will_return_fallback_locale_translation_when_getting_an_unknown_locale_and_fallback_is_true()
    {
        config()->set('app.fallback_locale', 'en');

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->getTranslationWithFallback('name', 'fr'));
    }

    /** @test */
    public function it_will_execute_callback_fallback_when_getting_an_unknown_locale_and_fallback_callback_is_enabled()
    {
        Storage::fake();

        Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
            //something assertable outside the closure
            Storage::put("test.txt", "test");
        });

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->getTranslationWithFallback('name', 'fr'));

        Storage::assertExists("test.txt");
    }

    /** @test */
    public function it_will_use_callback_fallback_return_value_as_translation()
    {
        Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
            return "testValue_fallback_callback";
        });

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_fallback_callback', $this->testModel->getTranslationWithFallback('name', 'fr'));
    }

    /** @test */
    public function it_wont_use_callback_fallback_return_value_as_translation_if_it_is_not_a_string()
    {
        Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
            return 123456;
        });

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->getTranslationWithFallback('name', 'fr'));
    }

    /** @test */
    public function it_wont_execute_callback_fallback_when_getting_an_existing_translation()
    {
        Storage::fake();

        Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
            //something assertable outside the closure
            Storage::put("test.txt", "test");
        });

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->getTranslationWithFallback('name', 'en'));

        Storage::assertMissing("test.txt");
    }

    /** @test */
    public function it_wont_fail_if_callback_fallback_throw_exception()
    {
        Translatable::fallback(missingKeyCallback: function ($model, string $translationKey, string $locale) {
            throw new \Exception();
        });

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->getTranslationWithFallback('name', 'fr'));
    }

    /** @test */
    public function it_will_return_an_empty_string_when_getting_an_unknown_locale_and_fallback_is_not_set()
    {
        config()->set('app.fallback_locale', '');

        Translatable::fallback(
            fallbackLocale: '',
        );

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('', $this->testModel->getTranslationWithoutFallback('name', 'fr'));
    }

    /** @test */
    public function it_will_return_an_empty_string_when_getting_an_unknown_locale_and_fallback_is_empty()
    {
        config()->set('app.fallback_locale', '');

        Translatable::fallback(
            fallbackLocale: '',
        );

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('', $this->testModel->getTranslation('name', 'fr'));
    }

    /** @test */
    public function it_can_save_a_translated_attribute()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->name);
    }

    /** @test */
    public function it_can_set_translated_values_when_creating_a_model()
    {
        $model = TestModel::create([
            'name' => ['en' => 'testValue_en'],
        ]);

        $this->assertSame('testValue_en', $model->name);
    }

    /** @test */
    public function it_can_save_multiple_translations()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->name);
        $this->assertSame('testValue_fr', $this->testModel->getTranslation('name', 'fr'));
    }

    /** @test */
    public function it_will_return_the_value_of_the_current_locale_when_using_the_property()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue');
        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->save();

        app()->setLocale('fr');

        $this->assertSame('testValue_fr', $this->testModel->name);
    }

    /** @test */
    public function it_can_get_all_translations_in_one_go()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->save();

        $this->assertSame([
            'en' => 'testValue_en',
            'fr' => 'testValue_fr',
        ], $this->testModel->getTranslations('name'));
    }

    /** @test */
    public function it_can_get_specified_translations_in_one_go()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->save();

        $this->assertSame([
            'en' => 'testValue_en',
        ], $this->testModel->getTranslations('name', ['en']));
    }

    /** @test */
    public function it_can_get_all_translations_for_all_translatable_attributes_in_one_go()
    {
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
    }

    /** @test */
    public function it_can_get_specified_translations_for_all_translatable_attributes_in_one_go()
    {
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
    }

    /** @test */
    public function it_can_get_the_locales_which_have_a_translation()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->save();

        $this->assertSame(['en', 'fr'], $this->testModel->getTranslatedLocales('name'));
    }

    /** @test */
    public function it_can_forget_a_translation()
    {
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
    }

    /** @test */
    public function it_can_forget_all_translations_of_field()
    {
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
    }

    /** @test */
    public function it_can_forget_all_translations_of_field_and_make_field_null()
    {
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
    }

    /** @test */
    public function it_can_forget_a_field_with_mutator_translation()
    {
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
    }

    /** @test */
    public function it_can_forget_all_translations()
    {
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
    }

    /** @test */
    public function it_will_throw_an_exception_when_trying_to_translate_an_untranslatable_attribute()
    {
        $this->expectException(AttributeIsNotTranslatable::class);

        $this->testModel->setTranslation('untranslated', 'en', 'value');
    }

    /** @test */
    public function it_is_compatible_with_accessors_on_non_translatable_attributes()
    {
        $testModel = new class () extends TestModel {
            public function getOtherFieldAttribute(): string
            {
                return 'accessorName';
            }
        };

        $this->assertEquals((new $testModel())->otherField, 'accessorName');
    }

    /** @test */
    public function it_can_use_accessors_on_translated_attributes()
    {
        $testModel = new class () extends TestModel {
            public function getNameAttribute($value): string
            {
                return "I just accessed {$value}";
            }
        };

        $testModel->setTranslation('name', 'en', 'testValue_en');

        $this->assertEquals($testModel->name, 'I just accessed testValue_en');
    }

    /** @test */
    public function it_can_use_mutators_on_translated_attributes()
    {
        $testModel = new class () extends TestModel {
            public function setNameAttribute($value)
            {
                $this->attributes['name'] = "I just mutated {$value}";
            }
        };

        $testModel->setTranslation('name', 'en', 'testValue_en');

        $this->assertEquals($testModel->name, 'I just mutated testValue_en');
    }

    /** @test */
    public function it_can_set_translations_for_default_language()
    {
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
    }

    /** @test */
    public function it_can_set_multiple_translations_at_once()
    {
        $translations = ['nl' => 'hallo', 'en' => 'hello', 'kh' => 'សួរស្តី'];

        $this->testModel->setTranslations('name', $translations);
        $this->testModel->save();

        $this->assertEquals($translations, $this->testModel->getTranslations('name'));
    }

    /** @test */
    public function it_can_check_if_an_attribute_is_translatable()
    {
        $this->assertTrue($this->testModel->isTranslatableAttribute('name'));

        $this->assertFalse($this->testModel->isTranslatableAttribute('other'));
    }

    /** @test */
    public function it_can_check_if_an_attribute_has_translation()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->setTranslation('name', 'nl', null);
        $this->testModel->save();

        $this->assertTrue($this->testModel->hasTranslation('name', 'en'));

        $this->assertFalse($this->testModel->hasTranslation('name', 'pt'));
    }

    /** @test */
    public function it_can_correctly_set_a_field_when_a_mutator_is_defined()
    {
        $testModel = (new class () extends TestModel {
            public function setNameAttribute($value)
            {
                $this->attributes['name'] = "I just mutated {$value}";
            }
        });

        $testModel->name = 'hello';

        $expected = ['en' => 'I just mutated hello'];
        $this->assertEquals($expected, $testModel->getTranslations('name'));
    }

    /** @test */
    public function it_can_set_multiple_translations_when_a_mutator_is_defined()
    {
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

        $this->assertEquals($expected, $testModel->getTranslations('name'));
    }

    /** @test */
    public function it_can_set_multiple_translations_on_field_when_a_mutator_is_defined()
    {
        $translations = [
            'nl' => 'hallo',
            'en' => 'hello',
        ];

        $testModel = $this->testModel;
        $testModel->field_with_mutator = $translations;
        $testModel->save();

        $this->assertEquals($translations, $testModel->getTranslations('field_with_mutator'));
    }

    /** @test */
    public function it_can_translate_a_field_based_on_the_translations_of_another_one()
    {
        $testModel = (new class () extends TestModel {
            public function setOtherFieldAttribute($value, $locale = 'en')
            {
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
    }

    /** @test */
    public function it_handle_null_value_from_database()
    {
        $testModel = (new class () extends TestModel {
            public function setAttributesExternally(array $attributes)
            {
                $this->attributes = $attributes;
            }
        });

        $testModel->setAttributesExternally(['name' => json_encode(null), 'other_field' => null]);

        $this->assertEquals('', $testModel->name);
        $this->assertEquals('', $testModel->other_field);
    }

    /** @test */
    public function it_can_get_all_translations()
    {
        $translations = ['nl' => 'hallo', 'en' => 'hello'];

        $this->testModel->setTranslations('name', $translations);
        $this->testModel->setTranslations('field_with_mutator', $translations);
        $this->testModel->save();

        $this->assertEquals([
            'name' => ['nl' => 'hallo', 'en' => 'hello'],
            'other_field' => [],
            'field_with_mutator' => ['nl' => 'hallo', 'en' => 'hello'],
        ], $this->testModel->translations);
    }

    /** @test */
    public function it_will_return_fallback_locale_translation_when_getting_an_empty_translation_from_the_locale()
    {
        config()->set('app.fallback_locale', 'en');

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->setTranslation('name', 'nl', null);
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->getTranslation('name', 'nl'));
    }

    /** @test */
    public function it_will_return_correct_translation_value_if_value_is_set_to_zero()
    {
        $this->testModel->setTranslation('name', 'nl', '0');
        $this->testModel->save();

        $this->assertSame('0', $this->testModel->getTranslation('name', 'nl'));
    }

    /** @test */
    public function it_will_not_return_fallback_value_if_value_is_set_to_zero()
    {
        config()->set('app.fallback_locale', 'en');

        $this->testModel->setTranslation('name', 'en', '1');
        $this->testModel->setTranslation('name', 'nl', '0');
        $this->testModel->save();

        $this->assertSame('0', $this->testModel->getTranslation('name', 'nl'));
    }

    /** @test */
    public function it_will_not_remove_zero_value_of_other_locale_in_database()
    {
        config()->set('app.fallback_locale', 'en');

        $this->testModel->setTranslation('name', 'nl', '0');
        $this->testModel->setTranslation('name', 'en', '1');
        $this->testModel->save();

        $this->assertSame('0', $this->testModel->getTranslation('name', 'nl'));
    }

    /** @test */
    public function it_can_be_translated_based_on_given_locale()
    {
        $value = 'World';

        $this->testModel = TestModel::usingLocale('en')->fill([
            'name' => $value,
        ]);
        $this->testModel->save();

        $this->assertSame($value, $this->testModel->getTranslation('name', 'en'));
    }

    /** @test */
    public function it_can_set_and_fetch_attributes_based_on_set_locale()
    {
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
    }

    /** @test */
    public function it_can_replace_translations()
    {
        $translations = ['nl' => 'hallo', 'en' => 'hello', 'kh' => 'សួរស្តី'];

        $this->testModel->setTranslations('name', $translations);
        $this->testModel->save();

        $newTranslations = ['es' => 'hola'];
        $this->testModel->replaceTranslations('name', $newTranslations);

        $this->assertEquals($newTranslations, $this->testModel->getTranslations('name'));
    }

    /** @test */
    public function it_can_use_any_locale_if_given_locale_not_set()
    {
        config()->set('app.fallback_locale', 'en');

        Translatable::fallback(
            fallbackAny: true,
        );

        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->setTranslation('name', 'de', 'testValue_de');
        $this->testModel->save();

        $this->testModel->setLocale('it');
        $this->assertSame('testValue_fr', $this->testModel->name);
    }

    /** @test */
    public function it_will_return_set_translation_when_fallback_any_set()
    {
        config()->set('app.fallback_locale', 'en');

        Translatable::fallback(
            fallbackAny: true,
        );

        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->setTranslation('name', 'de', 'testValue_de');
        $this->testModel->save();

        $this->testModel->setLocale('de');
        $this->assertSame('testValue_de', $this->testModel->name);
    }

    /** @test */
    public function it_will_return_fallback_translation_when_fallback_any_set()
    {
        config()->set('app.fallback_locale', 'en');

        Translatable::fallback(
            fallbackAny: true,
        );

        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->testModel->setLocale('de');
        $this->assertSame('testValue_en', $this->testModel->name);
    }

    /** @test */
    public function it_provides_a_flog_to_not_return_any_translation_when_getting_an_unknown_locale()
    {
        config()->set('app.fallback_locale', 'en');

        Translatable::fallback(
            fallbackAny: true,
        );

        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');
        $this->testModel->setTranslation('name', 'de', 'testValue_de');
        $this->testModel->save();

        $this->testModel->setLocale('it');
        $this->assertSame('', $this->testModel->getTranslation('name', 'it', false));
    }

    /** @test */
    public function it_will_return_default_fallback_locale_translation_when_getting_an_unknown_locale_with_fallback_any()
    {
        config()->set('app.fallback_locale', 'en');

        Translatable::fallback(
            fallbackAny: true,
        );

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->getTranslation('name', 'fr'));
    }
}
