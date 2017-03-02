<?php
namespace AppChecker;

use AppChecker\Log;
use AppChecker\Utils;

class ErrorHandler
{

    private static function __output(&$zbe)
    {
        $code = $zbe->get_code($zbe->file, $zbe->line);
        $code = $code[$zbe->line - 1];

        Log::Title("ERROR");
        Log::Log("Type: " . $zbe->type);
        Log::Log("TypeName: " . $zbe->typename);
        Log::Log("Message: " . $zbe->message);
        Log::Log("File: " . $zbe->file);
        Log::Log("Line: " . $zbe->line);
        Log::Log("Code: \n" . htmlspecialchars_decode($code));
        Log::Title("TRACE");
        $traceString = "";

        foreach (debug_backtrace() as $iInt => $sData) {
            echo $iInt . ". ";
            echo(isset($sData['class']) ? $sData['class'] . $sData['type'] : "");
            echo $sData['function'] . '(';
            if (isset($sData['args'])) {
                foreach ($sData['args'] as $argKey => $argVal) {
                    echo $argKey . ' => ';
                    echo Utils::checkCanBeString($argVal) ? htmlspecialchars((string) $argVal) : 'Object';
                    echo ',';
                }
            }
            echo ' ' . (isset($sData['file']) ? $sData['file'] : 'Callback');
            if (isset($sData['line'])) {
                echo ': ' . $sData['line'];
            }
            echo ")\n";

            echo "\n\n";
        }
        ob_flush();
        Log::error("AppChecker Exited");
        exit;
    }
    /**
     * Hook
     * @return true
     */
    public static function hook()
    {
        set_error_handler(array(__CLASS__, 'ErrorHandler'));
        set_exception_handler(array(__CLASS__, 'ExceptionHandler'));
        register_shutdown_function(array(__CLASS__, 'ShutdownFunction'));
        Add_Filter_Plugin(
            'Filter_Plugin_Zbp_ShowError',
            '\AppChecker\ErrorHandler::DoZbpError',
            PLUGIN_EXITSIGNAL_RETURN);

        return true;
    }
    /**
     * Unhook
     * @return true
     */
    public static function unHook()
    {
        $function = array(__CLASS__, 'DoNothing');
        set_error_handler($function);
        set_exception_handler($function);
        register_shutdown_function($function);

        return true;
    }
    /**
     * Empty Function (Do nothing)
     *
     * @return true
     */
    public static function doNothing()
    {
        return true;
    }
    /**
     * Handle Error
     *
     * @param integer $errno   error number
     * @param string  $errstr  error string
     * @param string  $errfile error filename
     * @param integer $errline error line
     *
     * @return true
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {

        $zbe = \ZBlogException::GetInstance();
        $zbe->ParseError($errno, $errstr, $errfile, $errline);
        self::__output($zbe);
    }
    /**
     * Handle Exception
     *
     * @param array $exception expection object
     *
     * @return true
     */
    public static function exceptionHandler($exception)
    {
        $zbe = \ZBlogException::GetInstance();
        $zbe->ParseException($exception);
        self::__output($zbe);
    }

    /**
     * handle shutdown function
     *
     * @return true
     */
    public static function shutdownFunction()
    {
        if ($error = error_get_last()) {
            $zbe = \ZBlogException::GetInstance();
            $zbe->ParseShutdown($error);
            self::__output($zbe);
        }
    }
    
    /**
     * handle ZBP error
     *
     * @param integer $number      error number
     * @param string  $message     error message
     * @param string  $file        file name
     * @param integer $line_number error line number
     */
    public static function doZbpError($number, $message, $file, $line_number)
    {
        $zbe = \ZBlogException::GetInstance();
        $zbe->message = $message;
        $zbe->file = $file;
        $zbe->line = $line_number;
        $zbe->type = $number;
        self::__output($zbe);
    }
}
