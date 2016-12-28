<?php

namespace Milchkuh;

class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * QueryBuilder
     */
    protected $object;

    /**
     * Called before a test run
     */
    public function setUp()
    {
        $this->object = new QueryBuilder();
    }

    /**
     * Called after a test run
     */
    public function tearDown()
    {
    }

    /**
     * @covers Milchkuh\QueryBuilder::init
     */
    public function testInit()
    {
        $this->assertInstanceOf('\Milchkuh\QueryBuilder', $this->object->init());
        $this->assertEquals('', $this->object->getQuery());
    }

    /**
     * @covers Milchkuh\QueryBuilder::setQuery
     */
    public function testSetQuery()
    {
        $expected_result = 'SELECT * FROM db.table';
        $this->object->setQuery('SELECT * FROM db.table');
        $actual_result   = $this->object->getQuery();
        $this->assertEquals($expected_result, $actual_result);
    }

    /**
     * @covers Milchkuh\QueryBuilder::getQuery
     */
    public function testGetQuery()
    {
        $expected_result = '';
        $actual_result   = $this->object->getQuery();
        $this->assertEquals($expected_result, $actual_result);
    }

    /**
     * @covers Milchkuh\QueryBuilder::append
     */
    public function testAppend()
    {
        // Give no second argument for append (default: true)
        $expected_result = 'SELECT * FROM db.table WHERE key = :value';
        $this->object->append('SELECT * FROM db.table')
                ->append(' WHERE key = :value');
        $actual_result   = $this->object->getQuery();
        $this->assertEquals($expected_result, $actual_result);

        // Give true as second argument for append
        $expected_result = 'SELECT * FROM db.table WHERE key = :value';
        $this->object->init()
            ->append('SELECT * FROM db.table')
            ->append(' WHERE key = :value', true);
        $actual_result   = $this->object->getQuery();
        $this->assertEquals($expected_result, $actual_result);

        // Give false as second argument for append
        $expected_result = 'SELECT * FROM db.table';
        $this->object->init()
            ->append('SELECT * FROM db.table')
            ->append(' WHERE key = :value', false);
        $actual_result   = $this->object->getQuery();
        $this->assertEquals($expected_result, $actual_result);

        // Give both true and false as second argument for append
        $expected_result = 'SELECT * FROM db.table WHERE key1 = :value1 AND key3 = :value3';
        $this->object->init()
            ->append('SELECT * FROM db.table')
            ->append(' WHERE key1 = :value1')
            ->append(' AND key2 = :value2', false)
            ->append(' AND key3 = :value3', true);
        $actual_result = $this->object->getQuery();
        $this->assertEquals($expected_result, $actual_result);

        // Give expressions
        $array = [
            'key1' => 'value1',
            'key3' => 'value3'
        ];
        $expected_result = 'SELECT * FROM db.table WHERE key1 = :value1 AND key3 = :value3';
        $this->object->init()
            ->append('SELECT * FROM db.table')
            ->append(' WHERE key1 = :value1')
            ->append(' AND key2 = :value2', isset($array['key2']))
            ->append(' AND key3 = :value3', isset($array['key3']));
        $actual_result = $this->object->getQuery();
        $this->assertEquals($expected_result, $actual_result);
    }
}
