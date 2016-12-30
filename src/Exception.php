<?php
/**
 * Milchkuh\Exception
 *
 * Custom exception class.
 */

namespace chloe463\Milchkuh;

class Exception extends \Exception
{
    const INVALID_PARAMETER = 1;
    const UNMATCHED_SQL     = 2;
    const INSERTION_ERROR   = 3;
    const SELECTION_ERROR   = 4;
    const UPDATE_ERROR      = 5;
    const DELETION_ERROR    = 6;
    const IN_TRANSACTION    = 7;
    const NO_TRANSACTION    = 8;
    const EXEC_ERROR        = 9;
    const CALL_ERROR        = 10;

    /**
     * @var string  $query
     */
    protected $query;

    /**
     * @var array   $bind_param
     */
    protected $bind_param;

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     */
    public function __construct($message, $query = '', $bind_param = [], $code = null, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->query      = $query;
        $this->bind_param = $bind_param;
    }

    /**
     * Return query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Return bind param
     */
    public function getBindParam()
    {
        return $this->bind_param;
    }

    /**
     * Return bind param as a json string
     */
    public function getBindParamAsJson()
    {
        return json_encode($this->bind_param);
    }
}
