<?php

namespace Milchkuh;

require __DIR__ . '/vendor/autoload.php';

use Milchkuh\Milchkuh;

class MilchkuhDummyClass
{
    use Milchkuh;

    public function __construct()
    {
        $db_info = [
            'host'       => 'localhost',
            'port'       => 3306,
            'user'       => 'root',
            'pass'       => 'vagrant',
            'db_name'    => 'test',
            'table_name' => 'milchkuh_test'
        ];
        $this->init($db_info);
    }

    public function fetch()
    {
        $query = <<<SQL
SELECT *
  FROM {$this->getDbName()}.{$this->getTableName()}
 WHERE score > :score;
SQL;
        $bind_param = [':score' => 70];
        $records    = [];

        try {
            $records = $this->select($query, $bind_param, 'Milchkuh\\UScore');
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            print_r($e->getTrace());
        }

        return $records;
    }

    public function store()
    {
        $query = <<<SQL
INSERT INTO {$this->getDbName()}.{$this->getTableName()} (
    name, subject, score, del_flag, reg_date, update_date
) VALUES (?,?,?,0,NOW(),NOW())
SQL;
        $bind_param = ['milchkuh', 99, 99];

        $id = null;
        try {
            $this->begin();
            $id = $this->insert($query, $bind_param);
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            echo $e->getMessage() . PHP_EOL;
            echo $e->getQuery() . PHP_EOL;
            print_r($e->getBindParam());
        } catch (\Exception $e) {
            $this->rollBack();
            echo $e->getMessage() . PHP_EOL;
        }

        return $id;
    }

    public function edit($id)
    {
        $query = <<<SQL
UPDATE {$this->getDbName()}.{$this->getTableName()}
   SET score    = score + 1
 WHERE id       = :id
   AND del_flag = 0
SQL;
        $bind_param = [':id' => $id];
        try {
            $this->begin();
            $row_count = $this->update($query, $bind_param);
            $this->commit();
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            echo $e->getQuery() . PHP_EOL;
            print_r($e->getBindParam());
            $this->rollBack();
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            $this->rollBack();
        }

        return $row_count;
    }

    public function remove($id)
    {
        $query = <<<SQL
DELETE FROM {$this->getDbName()}.{$this->getTableName()}
 WHERE id = :id
   AND del_flag = 0
SQL;
        $bind_param = [':id' => $id - 1];

        $row_count = null;
        try {
            $this->begin();
            $row_count = $this->delete($query, $bind_param);
            $this->commit();
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            echo $e->getQuery() . PHP_EOL;
            print_r($e->getBindParam());
            $this->rollBack();
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            $this->rollBack();
        }

        return $row_count;
    }

    public function wait()
    {
        $this->nap(3);
    }
}

class UScore
{
    private $name;
    private $subject;
    private $score;

    // public function __construct($name, $subject, $score)
    // {
    //     $this->name    = $anme;
    //     $this->subject = $subject;
    //     $this->score   = $score;
    // }
}

$milchkuh_dummy = new MilchkuhDummyClass();

$records = $milchkuh_dummy->fetch();
print_r($records);
$id = $milchkuh_dummy->store();
echo $id . PHP_EOL;

var_dump($milchkuh_dummy->wait());
var_dump($milchkuh_dummy->edit($id));
var_dump($milchkuh_dummy->remove($id));

