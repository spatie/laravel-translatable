<?php

namespace Spatie\Translatable\Test;

use Spatie\Translatable\Exceptions\AttributeIsNotTranslatable;

class TranslatableTest extends TestCase
{
    /** @var \Spatie\Translatable\Test\TestModel */
    protected $testModel;

    public function setUp()
    {
        parent::setUp();

        $this->testModel = new TestModel();
    }

    /** @test */
    public function it_will_return_fallback_locale_translation_when_getting_an_unknown_locale()
    {
        $this->app['config']->set('laravel-translatable.fallback_locale', 'en');

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->getTranslation('name', 'fr'));
    }

    /** @test */
    public function it_provides_a_flog_to_not_return_fallback_locale_translation_when_getting_an_unknown_locale()
    {
        $this->app['config']->set('laravel-translatable.fallback_locale', 'en');

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('', $this->testModel->getTranslation('name', 'fr', false));
    }

    /** @test */
    public function it_will_return_fallback_locale_translation_when_getting_an_unknown_locale_and_fallback_is_true()
    {
        $this->app['config']->set('laravel-translatable.fallback_locale', 'en');

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('testValue_en', $this->testModel->getTranslationWithFallback('name', 'fr'));
    }

    /** @test */
    public function it_will_return_an_empty_string_when_getting_an_unknown_locale_and_fallback_is_not_set()
    {
        $this->app['config']->set('laravel-translatable.fallback_locale', '');

        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->save();

        $this->assertSame('', $this->testModel->getTranslationWithoutFallback('name', 'fr'));
    }

    /** @test */
    public function it_will_return_an_empty_string_when_getting_an_unknown_locale_and_fallback_is_empty()
    {
        $this->app['config']->set('translatable.fallback_locale', '');

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
    public function it_can_get_all_translations_for_all_translatable_attributes_in_one_go()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');

        $this->testModel->setTranslation('other_field', 'en', 'testValue_en');
        $this->testModel->setTranslation('other_field', 'fr', 'testValue_fr');
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
        ], $this->testModel->getTranslations());
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
    public function it_can_forget_all_translations()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');
        $this->testModel->setTranslation('name', 'fr', 'testValue_fr');

        $this->testModel->setTranslation('other_field', 'en', 'testValue_en');
        $this->testModel->setTranslation('other_field', 'fr', 'testValue_fr');
        $this->testModel->save();

        $this->assertSame([
            'en' => 'testValue_en',
            'fr' => 'testValue_fr',
        ], $this->testModel->getTranslations('name'));

        $this->assertSame([
            'en' => 'testValue_en',
            'fr' => 'testValue_fr',
        ], $this->testModel->getTranslations('other_field'));

        $this->testModel->forgetAllTranslations('en');

        $this->assertSame([
            'fr' => 'testValue_fr',
        ], $this->testModel->getTranslations('name'));

        $this->assertSame([
            'fr' => 'testValue_fr',
        ], $this->testModel->getTranslations('other_field'));
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
        $testModel = new class() extends TestModel {
            public function getOtherFieldAttribute() : string
            {
                return 'accessorName';
            }
        };

        $this->assertEquals((new $testModel())->otherField, 'accessorName');
    }

    /** @test */
    public function it_can_use_accessors_on_translated_attributes()
    {
        $testModel = new class() extends TestModel {
            public function getNameAttribute($value) : string
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
        $testModel = new class() extends TestModel {
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
    public function it_can_correctly_set_a_field_when_a_mutator_is_defined()
    {
        $testModel = (new class() extends TestModel {
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
        $testModel = (new class() extends TestModel {
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
    public function it_can_translate_a_field_based_on_the_translations_of_another_one()
    {
        $testModel = (new class() extends TestModel {
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
        $testModel = (new class() extends TestModel {
            public function setAttributesExternally(array $attributes)
            {
                $this->attributes = $attributes;
            }
        });

        $testModel->setAttributesExternally(['name' => json_encode(null), 'other_field' => null]);

        $this->assertEquals('', $testModel->name);
        $this->assertEquals('', $testModel->other_field);
    }
}
