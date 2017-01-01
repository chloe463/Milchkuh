# Milchkuh

[![Build Status](https://travis-ci.org/chloe463/Milchkuh.svg?branch=master)](https://travis-ci.org/chloe463/Milchkuh)

## Summary

This is a wrapper class (trait) of PDO.

You can access MySQL database with simple APIs.

## Install

```bash
$ composer require chloe463/milchkuh
```

## Usage

```php
<?php

use chloe463\Milchkuh\Milchkuh;
use chloe463\Milchkuh\Exception;

class AwesomeClass
{
    use Milchkuh;

    public function __construct()
    {
        $db_info = [
            // These are essential keys
            'host'       => 'localhost',
            'port'       => 3306,
            'user'       => '',
            'pass'       => '',
            'db_name'    => 'db',

            // table_name is optional
            'table_name' => 'table'
        ];
        $this->init($db_info);
    }

    public function doSomething()
    {
        // SELECT
        $records = [];
        try {
            $records = $this->select($query, $bind_param);
        } catch (Exception $e) {
            // Handle exception
        }

        // INSERT
        $last_insert_id = null;
        try {
            $last_insert_id = $this->insert($query, $bind_param);
        } catch (Exception $e) {
            // Handle exception
        }

        // UPDATE
        $row_count = null;
        try {
            $row_count = $this->update($query, $bind_param);
        } catch (Exception $e) {
            // Handle exception
        }

        // DELETE
        $row_count = null;
        try {
            $row_count = $this->delete($query, $bind_param);
        } catch (Exception $e) {
            // Handle exception
        }

        // SLEEP
        $this->nap(3);

        // There are some transaction APIs
        try {
            $this->begin();

            // Execute query

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
        }
    }
}
```

## Other features

* `QueryBuilder` helps you to build SQL

```php
$qb = new QueryBuilder();
$qb->append('SELECT * FROM db.table')
   ->append(' WHERE column1 = :column1')
   ->append(' AND column2 = :column2', isset($values['column2']));

$query = $qb->getQuery();
// If $values['column2'] is set
// SELECT * FROM db.table WHERE column1 = :column1 AND column2 = :column2
//
// If $values['column2'] is NOT set
// SELECT * FROM db.table WHERE column1 = :column1
```

* `Logger` logs queries
    * To enable logger, just pass path to log file as 2nd argument to `chloe463\Milchkuh\Milchkuh::init`
    ```php
    // Query logs are going to /path/to/log_file
    $this->init($connection_info, '/path/to/log_file');
    ```

