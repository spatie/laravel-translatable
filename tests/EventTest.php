<?php

use Illuminate\Support\Facades\Event;
use Spatie\Translatable\Events\TranslationHasBeenSetEvent;

uses(TestCase::class);

beforeEach(function () {
    Event::fake();

    $this->testModel = new TestModel();
});

it('will fire an event when a translation has been set', function () {
    $this->testModel->setTranslation('name', 'en', 'testValue_en');

    Event::assertDispatched(TranslationHasBeenSetEvent::class);
});
