<?php

namespace Spatie\Translatable\Test;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Translatable\TranslatableServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [TranslatableServiceProvider::class];
    }

    protected function setUpDatabase()
    {
        Schema::dropIfExists('test_models');

        Schema::create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->json('name')->nullable();
            $table->json('other_field')->nullable();
            $table->json('field_with_mutator')->nullable();
            $table->json('nested')->nullable();
        });
    }
}
