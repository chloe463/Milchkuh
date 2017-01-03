<?php
/**
 * chloe463\Milchkuh\BindParamBuilder
 *
 * A simple bind parameter builder
 */

namespace chloe463\Milchkuh;

class BindParamBuilder
{
    /**
     * @var array
     */
    private $bind_param;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bind_param = [];
    }

    /**
     * Initialize
     */
    public function init()
    {
        $this->bind_param = [];
    }

    /**
     * Return $bind_param
     */
    public function getBindParam()
    {
        return $this->bind_param;
    }

    /**
     * Append $base_array[$key] to $bind_param if $base_array[$key] is defined
     *
     * @param   array   $base_array
     * @param   string  $key
     * @param   string  $alias
     */
    public function append($base_array, $key, $alias = '')
    {
        if ($alias === '') {
            $alias = $key;
        }

        if (isset($base_array[$key])) {
            $this->bind_param[$alias] = $base_array[$key];
        }

        return;
    }
}
