<?php

namespace Spatie\Translatable\Test;

use Spatie\Translatable\Exceptions\InvalidCast;
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

    /** @test */
    public function it_will_throw_an_exception_when_using_an_invalid_cast()
    {
        $this->expectException(InvalidCast::class);

        new TranslatableAttribute('name', 'unknown cast');
    }
}