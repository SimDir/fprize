<?php

namespace mvcrb;

defined('ROOT') OR die('No direct script access.');

/**
 * Description of ErrorHandler
 *
 * @author я
 */
class ErrorHandler {

    protected $format = '{{message}} {{class}}::{{method}} {{file}} on line {{line}}';

    /**
     * @var HandlerInterface
     */
    protected $displayHandler;

    /**
     * @var integer the size of the reserved memory. A portion of memory is pre-allocated so that
     * when an out-of-memory issue occurs, the error handler is able to handle the error with
     * the help of this reserved memory. If you set this value to be 0, no memory will be reserved.
     * Defaults to 256KB.
     */
    protected $memoryReserveSize = 262144;

    /**
     * @var string Used to reserve memory for fatal error handler.
     */
    private $_memoryReserve;

    /**
     * Register this error handler.
     */
    public function register() {
        // Catch errors
        set_error_handler([$this, 'handleError']);

        if ($this->memoryReserveSize > 0) {
            $this->_memoryReserve = str_repeat('x', $this->memoryReserveSize);
        }
        // Start buffer
        ob_start();
        // Catch fatal errors
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * Unregisters this error handler by restoring the PHP error handlers.
     */
    public function unregister() {
        restore_error_handler();
    }

    /**
     * Error handler.
     *
     * @param int $code
     * @param string $msg
     * @param string $file
     * @param int $line
     * @return bool
     * @throws \ErrorException
     */
    public function handleError($code, $msg, $file, $line) {
        if (~error_reporting() & $code) {
            return false;
        }

        switch ($code) {
            case E_USER_WARNING:
            case E_WARNING:
                $exception = new \ErrorException("[E_WARNING] {$msg}", \Monolog\Logger::WARNING, $code, $file, $line);
                break;
            case E_USER_NOTICE:
            case E_NOTICE:
            case E_STRICT:
                $exception = new \ErrorException("[E_NOTICE] {$msg}", \Monolog\Logger::NOTICE, $code, $file, $line);
                break;
            case E_RECOVERABLE_ERROR:
                $exception = new \ErrorException("[E_CATCHABLE] {$msg}", \Monolog\Logger::ERROR, $code, $file, $line);
                break;
            default:
                $exception = new \ErrorException("[E_UNKNOWN] {$msg}", \Monolog\Logger::CRITICAL, $code, $file, $line);
        }

        throw $exception;
    }

    /**
     * Fatal handler.
     *
     * @return void
     */
    public function handleShutdown() {
        unset($this->_memoryReserve);
// create a log channel
        $Log = new \Monolog\Logger('ErrorHandler');
        $Log->pushHandler(new \Monolog\Handler\StreamHandler(SITE_DIR.'error.log'));
        $error = error_get_last();
        if (
                isset($error['type']) &&
                ($error['type'] == E_ERROR ||
                $error['type'] == E_PARSE ||
                $error['type'] == E_COMPILE_ERROR ||
                $error['type'] == E_CORE_ERROR)
        ) {

            $type = "";
            switch ($error['type']) {
                case E_ERROR:
                    $type = '[E_ERROR]';
                    break;
                case E_PARSE:
                    $type = '[E_PARSE]';
                    break;
                case E_COMPILE_ERROR:
                    $type = '[E_COMPILE_ERROR]';
                    break;
                case E_CORE_ERROR:
                    $type = '[E_CORE_ERROR]';
                    break;
            }
            $exception = new \ErrorException("$type {$error['message']}", $Log::CRITICAL, $error['type'], $error['file'], $error['line']);
            if (SHOW_ERROR) {
                $Log->log($Log::CRITICAL, $this->convertExceptionToString($exception));
            }
            $this->display($exception);
        } else {
            if (ob_get_length() !== false) {
                // Display buffer, complete work buffer
                ob_end_flush();
            }
        }
    }

    /**
     * Sets a display handler.
     * @param HandlerInterface $handler
     */
    public function setDisplayHandler(HandlerInterface $handler) {
        $this->displayHandler = $handler;
    }

    /**
     * Sets a format message log.
     * @param string $format
     */
    public function setFormat($format) {
        $this->format = $format;
    }

    /**
     * Sets a size memory.
     * @param int $size
     */
    public function setMemoryReserve($size) {
        $this->memoryReserveSize = $size;
    }

    /**
     * @param \Exception $exception
     */
    public function display(\Exception $exception) {
        // display Whoops

//        dd($exception);
        die('This site is temporarily unavailable. Please, visit the page later.');
    }

    /**
     * Converts an exception into a simple string.
     *
     * @param \Exception $exception the exception being converted
     * @return string the string representation of the exception.
     */
    public function convertExceptionToString(\Exception $exception) {
        $trace = $exception->getTrace();
        $placeholders = [
            '{{class}}' => isset($trace[0]['class']) ? $trace[0]['class'] : '',
            '{{method}}' => isset($trace[0]['function']) ? $trace[0]['function'] : '',
            '{{message}}' => $exception->getMessage(),
            '{{file}}' => $exception->getFile(),
            '{{line}}' => $exception->getLine(),
        ];

        return strtr($this->format, $placeholders);
    }

}
