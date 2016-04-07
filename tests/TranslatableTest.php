<?php

namespace Spatie\Translatable\Test;

use Spatie\Translatable\Exceptions\Untranslatable;

class TranslatableTest extends TestCase
{
    /** @test */
    public function it_can_save_a_translated_field()
    {
        $model = new TestModel();

        $model->setTranslation('name', 'en', 'testValue_en');
        $model->save();

        $this->assertSame('testValue_en', $model->name);
    }

    /** @test */
    public function it_can_save_multiple_translations()
    {
        $model = new TestModel();

        $model->setTranslation('name', 'en', 'testValue_en');
        $model->setTranslation('name', 'fr', 'testValue_fr');
        $model->save();

        $this->assertSame('testValue_en', $model->name);
        $this->assertSame('testValue_fr', $model->getTranslation('name', 'fr'));
    }

    /** @test */
    public function it_will_return_the_value_of_the_current_locale_when_using_the_property()
    {
        $model = new TestModel();

        $model->setTranslation('name', 'en', 'testValue');
        $model->setTranslation('name', 'fr', 'testValue_fr');
        $model->save();

        app()->setLocale('fr');

        $this->assertSame('testValue_fr', $model->name);
    }

    /** @test */
    public function it_will_return_an_empty_string_when_getting_an_unknown_locale()
    {
        $model = new TestModel();

        $model->setTranslation('name', 'en', 'testValue_en');
        $model->save();

        $this->assertSame('', $model->getTranslation('name', 'de'));
    }

    /** @test */
    public function it_will_return_a_default_locale_when_a_translation_is_not_set()
    {
        $model = new TestModel();

        $model->setTranslation('name', 'en', 'testValue_en');
        $model->save();

        $this->assertSame('testValue_en', $model->getTranslation('name', 'en', 'default'));
        $this->assertSame('default', $model->getTranslation('name', 'de', 'default'));
    }

    /** @test */
    public function it_will_throw_an_exception_when_trying_to_translate_an_untranslatable_field()
    {
        $model = new TestModel();

        $this->expectException(Untranslatable::class);

        $model->setTranslation('untranslated', 'en', 'value');

    }
}
