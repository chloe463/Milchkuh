# Milchkuh

## Summary

This is a wrapper class (trait) of PDO.

You can access MySQL database with simple APIs.

## Usage

**Initialize**

```php
<?php

use Milchkuh\Milchkuh;

class AwesomeClass
{
    use Milchkuh;

    public function __construct()
    {
        $db_info = [
            // These are essential keys
            'host'       => 'localhost',
            'port'       => 3306,
            'user'       => 'root',
            'pass'       => 'vagrant',
            'db_name'    => 'test',

            // table_name is optional
            'table_name' => 'milchkuh_test'
        ];
        $this->init($db_info);
    }

```

**SELECT**

```php

    public function fetch()
    {
        $query = <<<SQL
SELECT * FROM {$this->getDbName()}.{$this->getTableName()}
 WHERE score > :score;
SQL;
        $bind_param = [':score' => 70];
        $records    = [];

        try {
            $records = $this->select($query, $bind_param, 'Milchkuh\\UScore');
        } catch (\Exception $e) {
            // Handle exception
        }

        return $records;
    }

```

**INSERT**

```php

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
        } catch (\Exception $e) {
            // Handle exception
        }

        return $id;
    }

```

**UPDATE**

```php

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
        } catch (\Exception $e) {
            // Handle exception
        }

        return $row_count;
    }

```

**DELETE**

```php

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
        } catch (\Exception $e) {
            // Handle exception
        }

        return $row_count;
    }
```

**SLEEP**

```php

    public function wait()
    {
        $this->nap(3);
    }
}

```

