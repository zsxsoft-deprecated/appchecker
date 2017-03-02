<?php
namespace AppChecker;

class Log
{

    private static $outputInterface = null;
    /**
     * SetOutputInterface
     * @param object &$interface
     */
    public static function setOutputInterface(&$interface)
    {
        self::$outputInterface = $interface;
    }

    /**
     * Output info
     * @param string $text
     */
    public static function info($text)
    {
        self::log('<info>' . $text . '</info>');
    }
    /**
     * Output error then exit
     * @param string $text
     * @param bool $exit
     */
    public static function error($text, $exit = true)
    {

        $text = '<error>' . $text . '</error>';
        if ($exit) {
            self::end($text, 1);
        } else {
            self::log($text);
        }
    }

    /**
     * Output warning
     * @param string $text
     * @param bool $exit
     */
    public static function warning($text, $exit = false)
    {

        $text = '<question>' . $text . '</question>';
        if ($exit) {
            self::end($text, 1);
        } else {
            self::log($text);
        }
    }

    /**
     * Output title
     * @param string $text
     */
    public static function title($text)
    {
        $boundary = "===================================================================";
        self::$outputInterface->writeln($boundary);
        self::$outputInterface->writeln(str_repeat(" ", (strlen($boundary) - strlen($text)) / 2) . $text);
        self::$outputInterface->writeln($boundary);
    }

    /**
     * Log
     * @param string $text
     */
    public static function line($flush = true)
    {

        self::write(PHP_EOL, $flush);
    }
    /**
     * Log
     * @param string $text
     */
    public static function log($text, $flush = true)
    {

        $text = "[" . date("Y/m/d h:i:s a") . "] " . $text;
        self::write($text, $flush);
    }

    /**
     * Directly Echo
     */
    public static function write($text, $flush = true)
    {

        if (defined('PHP_SYSTEM')) {
            if (PHP_SYSTEM === SYSTEM_WINDOWS && getenv("APPCHECKER_GUI_CHARSET") != "UTF-8") {
                $text = iconv("UTF-8", "gbk", $text);
            }
        }

        if ($flush) {
            ob_flush();
            flush();
        }

        if (is_null(self::$outputInterface)) {
            echo $text . PHP_EOL;
        } else {
            self::$outputInterface->writeln($text);
        }
    }

    /**
     * Output something then exit
     * @param string $text
   * @param integer $errno
     */
    public static function end($text, $errno = 0)
    {
        self::log($text);
        exit($errno);
    }
}
