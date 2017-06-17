<?php

namespace Spatie\Translatable\Test;

use Spatie\Translatable\Exceptions\AttributeIsNotTranslatable;
use Spatie\Translatable\Test\Stubs\TestModel;

class TranslatableTest extends TestCase
{
    /** @var \Spatie\Translatable\Test\Stubs\TestModel */
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
        $this->app['config']->set('laravel-translatable.fallback_locale', '');

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
            public function setNameAttribute($value) : string
            {
                return "I just mutated {$value}";
            }
        };

        $testModel->setTranslation('name', 'en', 'testValue_en');

        $this->assertEquals($testModel->name, 'I just mutated testValue_en');
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
}
