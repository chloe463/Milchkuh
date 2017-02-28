<?php

namespace chloe463\Milchkuh;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Logger
     */
    protected $object;

    /**
     * Called before a test run
     */
    public function setUp()
    {
        $this->object = new Logger(__DIR__ . '/logs/test.log');
    }

    /**
     * Called after a test run
     */
    public function tearDown()
    {
        $this->object = null;
        $file = __DIR__ . '/logs/test.log';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * @covers chloe463\Milchkuh\Logger::setLogFilePath
     */
    public function testSetLogFilePath()
    {
        $log_file_path   = __DIR__ . '/logs/test.log';
        $expected_result = __DIR__ . '/logs/test.log';
        $this->object->setLogFilePath(__DIR__. '/logs/test.log');
        $actual_result   = $this->object->getLogFilePath();

        $this->assertEquals($expected_result, $actual_result);
    }

    /**
     * @covers chloe463\Milchkuh\Logger::setLogFilePath
     */
    public function testSetLogFilePath_throwsException()
    {
        $expected_result = 'No such directory: /path/to/no-such-directory';
        $log_file_path = '/path/to/no-such-directory/file';
        try {
            $this->object->setLogFilePath($log_file_path);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals($expected_result, $e->getMessage());
            $this->assertEquals(11, $e->getCode());
        }
    }

    /**
     * @covers chloe463\Milchkuh\Logger::getLogFilePath
     */
    public function testGetLogFilePath()
    {
        $expected_result = __DIR__ . '/logs/test.log';
        $actual_result   = $this->object->getLogFilePath();

        $this->assertEquals($expected_result, $actual_result);
    }

    /**
     * @covers chloe463\Milchkuh\Logger::log
     */
    public function testLog()
    {
        $this->object->log('SELECT * FROM db.table');
        $this->assertFileExists(__DIR__ . '/logs/test.log');
    }

    /**
     * @covers chloe463\Milchkuh\Logger::buildMessage
     */
    public function testBuildMessage()
    {
        $expected_result = '/^\[[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}\]\s\[[0-9]+\]\sSELECT \* FROM db\.table$/';
        $actual_result   = $this->object->buildMessage('SELECT * FROM db.table');
        $this->assertRegExp($expected_result, $actual_result);

        $query           = 'SELECT * FROM db.table WHERE key = ? AND column = ?';
        $bind_param      = [123, 456];
        $expected_result = '/^\[[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}\]\s\[[0-9]+\]\sSELECT \* FROM db\.table WHERE key = 123 AND column = 456$/';
        $actual_result   = $this->object->buildMessage($query, $bind_param);
        $this->assertRegExp($expected_result, $actual_result);

        $query           = 'SELECT * FROM db.table WHERE key = :key AND column = :column';
        $bind_param      = [':key' => 123, ':column' => 456];
        $expected_result = '/^\[[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}\]\s\[[0-9]+\]\sSELECT \* FROM db\.table WHERE key = 123 AND column = 456$/';
        $actual_result   = $this->object->buildMessage($query, $bind_param);
        $this->assertRegExp($expected_result, $actual_result);

        $query           = 'SELECT * FROM db.table WHERE key = :key AND column = :column';
        $bind_param      = ['key' => 123, 'column' => 456];
        $expected_result = '/^\[[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}\]\s\[[0-9]+\]\sSELECT \* FROM db\.table WHERE key = 123 AND column = 456$/';
        $actual_result   = $this->object->buildMessage($query, $bind_param);
        $this->assertRegExp($expected_result, $actual_result);
    }

    /**
     * @covers chloe463\Milchkuh\Logger::buildMessage
     */
    public function testBuildMessage_withMissingKey()
    {
        $query           = 'SELECT * FROM db.table WHERE key = ? AND column = ?';
        $bind_param      = [123];
        $expected_result = '/^\[[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}\]\s\[[0-9]+\]\sSELECT \* FROM db\.table WHERE key = 123 AND column = \?\n$/';
        $actual_result   = $this->object->buildMessage($query, $bind_param);
        $this->assertRegExp($expected_result, $actual_result);

        $query           = 'SELECT * FROM db.table WHERE key = :key AND column = :column';
        $bind_param      = [':column' => 456];
        $expected_result = '/^\[[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}\]\s\[[0-9]+\]\sSELECT \* FROM db\.table WHERE key = :key AND column = 456\n$/';
        $actual_result   = $this->object->buildMessage($query, $bind_param);
        $this->assertRegExp($expected_result, $actual_result);
    }

    /**
     * @covers chloe463\Milchkuh\Logger::replaceQuestion2Param
     */
    public function testReplaceQuestion2Param()
    {
        // With empty bind parameters
        $query           = 'SELECT * FROM db.table WHERE key = ? AND column = ?';
        $bind_param      = [];
        $expected_result = 'SELECT * FROM db.table WHERE key = ? AND column = ?';
        $actual_result   = $this->object->replaceQuestion2Param($query, $bind_param);
        $this->assertEquals($expected_result, $actual_result);

        // With bind parameters
        $query           = 'SELECT * FROM db.table WHERE key = ? AND column = ?';
        $bind_param      = [123, 456];
        $expected_result = 'SELECT * FROM db.table WHERE key = 123 AND column = 456';
        $actual_result   = $this->object->replaceQuestion2Param($query, $bind_param);
        $this->assertEquals($expected_result, $actual_result);
    }

    /**
     * @covers chloe463\Milchkuh\Logger::replaceKeyword2Param
     */
    public function testReplaceKeyword2Param()
    {
        // With empty bind parameters
        $query           = 'SELECT * FROM db.table WHERE key = :key AND column = :column';
        $bind_param      = [];
        $expected_result = 'SELECT * FROM db.table WHERE key = :key AND column = :column';
        $actual_result   = $this->object->replaceKeyword2Param($query, $bind_param);
        $this->assertEquals($expected_result, $actual_result);

        // With bind parameters
        $query           = 'SELECT * FROM db.table WHERE key = :key AND column = :column';
        $bind_param      = [':key' => 123, ':column' => 456];
        $expected_result = 'SELECT * FROM db.table WHERE key = 123 AND column = 456';
        $actual_result   = $this->object->replaceKeyword2Param($query, $bind_param);
        $this->assertEquals($expected_result, $actual_result);

        // With bind parameters (no colons)
        $query           = 'SELECT * FROM db.table WHERE key = :key AND column = :column';
        $bind_param      = ['key' => 123, 'column' => 456];
        $expected_result = 'SELECT * FROM db.table WHERE key = 123 AND column = 456';
        $actual_result   = $this->object->replaceKeyword2Param($query, $bind_param);
        $this->assertEquals($expected_result, $actual_result);
    }
}
