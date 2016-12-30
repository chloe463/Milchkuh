<?php

namespace chloe463\Milchkuh;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Milchkuh
     */
    protected $object;

    /**
     * Called before a test run
     */
    public function setUp()
    {
        $this->object = new Exception("A test exception message", "SELECT 1", ['key' => 'value'], 100);
    }

    /**
     * Called after a test run
     */
    public function tearDown()
    {
        $this->object = null;
    }

    /**
     * @covers chloe463\Milchkuh\Exception::getQuery
     */
    public function testGetQuery()
    {
        $expected_result = 'SELECT 1';
        $actual_result   = $this->object->getQuery();

        $this->assertEquals($expected_result, $actual_result);
    }

    /**
     * @covers chloe463\Milchkuh\Exception::getBindParam
     */
    public function testGetBindParam()
    {
        $expected_restul = ['key' => 'value'];
        $actual_result   = $this->object->getBindParam();

        $this->assertEquals($expected_restul, $actual_result);
    }

    /**
     * @covers chloe463\Milchkuh\Exception::getBindParamAsJson
     */
    public function testGetBindParamAsJson()
    {
        $expected_restul = '{"key":"value"}';
        $actual_result   = $this->object->getBindParamAsJson();

        $this->assertEquals($expected_restul, $actual_result);
    }
}
