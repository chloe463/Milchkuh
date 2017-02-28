<?php

namespace chloe463\Milchkuh;

class Logger
{
    /**
     * @var string  $log_file_path
     */
    private $log_file_path;

    /**
     * Constructor
     *
     * @param   string  $log_file_path
     * @codeCoverageIgnore
     */
    public function __construct($log_file_path)
    {
        $this->setLogFilePath($log_file_path);
    }

    /**
     * Reset $log_file_path
     */
    public function setLogFilePath($log_file_path)
    {
        if (!file_exists(dirname($log_file_path))) {
            throw new Exception('No such directory: '. dirname($log_file_path), '', [], Exception::LOGFILE_DIR_ERROR);
        }
        $this->log_file_path = $log_file_path;
    }

    /**
     * Return $log_file_path
     */
    public function getLogFilePath()
    {
        return $this->log_file_path;
    }

    /**
     * Log query to $log_file_path
     *
     * @param   string  $query
     * @param   array   $bind_param
     */
    public function log($query)
    {
        error_log($this->buildMessage($query), 3, $this->log_file_path);
    }

    /**
     * Build message to log
     * Log format: [Y-m-d H:i:s] [pid] Message
     *
     * @param   string  $query
     * @param   array   $bind_param
     */
    public function buildMessage($query, $bind_param = [])
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $pid = getmypid();

        $message = '';
        if (strpos($query, '?') !== false) {
            $message = $this->replaceQuestion2Param($query, $bind_param);
        } else {
            $message = $this->replaceKeyword2Param($query, $bind_param);
        }
        return sprintf("[%s] [%s] %s\n", $now, $pid, $message);
    }

    /**
     * Replace ? in query to parameter value
     *
     * @param   string  $query
     * @param   array   $bind_param
     */
    public function replaceQuestion2Param($query, $bind_param)
    {
        if (empty($bind_param)) {
            return $query;
        }
        $count = 0;
        return preg_replace_callback('/\?/', function ($matches) use ($bind_param, &$count) {
            if (!isset($bind_param[$count])) {
                return '?';
            }
            $res = $bind_param[$count++];
            return $res;
        }, $query);
    }

    /**
     * Replace :keyword to parameter value
     *
     * @param   string  $query
     * @param   array   $bind_param
     */
    public function replaceKeyword2Param($query, $bind_param)
    {
        if (empty($bind_param)) {
            return $query;
        }
        $map = $bind_param;
        foreach ($bind_param as $key => $value) {
            if (strpos($key, ':') !== 0) {
                $map[':' . $key] = $value;
                unset($map[$key]);
            }
        }
        return strtr($query, $map);
    }
}
