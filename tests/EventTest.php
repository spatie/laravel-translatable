<?php

namespace Spatie\Translatable\Test;

use Illuminate\Support\Facades\Event;
use Spatie\Translatable\Events\TranslationHasBeenSetEvent;

class EventTest extends TestCase
{
    protected TestModel $testModel;

    public function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->testModel = new TestModel();
    }

    /** @test */
    public function it_will_fire_an_event_when_a_translation_has_been_set()
    {
        $this->testModel->setTranslation('name', 'en', 'testValue_en');

        Event::assertDispatched(TranslationHasBeenSetEvent::class);
    }
}
