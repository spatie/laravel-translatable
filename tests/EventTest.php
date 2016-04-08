<?php

namespace Spatie\Translatable\Test;

use Spatie\Translatable\Events\TranslationHasBeenSet;

class EventTest extends TestCase
{
    /** @var TestModel */
    protected $testModel;

    public function setUp()
    {
        parent::setUp();

        $this->testModel = new TestModel();
    }

    /** @test */
    public function it_will_fire_an_event_when_a_translation_has_been_set()
    {
        $this->expectsEvents(TranslationHasBeenSet::class);

        $this->testModel->setTranslation('name', 'en', 'testValue_en');

        $this->assertCount(1, $this->firedEvents);

        $this->assertInstanceOf(TestModel::class, $this->firedEvents[0]->model);
        $this->assertSame('en', $this->firedEvents[0]->locale);
        $this->assertSame('name', $this->firedEvents[0]->attributeName);
        $this->assertSame('', $this->firedEvents[0]->oldValue);
        $this->assertSame('testValue_en', $this->firedEvents[0]->newValue);
    }
}
