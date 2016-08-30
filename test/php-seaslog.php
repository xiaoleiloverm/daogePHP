<?php
/**
 * @author ciogao@gmail.com
 * Date: 14-1-27 下午4:41
 */

if (!class_exists('seaslog')) {
    die('do not find php extend: seaslog');
}
//常量列表
/* SEASLOG_DEBUG                       "debug"
 * SEASLOG_INFO                        "info"
 * SEASLOG_NOTICE                      "notice"
 * SEASLOG_WARNING                     "warning"
 * SEASLOG_ERROR                       "error"
 * SEASLOG_CRITICAL                    "critical"
 * SEASLOG_ALERT                       "alert"
 * SEASLOG_EMERGENCY                   "emergency"
 */

interface Log extends LoggerInterface
{

}

/**
 * Describes a logger instance
 *
 * The message MUST be a string or object implementing __toString().
 *
 * The message MAY contain placeholders in the form: {foo} where foo
 * will be replaced by the context data in key "foo".
 *
 * The context array can contain arbitrary data, the only assumption that
 * can be made by implementors is that if an Exception instance is given
 * to produce a stack trace, it MUST be in a key named "exception".
 *
 * See https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 * for the full interface specification.
 */
interface LoggerInterface
{
    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    //public function warning($message, array $context = array());

    public function getBasePath();
}

class testSeas implements LoggerInterface
{
    protected $handler;
    public function __construct()
    {
        #SeasLog init
        if (!class_exists('seaslog')) {
            throw new ErrorException("do not find php extend: seaslog");
            return;
        }
        //初始化
        $this->handler = new \SeasLog();
    }

    public function __destruct()
    {
        unset($this->handler);
    }

    /*public function warning($message, array $context = array())
    {
    echo '重写 function warning()';
    }*/

    public function getBasePath()
    {
        echo '重写 function getBasePath()';
    }

    public function __call($name, $param_arr)
    {
        var_dump($name, $param_arr);
        $res = $this->handler->$name($param_arr);
        return call_user_func_array([$this->handler, $name], $param_arr);
    }

    //__get()方法用来获取私有属性
    public function __get($name)
    {
        var_dump('private function:' . $name);
    }

    //__set()方法用来设置私有属性
    public function __set($property_name, $value)
    {
        echo "在直接设置私有属性值的时候，自动调用了这个__set()方法为私有属性赋值<br>";
        $this->$property_name = $value;
    }

}

/*$handler = new \SeasLog();
echo '<pre>';
var_dump($handler->getBasePath());
var_dump($handler->setBasePath('log/index'));
var_dump($handler->getBasePath());
var_dump(SEASLOG_DEBUG);*/
$testSeas = new \testSeas();
$res      = $testSeas->getBasePath();
//$res      = $testSeas->warning();
var_dump($res);
/*
SeasLog::log(SEASLOG_ERROR, 'this is a error test by ::log');

SeasLog::debug('this is a {userName} debug', array('{userName}' => 'neeke'));

SeasLog::info('this is a info log');

SeasLog::notice('this is a notice log');

SeasLog::warning('your {website} was down,please {action} it ASAP!', array('{website}' => 'github.com', '{action}' => 'rboot'));

SeasLog::error('a error log');

SeasLog::critical('some thing was critical');

SeasLog::alert('yes this is a {messageName}', array('{messageName}' => 'alertMSG'));

SeasLog::emergency('Just now, the house next door was completely burnt out! {note}', array('{note}' => 'it`s a joke'));
echo "\n";
 */
