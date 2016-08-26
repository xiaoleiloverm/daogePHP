<?php
/*function exception_handler($exception)
{
echo "Uncaught exception: ", $exception->getMessage(), "\n";
}

set_exception_handler('exception_handler');

throw new Exception('Uncaught Exception');
echo "Not Executed\n";*/
set_error_handler("error"); //错误处理
set_exception_handler('exception'); //异常处理
//异常处理
function exception($exception)
{
    echo "Uncaught exception: ", $exception->getMessage(), "\n";
}

// error handler function
function error($errno, $errstr, $errfile, $errline)
{
    /* if (!(error_reporting() & $errno)) {
    // This error code is not included in error_reporting
    return;
    }*/
    throw new ErrorException($errstr, 0, $code, $file, $line);
/*
switch ($errno) {
case E_USER_ERROR:
echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
echo "  Fatal error on line $errline in file $errfile";
echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
echo "Aborting...<br />\n";
exit(1);
break;

case E_USER_WARNING:
echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
break;

case E_USER_NOTICE:
echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
break;

default:
echo "Unknown error type: [$errno] $errstr<br />\n";
break;
}*/

    /* Don't execute PHP internal error handler */
    return true;
}

function errorone()
{
    require 'errorone.php';
    //throw new Exception("Test 1");
}
function errortwo()
{
    //throw new Exception("Test 2");
}
function test()
{
    errorone();
    errortwo();
}

test();
