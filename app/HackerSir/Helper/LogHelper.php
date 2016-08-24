<?php

namespace Hackersir\Helper;

use App;
use Illuminate\Support\Facades\Log;
use Monolog\Logger as MonologLogger;

class LogHelper
{
    /**
     * The Log levels.
     *
     * @var array
     */
    protected static $levels = [
        'debug'     => MonologLogger::DEBUG,
        'info'      => MonologLogger::INFO,
        'notice'    => MonologLogger::NOTICE,
        'warning'   => MonologLogger::WARNING,
        'error'     => MonologLogger::ERROR,
        'critical'  => MonologLogger::CRITICAL,
        'alert'     => MonologLogger::ALERT,
        'emergency' => MonologLogger::EMERGENCY,
    ];

    /**
     * Log an emergency message to the logs.
     *
     * @param  string $message
     * @param  array $contextList
     * @return void
     */
    public static function emergency($message, array $contextList = [])
    {
        forward_static_call_array([new static(), 'writeLog'], [__FUNCTION__, func_get_args()]);
    }

    /**
     * Log an alert message to the logs.
     *
     * @param  string $message
     * @param  array $contextList
     * @return void
     */
    public static function alert($message, array $contextList = [])
    {
        forward_static_call_array([new static(), 'writeLog'], [__FUNCTION__, func_get_args()]);
    }

    /**
     * Log a critical message to the logs.
     *
     * @param  string $message
     * @param  array $contextList
     * @return void
     */
    public static function critical($message, array $contextList = [])
    {
        forward_static_call_array([new static(), 'writeLog'], [__FUNCTION__, func_get_args()]);
    }

    /**
     * Log an error message to the logs.
     *
     * @param  string $message
     * @param  array $contextList
     * @return void
     */
    public static function error($message, array $contextList = [])
    {
        forward_static_call_array([new static(), 'writeLog'], [__FUNCTION__, func_get_args()]);
    }

    /**
     * Log a warning message to the logs.
     *
     * @param  string $message
     * @param  array $contextList
     * @return void
     */
    public static function warning($message, array $contextList = [])
    {
        forward_static_call_array([new static(), 'writeLog'], [__FUNCTION__, func_get_args()]);
    }

    /**
     * Log a notice to the logs.
     *
     * @param  string $message
     * @param  array $contextList
     * @return void
     */
    public static function notice($message, array $contextList = [])
    {
        forward_static_call_array([new static(), 'writeLog'], [__FUNCTION__, func_get_args()]);
    }

    /**
     * Log an informational message to the logs.
     *
     * @param  string $message
     * @param  array $contextList
     * @return void
     */
    public static function info($message)
    {
        forward_static_call_array([new static(), 'writeLog'], [__FUNCTION__, func_get_args()]);
    }

    /**
     * Log a debug message to the logs.
     *
     * @param  string $message
     * @param  array $contextList
     * @return void
     */
    public static function debug($message, array $contextList = [])
    {
        forward_static_call_array([new static(), 'writeLog'], [__FUNCTION__, func_get_args()]);
    }

    /**
     * Write a message to Monolog.
     *
     * @param  string $level
     * @param  string $message
     * @param  array $contextList
     * @return void
     */
    protected static function writeLog($level)
    {
        if (App::environment('testing')) {
            return;
        }

        $contextList = func_get_arg(1);
        $message = '';
        foreach ($contextList as $context) {
            if (is_string($context)) {
                $temp = $context;
            } else {
                $temp = JsonHelper::encode($context);
            }
            $message .= $temp . PHP_EOL;
        }
        Log::$level($message);
    }
}
