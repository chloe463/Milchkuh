<?php
/**
 * Milchkuh\QueryBuilder
 * 
 * A simple query builder
 */

namespace Milchkuh;

class QueryBuilder
{
    /**
     * @var string
     */
    private $query;

    /**
     * Constructor
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->query = '';
    }

    /**
     * Reset query
     */
    public function init()
    {
        $this->query = '';
        return $this;
    }

    /**
     * Reset query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * Return query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Append $partial to $this->query if $condition is true
     *
     * @param   string      $partial
     * @param   boolean     $condition
     *
     * @return  QueryBuilder
     */
    public function append($partial, $condition = true)
    {
        if ($condition) {
            $this->query .= $partial;
        }
        return $this;
    }
}
