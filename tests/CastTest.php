<?php

namespace Spatie\Translatable\Test;

class CastTest extends TestCase
{
    /** @var TestModel */
    protected $testModel;

    public function setUp()
    {
        parent::setUp();

        $this->testModel = new class extends TestModel
        {
            /** @var string */
            protected $cast;

            public function getTranslatableAttributes() : array
            {
                return ['name' => $this->cast];
            }

            public function setTestCast(string $cast)
            {
                $this->cast = $cast;

                return $this;
            }

        };
    }

    /**
     * @test
     *
     * @dataProvider castProvider
     */
    public function it_can_return_a_cast_value(string $cast, $setValue, $expectValue)
    {
        $this->testModel
            ->setTestCast($cast)
            ->setTranslation('name', 'en', $setValue);

        $this->testModel->save();

        $this->refreshTestModel();

        $actualValue = $this->testModel
            ->setTestCast($cast)
            ->getTranslation('name', 'en');

        $this->assertSame($expectValue, $actualValue);
    }

    public function castProvider()
    {
        return [
            ['bool', true, true],
            ['bool', null, false],

            ['integer', 123, 123],
            ['integer', null, 0],

            ['float', -1234.5678, -1234.5678],
            ['float', null, 0.0],

            ['array', ['one' => 1, 'two' => 2], ['one' => 1, 'two' => 2]],
            ['array', null, []],
        ];
    }

    protected function refreshTestModel()
    {
        $this->testModel = $this->testModel->fresh();
    }
}
