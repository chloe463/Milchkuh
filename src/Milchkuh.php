<?php
/**
 * Milchkuh\Michkuh
 *
 * A wrapper class (trait) of PDO.
 */

namespace chloe463\Milchkuh;

trait Milchkuh
{
    /**
     * @var array
     */
    protected $connection_info;

    /**
     * @var string
     */
    protected $db_name;

    /**
     * @var string
     */
    protected $table_name;

    /**
     * @var \PDO
     */
    protected $dbh;

    /**
     * @var integer
     */
    protected $last_insert_id;

    /**
     * @var integer
     */
    protected $row_count;

    /**
     * Getters and setters
     */
    public function getConnectionInfo()
    {
        return $this->connection_info;
    }

    public function getDbName()
    {
        return $this->db_name;
    }

    public function setDbName($db_name)
    {
        $this->db_name = $db_name;
    }

    public function getTableName()
    {
        return $this->table_name;
    }

    public function setTableName($table_name)
    {
        $this->table_name = $table_name;
    }

    public function getDbh()
    {
        return $this->dbh;
    }

    public function getLastInsertId()
    {
        return $this->last_insert_id;
    }

    public function getRowCount()
    {
        return $this->row_count;
    }

    /**
     * Initialize
     *
     * @param   array   $connection_info
     */
    public function init($connection_info)
    {
        $this->validateConnectionInfo($connection_info);
        $this->connection_info = $connection_info;
        $this->db_name         = $connection_info['db_name'];
        $this->table_name      = isset($connection_info['table_name']) ? $connection_info['table_name'] : '';

        return;
    }

    /**
     * Validate connection info
     *
     * @param   array   $connection_info
     *
     * @throws  Milchkuh\Exception  If some parameters are missing
     */
    public function validateConnectionInfo($connection_info)
    {
        $essential_keys = ['host', 'port', 'user', 'pass', 'db_name'];
        $missing_keys   = [];
        foreach ($essential_keys as $key) {
            if (!isset($connection_info[$key])) {
                $missing_keys[] = $key;
            }
        }

        if (!empty($missing_keys)) {
            throw new Exception(sprintf("Some parameters are missing(%s)", implode($missing_keys, ',')), '', [], Exception::INVALID_PARAMETER);
        }

        return true;
    }

