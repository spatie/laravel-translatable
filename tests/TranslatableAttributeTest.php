<?php

namespace Spatie\Translatable\Test;

use Spatie\Translatable\TranslatableAttribute;

class TranslatableAttributeTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider keyValueProvider
     */
    public function it_can_determine_a_cast_and_a_type($key, $value, $name, $cast)
    {
        $translatableAttribute = new TranslatableAttribute($key, $value);

        $this->assertSame($name, $translatableAttribute->name);
        $this->assertSame($cast, $translatableAttribute->cast);
    }

    public function keyValueProvider()
    {
        return [
            [null, 'name', 'name', 'string'],
            ['name', 'bool', 'name', 'bool'],
            ['name', 'integer', 'name', 'integer'],
            ['name', 'float', 'name', 'float'],
            ['name', 'array', 'name', 'array'],
        ];
    }
}