<?php

namespace Spatie\Translatable\Test;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
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