    /**
     * Instantiate PDO object
     *
     * @return \PDO
     */
    public function connect()
    {
        if (is_a($this->dbh, '\PDO')) {
            return $this->dbh;
        }

        $user    = $this->connection_info['user'];
        $pass    = $this->connection_info['pass'];
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ];
        $this->dbh = new \PDO($this->buildDsn(), $user, $pass, $options);
        return $this->dbh;
    }

    /**
     * Disconnect
     *
     * @param   \PDOStatement   $statement
     */
    public function disconnect($statement)
    {
        $statement = null;
        if ($this->dbh->inTransaction()) {
            return;
        }
        $this->dbh = null;
        return;
    }

    /**
     * Build DSN string from connection info
     *
     * @return  string
     */
    public function buildDsn()
    {
        return sprintf("mysql:dbname=%s;host=%s;port=%s",
            $this->connection_info['db_name'], $this->connection_info['host'], $this->connection_info['port']
        );
    }

    /**
     * Begin transaction
     *
     * @return  boolean
     */
    public function begin()
    {
        if ($this->inTransaction()) {
            throw new Exception("Active transaction already exists", '', [], Exception::IN_TRANSACTION);
        }
        return $this->connect()->beginTransaction();
    }

    /**
     * Check if inside a transaction
     */
    public function inTransaction()
    {
        if (is_null($this->dbh)) {
            return false;
        }
        return $this->dbh->inTransaction();
    }

    /**
     * Commit transaction
     *
     * @return  boolean
     *
     * @throws  Milchkuh\Exception  If no transaction is available
     */
    public function commit()
    {
        if (!$this->inTransaction()) {
            throw new Exception("No active transaction exists", '', [], Exception::NO_TRANSACTION);
        }
        return $this->dbh->commit();
    }

    /**
     * Rollback transaction
     *
     * @return  boolean
     *
     * @throws  Milchkuh\Exception  If no transaction is available
     */
    public function rollBack()
    {
        if (!$this->inTransaction()) {
            throw new Exception("No active transaction exists", '', [], Exception::NO_TRANSACTION);
        }
        return $this->dbh->rollBack();
    }

    /**
     * Validate given sql
     *
     * @param   string  $query
     * @param   string  $keyword
     *
     * @return  boolean
     */
    public function validateQuery($query, $keyword)
    {
        if (stripos($query, $keyword) === false) {
            return false;
        }
        return true;
    }

    /**
     * Prepare sql
     *
     * @param   string  $query
     *
     * @return  \PDOStatement
     */
    public function prepare($query)
    {
        return $this->connect()->prepare($query);
    }

    /**
     * Execute sql
     *
     * @param   \PDOStatement   $statement
     * @param   array           $bind_param
     *
     * @return  boolean
     */
    public function execute($statement, $bind_param)
    {
        try {
            return $statement->execute($bind_param);
        } catch (\PDOException $e) {
            throw new Exception("Failed to execute SQL - {$e->getMessage()}", '', $bind_param, Exception::EXEC_ERROR, $e);
        }
    }

    /**
     * Execute INSERT query
     *
     * @param   string  $query
     * @param   array   $bind_param
     *
     * @return  integer
     */
    public function insert($query, $bind_param)
    {
        if (!$this->validateQuery($query, 'INSERT')) {
            throw new Exception("Non-INSERT query is given to " . __METHOD__, $query, $bind_param, Exception::UNMATCHED_SQL);
        }

        $statement = $this->prepare($query);
        $this->execute($statement, $bind_param);

        $this->last_insert_id = $this->dbh->lastInsertId();

        $this->disconnect($statement);

        return $this->last_insert_id;
    }

    /**
     * Execute SELECT query
     *
     * @param   string  $query
     * @param   array   $bind_param
     *
     * @return  array
     */
    public function select($query, $bind_param, $class_name = '')
    {
        if (!$this->validateQuery($query, 'SELECT')) {
            throw new Exception("Non-SELECT query is given to " . __METHOD__, $query, $bind_param, Exception::UNMATCHED_SQL);
        }

        $statement = $this->prepare($query);
        $this->execute($statement, $bind_param);

        $records = [];
        if ($class_name === '') {
            $records = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $records = $statement->fetchAll(\PDO::FETCH_CLASS, $class_name);
        }

        $this->row_count = $statement->rowCount();
        $this->disconnect($statement);

        return $records;
    }

    /**
     * Execute UPDATE query
     *
     * @param   string  $query
     * @param   array   $bind_param
     *
     * @return  integer
     */
    public function update($query, $bind_param)
    {
        if (!$this->validateQuery($query, 'UPDATE')) {
            throw new Exception("Non-UPDATE query is given to " . __METHOD__, $query, $bind_param, Exception::UNMATCHED_SQL);
        }

        $statement = $this->prepare($query);
        $this->execute($statement, $bind_param);

        $row_count = $statement->rowCount();
        $this->disconnect($statement);

        return $row_count;
    }

    /**
     * Execute DELETE query
     *
     * @param   string  $query
     * @param   array   $bind_param
     *
     * @return  integer
     */
    public function delete($query, $bind_param)
    {
        if (!$this->validateQuery($query, 'DELETE')) {
            throw new Exception("Non-DELETE query is given to " . __METHOD__, $query, $bind_param, Exception::UNMATCHED_SQL);
        }

        $statement = $this->prepare($query);
        $this->execute($statement, $bind_param);

        $row_count = $statement->rowCount();
        $this->disconnect($statement);

        return $row_count;
    }

    /**
     * Call stored procedure
     *
     * @param   string  $query
     * @param   array   $bind_param
     *
     * @return  array
     */
    public function call($query, $bind_param)
    {
        if (!$this->validateQuery($query, 'CALL')) {
            throw new Exception("Non-CALL query is given to " . __METHOD__, $query, $bind_param, Exception::UNMATCHED_SQL);
        }

        $statement = $this->prepare($query);
        $this->execute($statement, $bind_param);

        $records = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $this->disconnect($statement);

        return $records;
    }

    /**
     * SLEEP $seconds
     *
     * @param   integer $seconds
     *
     * @return  boolean
     */
    public function nap($seconds)
    {
        $statement = $this->prepare("SELECT SLEEP({$seconds})");
        $this->execute($statement, []);
        $this->disconnect($statement);
        return true;
    }
}
