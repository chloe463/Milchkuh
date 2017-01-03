<?php

namespace chloe463\Milchkuh;

class BindParamBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param   BindParamBuilder
     */
    protected $object;

    /**
     * Called before a test run
     */
    public function setUp()
    {
        $this->object = new BindParamBuilder();
    }

    /**
     * Called after a test run
     */
    public function tearDown()
    {
        $this->object = null;
    }

    /**
     * @cover   chloe463\Milchkuh\BindParamBuilder::getBindParam
     */
    public function testGetBindParam()
    {
        $expected_result = [];
        $actual_result   = $this->object->getBindParam();
        $this->assertEquals($expected_result, $actual_result);
    }

    /**
     * @cover   chloe463\Milchkuh\BindParamBuilder::init
     */
    public function testInit()
    {
        $expected_result = [];
        $actual_result   = $this->object->init();
        $actual_result   = $this->object->getBindParam();
        $this->assertEquals($expected_result, $actual_result);
    }

    /**
     * @cover   chloe463\Milchkuh\BindParamBuilder::append
     */
    public function testAppend()
    {
        $base = [
            'key1' => 'value1',
            'key3' => 'value3'
        ];

        $expected_reult = [
            'key1'  => 'value1',
            'alias' => 'value3'
        ];

        $this->object->append($base, 'key1');
        $this->object->append($base, 'key2');
        $this->object->append($base, 'key3', 'alias');

        $actual_result = $this->object->getBindParam();

        $this->assertEquals($expected_reult, $actual_result);
    }
}
