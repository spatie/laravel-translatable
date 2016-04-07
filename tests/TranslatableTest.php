<?php

namespace Spatie\Translatable\Test;

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
}
