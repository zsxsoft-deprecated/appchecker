<?php
namespace AppChecker;
use AppChecker\Log;
use AppChecker\Utils;

class ErrorHandler {

	private static function Output(&$zbe) {
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
		    echo $iInt . ". " ;
        echo (isset($sData['class']) ? $sData['class'] . $sData['type'] : "");
        echo $sData['function'] . '(';
        if (isset($sData['args'])) {
            foreach ($sData['args'] as $argKey => $argVal) {
                echo $argKey . ' => ' . (Utils::CheckCanBeString($argVal) ? htmlspecialchars((string) $argVal) : 'Object') . ',';
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
		Log::Error("AppChecker Exited");
		exit;

	}
	/**
	 * Hook
	 * @return true
	 */
	public static function Hook() {
      set_error_handler(array(__CLASS__, 'ErrorHandler'));
		  set_exception_handler(array(__CLASS__, 'ExceptionHandler'));
		  register_shutdown_function(array(__CLASS__, 'ShutdownFunction'));
      Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError', '\AppChecker\ErrorHandler::DoZbpError', PLUGIN_EXITSIGNAL_RETURN);
      return true;
	}
	/**
	 * Unhook
	 * @return true
	 */
	public static function Unhook() {
		  $function = array(__CLASS__, 'DoNothing');
	  	set_error_handler($function);
	  	set_exception_handler($function);
	  	register_shutdown_function($function);
  		return true;
	}
	/**
	 * Empty Function (Do nothing)
	 * @return true
	 */
	public static function DoNothing() {
		return true;
	}
	/**
	 * api_error_handler
	 * @param  integer $errno
	 * @param  string $errstr
	 * @param  string $errfile
	 * @param  integer $errline
	 * @return true
	 */
	public static function ErrorHandler($errno, $errstr, $errfile, $errline) {

	  	$zbe = \ZBlogException::GetInstance();
	  	$zbe->ParseError($errno, $errstr, $errfile, $errline);
	  	self::Output($zbe);

	}
	/**
	 * api_exception_handler
	 * @param  array $exception
	 * @return true
	 */
	public static function ExceptionHandler($exception) {
  		$zbe = \ZBlogException::GetInstance();
	  	$zbe->ParseException($exception);
  		self::Output($zbe);
	}

	/**
	 * register_shutdown_function
	 * @return true
	 */
	public static function ShutdownFunction() {
      if ($error = error_get_last()) {
          $zbe = \ZBlogException::GetInstance();
          $zbe->ParseShutdown($error);
          self::Output($zbe);
      }
	}

	public static function DoZbpError($number, $message, $file, $line_number) {
      $zbe = \ZBlogException::GetInstance();
      $zbe->message = $message;
      $zbe->file = $file;
      $zbe->line = $line_number;
      $zbe->type = $number;
      self::Output($zbe);
  }
}