<?php

namespace Spatie\Translatable\Test;

class MySQLWhereTest extends MySQLTestCase
{
    /** @var \Spatie\Translatable\Test\TestModel */
    protected $testModel;

    public function setUp()
    {
        parent::setUp();

        $this->testModel = new TestModel();
    }

    /** @test */
    public function it_is_compatible_with_where_on_translatable_attributes()
    {
        $this->clearAndcreateFakeRecords(3);

        $this->assertSame([
            'en_testValue_0'
        ], $this->testModel->where('name->en', 'en_testValue_0')->pluck('name')->toArray());
    }

    /** @test */
    public function it_is_compatible_with_wherein_on_translatable_attributes()
    {
        $this->clearAndcreateFakeRecords(3);

        $this->assertSame([
            'en_testValue_0',
            'en_testValue_1'
        ], $this->testModel->whereIn('name->en', [
                'en_testValue_0',
                'en_testValue_1'
            ])->pluck('name')->toArray()
        );
    }

    /**
     * Clear old records and create new records for testing
     * @param int $noOfRecords
     */
    protected function clearAndcreateFakeRecords($noOfRecords)
    {
        $this->testModel::truncate();

        for ($i = 0; $i < $noOfRecords; $i++) {

            (new $this->testModel)->setTranslation('name', 'en', "en_testValue_{$i}")
                ->setTranslation('name', 'fr', 'fr_testValue_{$i}')
                ->save();
        }
    }
}