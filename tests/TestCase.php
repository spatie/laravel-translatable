<?php

namespace Spatie\Translatable\Test;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Translatable\TranslatableServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
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
        Schema::create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name')->nullable();
            $table->text('other_field')->nullable();
        });
    }
}
