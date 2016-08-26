<?php
//error_reporting(0);
//Example #1 使用set_error_handler()函数将错误信息托管至ErrorException

function exception_error_handler($errno, $errstr, $errfile, $errline)
{
    var_dump($errno, $errstr, $errfile, $errline);
    if (error_reporting() == 0) {
        //return;
    }
    //trigger_error();

    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
}
function myException(Exception $e)
{
    var_dump($e);
}
//自定义错误处理
set_error_handler("exception_error_handler");
// 捕捉未处理的：try 的异常
set_exception_handler('myException');
/* Trigger exception */
a[0];
//处理致命错误
//register_shutdown_function('fatalError');
require 'errorone.php';
function fatalError()
{
    var_dump(error_get_last());
}
