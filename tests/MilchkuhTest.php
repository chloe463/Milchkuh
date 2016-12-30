<?php

namespace Milchkuh;

/**
 * Milchkuh_Dummy class
 * Since Milchkuh is a trait, we need a dummy class to test.
 */
class Milchkuh_dummy
{
    use Milchkuh;

    public function __construct()
    {
        $connection_info = [
            'host'       => $_ENV['DB_HOST'],
            'port'       => $_ENV['DB_PORT'],
            'user'       => $_ENV['DB_USER'],
            'pass'       => $_ENV['DB_PASS'],
            'db_name'    => $_ENV['DB_NAME'],
            'table_name' => $_ENV['DB_TABLE']
        ];
        $this->init($connection_info);
    }
}

/**
 * A dummy class for testSelect_mapResult2Class
 */
class BattleShip
{
    public $name;
    public $nick_name;
    public $del_flag;
    public $reg_date;
    public $update_date;
}

/**
 * Test class
 */
class MilchkuhTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Milchkuh
     */
    protected $object;

    /**
     * @var integer
     */
    protected $test_record_id;

    /**
     * Called before a test run
     */
    public function setUp()
    {
        $this->object = new Milchkuh_dummy();

        $query = <<<SQL
INSERT INTO {$_ENV['DB_NAME']}.{$_ENV['DB_TABLE']} (
    name, nick_name, del_flag, reg_date, update_date
) VALUES (:name, :nick_name, 0, NOW(), NOW())
SQL;
        $new_record = [
            ':name'      => 'I-401',
            ':nick_name' => 'Iona'
        ];
        $dbh = new \PDO(
            sprintf("mysql:dbname=%s;host=%s;port=%s", $_ENV['DB_NAME'], $_ENV['DB_HOST'], $_ENV['DB_PORT']),
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']
        );
        $statement = $dbh->prepare($query);
        $statement->execute($new_record);
        $this->test_record_id = $dbh->lastInsertId();
    }

    /**
     * Called after a test run
     */
    public function tearDown()
    {
        $query = "DELETE FROM {$_ENV['DB_NAME']}.{$_ENV['DB_TABLE']}";
        $dbh   = new \PDO(
            sprintf("mysql:dbname=%s;host=%s;port=%s", $_ENV['DB_NAME'], $_ENV['DB_HOST'], $_ENV['DB_PORT']),
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']
        );
        $statement = $dbh->prepare($query);
        $statement->execute([]);
        $this->object = null;
    }

    /**
     * @covers Milchkuh\Milchkuh::getConnectionInfo
     */
    public function testGetConnectionInfo()
    {
        $expected_result = [
            'host'       => $_ENV['DB_HOST'],
            'port'       => $_ENV['DB_PORT'],
            'user'       => $_ENV['DB_USER'],
            'pass'       => $_ENV['DB_PASS'],
            'db_name'    => $_ENV['DB_NAME'],
            'table_name' => $_ENV['DB_TABLE']
        ];
        $actual_result = $this->object->getConnectionInfo();
        $this->assertEquals($expected_result, $actual_result);
    }

    /**
     * @covers Milchkuh\Milchkuh::getDbName
     */
    public function testGetDbName()
    {
        $expected_result = $_ENV['DB_NAME'];
        $actual_result   = $this->object->getDbName();
        $this->assertEquals($expected_result, $actual_result);
    }

    /**
     * @covers Milchkuh\Milchkuh::setDbName
     */
    public function testSetDbName()
    {
        $other_db = 'OTHER_DB';
        $this->object->setDbName($other_db);
        $this->assertEquals($other_db, $this->object->getDbName());
    }

    /**
     * @covers Milchkuh\Milchkuh::getTableName
     */
    public function testGetTableName()
    {
        $expected_result = $_ENV['DB_TABLE'];
        $actual_result   = $this->object->getTableName();
        $this->assertEquals($expected_result, $actual_result);
    }

    /**
     * @covers Milchkuh\Milchkuh::setTableName
     */
    public function testSetTableName()
    {
        $other_table = 'OTHER_TABLE';
        $this->object->setTableName($other_table);
        $this->assertEquals($other_table, $this->object->getTableName());
    }

    /**
     * @covers Milchkuh\Milchkuh::getDbh
     */
    public function testGetDbh()
    {
        $this->assertNull($this->object->getDbh());

        $this->object->connect();
        $this->assertInstanceOf('\PDO', $this->object->getDbh());
        $this->object->disconnect(null);
    }

    /**
     * @covers Milchkuh\Milchkuh::getLastInsertId
     *
     * This test must be executed after Milchkuh\Milchkuh::insert()
     */
    public function testGetLastInsertId()
    {
        $this->assertNull($this->object->getLastInsertId());

        $query = <<<SQL
INSERT INTO {$this->object->getDbName()}.{$this->object->getTableName()} (
    name, nick_name, del_flag, reg_date, update_date
) VALUES (:name, :nick_name, 0, NOW(), NOW())
SQL;
        $bind_param = [
            ':name'      => 'Yamato',
            ':nick_name' => 'Yamato'
        ];
        $this->object->insert($query, $bind_param);
        $actual_result = $this->object->getLastInsertId();
        $this->assertNotNull($this->object->getLastInsertId());
        $this->assertGreaterThanOrEqual(1, $this->object->getLastInsertId());
    }

    /**
     * @covers Milchkuh\Milchkuh::getRowCount
     */
    public function testGetRowCount()
    {
        $this->assertNull($this->object->getRowCount());

        $query = <<<SQL
SELECT * FROM {$this->object->getDbName()}.{$this->object->getTableName()}
SQL;
        $this->object->select($query, []);
        $this->assertNotNull($this->object->getRowCount());
        $this->assertGreaterThanOrEqual(1, $this->object->getRowCount());
    }

    /**
     * @covers Milchkuh\Milchkuh::init
     */
    public function testInit()
    {
        $connection_info = [
            'host'       => $_ENV['DB_HOST'],
            'port'       => $_ENV['DB_PORT'],
            'user'       => $_ENV['DB_USER'],
            'pass'       => $_ENV['DB_PASS'],
            'db_name'    => $_ENV['DB_NAME'],
            'table_name' => $_ENV['DB_TABLE']
        ];
        $this->object->init($connection_info);

        $this->assertEquals($connection_info, $this->object->getConnectionInfo());
        $this->assertEquals($_ENV['DB_NAME'], $this->object->getDbName());
        $this->assertEquals($_ENV['DB_TABLE'], $this->object->getTableName());
    }

    /**
     * @covers Milchkuh\Milchkuh::validateConnectionInfo
     */
    public function testValidateConnectionInfo()
    {
        $connection_info = [
            'host'       => $_ENV['DB_HOST'],
            'port'       => $_ENV['DB_PORT'],
            'user'       => $_ENV['DB_USER'],
            'pass'       => $_ENV['DB_PASS'],
            'db_name'    => $_ENV['DB_NAME'],
            'table_name' => $_ENV['DB_TABLE']
        ];

        try {
            $this->assertTrue($this->object->validateConnectionInfo($connection_info));
        } catch (Exception $e) {
            $this->fail();
        }
    }

    /**
     * @covers Milchkuh\Milchkuh::validateConnectionInfo
     */
    public function testValidateConnectionInfo_throwsException()
    {
        $connection_info = [];

        // Missing: all parameters
        try {
            $this->object->validateConnectionInfo([]);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('Some parameters are missing(host,port,user,pass,db_name)', $e->getMessage());
            $this->assertEquals(1, $e->getCode());
        }

        // Missing: port, user, pass, db_name 
        try {
            $this->object->validateConnectionInfo([
                'host' => 'localhost',
            ]);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('Some parameters are missing(port,user,pass,db_name)', $e->getMessage());
            $this->assertEquals(1, $e->getCode());
        }

        // Missing: user, pass, db_name 
        try {
            $this->object->validateConnectionInfo([
                'host' => 'localhost',
                'port' => 3306
            ]);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('Some parameters are missing(user,pass,db_name)', $e->getMessage());
            $this->assertEquals(1, $e->getCode());
        }

        // Missing: pass, db_name 
        try {
            $this->object->validateConnectionInfo([
                'host' => 'localhost',
                'port' => 3306,
                'user' => 'user'
            ]);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('Some parameters are missing(pass,db_name)', $e->getMessage());
            $this->assertEquals(1, $e->getCode());
        }

        // Missing: db_name 
        try {
            $this->object->validateConnectionInfo([
                'host' => 'localhost',
                'port' => 3306,
                'user' => 'user',
                'pass' => 'pass'
            ]);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('Some parameters are missing(db_name)', $e->getMessage());
            $this->assertEquals(1, $e->getCode());
        }
    }

    /**
     * @covers Milchkuh\Milchkuh::connect
     */
    public function testConnect()
    {
        $this->assertInstanceOf('\PDO', $this->object->connect());
        $this->assertInstanceOf('\PDO', $this->object->connect());
        $this->object->disconnect(null);
    }

    /**
     * @covers Milchkuh\Milchkuh::disconnect
     */
    public function testDisconnect()
    {
        $this->object->connect();
        $this->object->disconnect(null);
        $this->assertNull($this->object->getDbh());

        $this->object->begin();
        $this->object->disconnect(null);
        $this->assertInstanceOf('\PDO', $this->object->getDbh());
        $this->object->rollBack();
        $this->object->disconnect(null);
        $this->assertNull($this->object->getDbh());
    }

    /**
     * @covers Milchkuh\Milchkuh::begin
     * @covers Milchkuh\Milchkuh::commit
     * @covers Milchkuh\Milchkuh::rollBack
     */
    public function testTransaction()
    {
        $this->assertTrue($this->object->begin());
        $this->assertTrue($this->object->getDbh()->inTransaction());
        $this->assertTrue($this->object->commit());

        $this->assertTrue($this->object->begin());
        $this->assertTrue($this->object->getDbh()->inTransaction());
        $this->assertTrue($this->object->rollBack());
    }

    /**
     * @covers Milchkuh\Milchkuh::begin
     * @covers Milchkuh\Milchkuh::commit
     * @covers Milchkuh\Milchkuh::rollBack
     */
    public function testTransaction_throwsException()
    {
        $this->assertTrue($this->object->begin());
        $this->assertTrue($this->object->getDbh()->inTransaction());

        try {
            $this->object->begin();
        } catch (Exception $e) {
            $this->assertEquals("Active transaction already exists", $e->getMessage());
            $this->assertEquals(7, $e->getCode());
        }
        $this->assertTrue($this->object->rollBack());
    }

    /**
     * @covers Milchkuh\Milchkuh::inTransaction
     */
    public function testInTransaction()
    {
        $this->assertFalse($this->object->inTransaction());

        $this->object->begin();
        $this->assertTrue($this->object->inTransaction());
        $this->object->rollBack();
    }

    /**
     * @covers Milchkuh\Milchkuh::commit
     */
    public function testCommit()
    {
        $this->assertTrue($this->object->begin());
        $this->assertTrue($this->object->getDbh()->inTransaction());
        $this->assertTrue($this->object->commit());
    }

    /**
     * @covers Milchkuh\Milchkuh::commit
     */
    public function testCommit_throwsException()
    {
        try {
            $this->object->commit();
        } catch (Exception $e) {
            $this->assertEquals("No active transaction exists", $e->getMessage());
            $this->assertEquals(8, $e->getCode());
        }
    }

    /**
     * @covers Milchkuh\Milchkuh::rollBack
     */
    public function testRollBak()
    {
        $this->assertTrue($this->object->begin());
        $this->assertTrue($this->object->getDbh()->inTransaction());
        $this->assertTrue($this->object->rollBack());
    }

    /**
     * @covers Milchkuh\Milchkuh::rollBack
     */
    public function testRollBack_throwsException()
    {
        try {
            $this->object->rollBack();
        } catch (Exception $e) {
            $this->assertEquals("No active transaction exists", $e->getMessage());
            $this->assertEquals(8, $e->getCode());
        }
    }

    /**
     * @covers Milchkuh\Milchkuh::buildDsn
     */
    public function testBuildDsn()
    {
        $expected_result = sprintf("mysql:dbname=%s;host=%s;port=%s", $_ENV['DB_NAME'], $_ENV['DB_HOST'], $_ENV['DB_PORT']);
        $actual_result   = $this->object->buildDsn();

        $this->assertEquals($expected_result, $actual_result);
    }

    /**
     * @covers Milchkuh\Milchkuh::validateQuery
     */
    public function testValidateQuery()
    {
        $this->assertTrue($this->object->validateQuery('SELECT 1', 'SELECT'));
        $this->assertTrue($this->object->validateQuery('select 1', 'SELECT'));

        $this->assertFalse($this->object->validateQuery('INSERT 1', 'SELECT'));
        $this->assertFalse($this->object->validateQuery('insert 1', 'SELECT'));
    }

    /**
     * @covers Milchkuh\Milchkuh::prepare
     */
    public function testPrepare()
    {
        $actual_result = $this->object->prepare('SELECT 1');
        $this->assertInstanceOf('\PDOStatement', $actual_result);
    }

    /**
     * @covers Milchkuh\Milchkuh::execute
     */
    public function testExecute()
    {
        $statement     = $this->object->prepare('SELECT 1');
        $actual_result = $this->object->execute($statement, []);
        $this->assertTrue($actual_result);

        try {
            $statement = $this->object->prepare('SELECT ?');
            $this->object->execute($statement, []);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals(9, $e->getCode());
            $this->assertRegexp('/Failed to execute SQL\s\-\sSQLSTATE\[[^ ]+\]:\s.*/', $e->getMessage());
            $this->assertEquals('', $e->getQuery());
            $this->assertEquals([], $e->getBindParam());
            $this->assertEquals('[]', $e->getBindParamAsJson());
        }
    }

    /**
     * @covers Milchkuh\Milchkuh::insert
     */
    public function testInsert()
    {
        $query = <<<SQL
INSERT INTO {$this->object->getDbName()}.{$this->object->getTableName()} (
    name, nick_name, del_flag, reg_date, update_date
) VALUES (:name, :nick_name, 0, NOW(), NOW())
SQL;
        $bind_param = [
            ':name'      => 'I-401',
            ':nick_name' => 'Iona'
        ];

        $id = null;
        try {
            $id = $this->object->insert($query, $bind_param);
        } catch (Exception $e) {
            $this->fail();
        }

        $this->assertGreaterThanOrEqual(1, $id);
    }

    /**
     * @covers Milchkuh\Milchkuh::insert
     */
    public function testInsert_throwsException()
    {
        // Pass Non-INSERT query to Milchkuh\Milchkuh::insert
        try {
            $this->object->insert('SELECT 1', []);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals(2, $e->getCode());
            $this->assertEquals('Non-INSERT query is given to Milchkuh\Milchkuh::insert', $e->getMessage());
        }

        // Pass Insert query and empty bind parameter array
        try {
            $query = <<<SQL
INSERT INTO {$this->object->getDbName()}.{$this->object->getTableName()} (
    name, nick_name, del_flag, reg_date, update_date
) VALUES (:name, :nick_name, 0, NOW(), NOW())
SQL;
            $this->object->insert($query, []);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals(9, $e->getCode());
            $this->assertRegexp('/Failed to execute SQL\s\-\sSQLSTATE\[[^ ]+\]:\s.*/', $e->getMessage());
        }
    }

    /**
     * @covers Milchkuh\Milchkuh::select
     */
    public function testSelect()
    {
        $query = <<<SQL
SELECT name, nick_name, del_flag, reg_date, update_date
  FROM {$this->object->getDbName()}.{$this->object->getTableName()}
 WHERE del_flag = 0
SQL;
        $bind_param = [];
        $records    = [];
        try {
            $records = $this->object->select($query, $bind_param);
        } catch (Exception $e) {
            $this->fail();
            var_dump($e);
        }

        $this->assertNotNull($records);
        $record = reset($records);
        $this->assertEquals('I-401', $record['name']);
        $this->assertEquals('Iona', $record['nick_name']);
        $this->assertEquals(0,  $record['del_flag']);
        $this->assertRegexp('/[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}/', $record['reg_date']);
        $this->assertRegexp('/[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}/', $record['update_date']);
    }

    /**
     * @covers Milchkuh\Milchkuh::select
     */
    public function testSelect_mapResult2Class()
    {
        $query = <<<SQL
SELECT name, nick_name, del_flag, reg_date, update_date
  FROM {$this->object->getDbName()}.{$this->object->getTableName()}
 WHERE del_flag = 0
SQL;
        $bind_param = [];
        $records    = [];
        try {
            $records = $this->object->select($query, $bind_param, 'Milchkuh\BattleShip');
        } catch (Exception $e) {
            $this->fail();
            var_dump($e);
        }

        $this->assertNotNull($records);
        $record = reset($records);
        $this->assertEquals('I-401', $record->name);
        $this->assertEquals('Iona', $record->nick_name);
        $this->assertEquals(0,  $record->del_flag);
        $this->assertRegexp('/[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}/', $record->reg_date);
        $this->assertRegexp('/[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}/', $record->update_date);
    }

    /**
     * @covers Milchkuh\Milchkuh::select
     */
    public function testSelect_throwsException()
    {
        // Pass Non-SELECT query to Milchkuh\Milchkuh::select
        $query = <<<SQL
INSERT INTO {$this->object->getDbName()}.{$this->object->getTableName()} (
    name, nick_name, del_flag, reg_date, update_date
) VALUES (:name, :nick_name, 0, NOW(), NOW())
SQL;
        try {
            $this->object->select($query, []);
        } catch (Exception $e) {
            $this->assertEquals(2, $e->getCode());
            $this->assertEquals('Non-SELECT query is given to Milchkuh\Milchkuh::select', $e->getMessage());
        }

        // Pass SELECT query and empty bind parameter array
        $query = <<<SQL
SELECT * FROM {$this->object->getDbName()}.{$this->object->getTableName()}
 WHERE name = :name
   AND del_flag = 0
SQL;
        $bind_param = [];
        try {
            $records = $this->object->select($query, $bind_param);
        } catch (Exception $e) {
            $this->assertEquals(9, $e->getCode());
            $this->assertRegexp('/Failed to execute SQL\s\-\sSQLSTATE\[[^ ]+\]:\s.*/', $e->getMessage());
        }
    }

    /**
     * @covers Milchkuh\Milchkuh::update
     */
    public function testUpdate()
    {
        $query = <<<SQL
UPDATE {$this->object->getDbName()}.{$this->object->getTableName()}
   SET name = :name
 WHERE id = :id
SQL;
        $bind_param = [
            ':name' => 'I401',
            ':id'   => $this->test_record_id
        ];
        $row_count  = 0;
        try {
            $row_count = $this->object->update($query, $bind_param);
        } catch (Exception $e) {
            var_dump($e);
            $this->fail();
        }

        $this->assertEquals(1, $row_count);
    }

    /**
     * @covers Milchkuh\Milchkuh::update
     */
    public function testUpdate_throwsException()
    {
        // Pass Non-UPDATE query to Milchkuh\Milchkuh::update
        $query = "SELECT 1";
        try {
            $this->object->update($query, []);
        } catch (Exception $e) {
            $this->assertEquals(2, $e->getCode());
            $this->assertEquals('Non-UPDATE query is given to Milchkuh\Milchkuh::update', $e->getMessage());
        }

        // Pass UPDATE query and empty bind parameter array
        $query = <<<SQL
UPDATE {$this->object->getDbName()}.{$this->object->getTableName()}
   SET name = :name
 WHERE id = :id
SQL;
        $bind_param = [];

        $row_count  = 0;
        try {
            $row_count = $this->object->update($query, $bind_param);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals(9, $e->getCode());
            $this->assertRegexp('/Failed to execute SQL\s\-\sSQLSTATE\[[^ ]+\]:\s.*/', $e->getMessage());
        }

        $this->assertEquals(0, $row_count);
    }

    /**
     * @covers Milchkuh\Milchkuh::delete
     *
     * @depends testUpdate
     */
    public function testDelete()
    {
        $query = <<<SQL
DELETE FROM {$this->object->getDbName()}.{$this->object->getTableName()}
 WHERE id = :id
SQL;
        $bind_param = [':id' => $this->test_record_id];
        $row_count  = 0;
        try {
            $row_count = $this->object->delete($query, $bind_param);
        } catch (Exception $e) {
            var_dump($e);
            $this->fail();
        }

        $this->assertEquals(1, $row_count);
    }

    /**
     * @covers Milchkuh\Milchkuh::delete
     */
    public function testDelete_throwsException()
    {
        // Pass Non-UPDATE query to Milchkuh\Milchkuh::delete
        $query = "SELECT 1";
        try {
            $this->object->delete($query, []);
        } catch (Exception $e) {
            $this->assertEquals(2, $e->getCode());
            $this->assertEquals('Non-DELETE query is given to Milchkuh\Milchkuh::delete', $e->getMessage());
        }

        // Pass UPDATE query and empty bind parameter array
        $query = <<<SQL
DELETE FROM {$this->object->getDbName()}.{$this->object->getTableName()}
 WHERE id = :id
SQL;
        $bind_param = [];

        $row_count  = 0;
        try {
            $row_count = $this->object->delete($query, $bind_param);
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals(9, $e->getCode());
            $this->assertRegexp('/Failed to execute SQL\s\-\sSQLSTATE\[[^ ]+\]:\s.*/', $e->getMessage());
        }

        $this->assertEquals(0, $row_count);
    }

    /**
     * @covers Milchkuh\Milchkuh::call
     */
    public function testCall()
    {
        $query = <<<SQL
CALL Milchkuh_Test_Procedure();
SQL;
        $bind_param = [];
        $records    = [];
        try {
            $records = $this->object->call($query, $bind_param);
        } catch (Exception $e) {
            var_dump($e);
            $this->fail();
        }
        $this->assertNotNull($records);
    }

    /**
     * @covers Milchkuh\Milchkuh::call
     */
    public function testCall_throwsException()
    {
        // Pass Non-CALL query to Milchkuh\Milchkuh::call
        $query = 'SELECT 1';
        try {
            $this->object->call($query, []);
        } catch (Exception $e) {
            $this->assertEquals(2, $e->getCode());
            $this->assertEquals('Non-CALL query is given to Milchkuh\Milchkuh::call', $e->getMessage());
        }

        // Pass undefined stored procedure
        $query = 'CALL UNDEFINED_PRODECURE()';
        try {
            $this->object->call($query, []);
        } catch (Exception $e) {
            $this->assertEquals(9, $e->getCode());
            $this->assertRegexp('/Failed to execute SQL\s\-\sSQLSTATE\[[^ ]+\]:\s.*/', $e->getMessage());
        }
    }

    /**
     * @covers Milchkuh\Milchkuh::nap
     */
    public function testNap()
    {
        $from = (new \DateTime())->getTimeStamp();
        $this->object->nap(1);
        $to   = (new \DateTime())->getTimeStamp();

        $this->assertGreaterThanOrEqual(1, $to - $from);
    }
}
