<?php
set_error_handler('myError'); //设置错误处理函数
trigger_error("Cannot divide by zero", E_USER_ERROR);

function myError($level, $message, $file, $line, $context)
{
    var_dump($level, $message, $file, $line, $context);
}

echo 2;

function error_handler($level, $message, $file, $line, $context)
{
    //Handle user errors, warnings, and notices ourself
    if ($level === E_USER_ERROR || $level === E_USER_WARNING || $level === E_USER_NOTICE) {
        echo '<strong>Error:</strong> ' . $message;
        return (true); //And prevent the PHP error handler from continuing
    }
    return (false); //Otherwise, use PHP's error handler
}

function trigger_my_error($message, $level)
{
    //Get the caller of the calling function and details about it
    $debug  = debug_backtrace();
    $callee = next($debug); //php5.6 next函数直接传入函数返回 会报错 如 next(debug_backtrace())写法
    //Trigger appropriate error
    trigger_error($message . ' in <strong>' . $callee['file'] . '</strong> on line <strong>' . $callee['line'] . '</strong>', $level);
}

//Use our custom handler
set_error_handler('error_handler');

//-------------------------------
//Demo usage:
//-------------------------------
function abc($str)
{
    if (!is_string($str)) {
        trigger_my_error('abc() expects parameter 1 to be a string', E_USER_ERROR);
    }
}

abc('Hello world!'); //Works
abc(18); //Error: abc() expects parameter 1 to be a string in [FILE].php on line 31
