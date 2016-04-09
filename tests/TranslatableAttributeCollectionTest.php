<?php

namespace Spatie\Translatable\Test;

use Spatie\Translatable\TranslatableAttributeCollection;

class TranslatableAttributeCollectionTest extends TestCase
{
    /** @var TranslatableAttributeCollection */
    protected $translatableAttributeCollection;

    public function setUp()
    {
        parent::setUp();

        $testModel = new class extends TestModel
        {
            public function getTranslatableAttributes() : array
            {
                return ['name' => 'bool'];
            }
        };
        
        $this->translatableAttributeCollection = TranslatableAttributeCollection::createForModel($testModel);
    }

    /** @test */
    public function it_can_determine_if_an_attribute_is_translatable()
    {
        $this->assertTrue($this->translatableAttributeCollection->isTranslatable('name'));
        $this->assertFalse($this->translatableAttributeCollection->isTranslatable('unknown'));
    }

    /** @test */
    public function it_can_determine_the_cast_of_an_attribute()
    {
        $this->assertSame('bool', $this->translatableAttributeCollection->getCast('name'));
    }
}