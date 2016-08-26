<?php
ini_set(error_reporting(0));
echo $a;
xxx
/*
E_ERROR 1:严重错误。通常会显示出来，也会中断程序执行。

E_WARNING 2：最普通的错误类型。通常都会显示出来，但不会中断程序的执行。

E_PARSE 4：解析错误在编译的时候发生。

E_STRICT 2048：编码标准化警告，运行时发生的错误。这个错误级别是5.4之前唯一不包含在E_ALL常量中的，为了让PHP4到PHP5的迁移更加容易。5.4.0版本后已包含

E_NOTICE 8：表示运行的代码可能在操作一些未知的事情。在脚本正常运行下发生的代码错误。

E_CORE_ERROR 16：由于扩展启动失败等导致的。

E_COMPILE_WARNING 128：编译的时候出现的警告，告诉用户一些不推荐使用的语法信息。

E_COMPILE_ERROR：编译时发生的致命错误，指出脚本的错误。

E_USER_ERROR 256：致命的用户生成的错误。这类似于程序员使用 PHP 函数 trigger_error() 设置的 E_ERROR。

E_USER_WARNING 512：非致命的用户生成的警告。这类似于程序员使用 PHP 函数 trigger_error() 设置的 E_WARNING。

E_USER_NOTICE 1024：用户生成的通知。这类似于程序员使用 PHP 函数 trigger_error() 设置的 E_NOTICE。

E_RECOVERABLE_ERROR：接近致命的运行时错误，若未被捕获则视同E_ERROR。

E_ALL：捕获所有的错误和警告。

可以用error_reporting(Integer)函数来设置哪些错误被报告。如所有的错误，表示为E_ALL ,除了通告以外的所有错误,可以表示为E_ALL & ~E_NOTICE.

display_errors(boolean):这个设置控制错误是否作为PHP输出的一部分显示出来。

log_errors(boolean),这个设置控制错误是否记录。日志的地址是通过error_log(String)设置决定的。默认情况下，错误被记录到WEB服务器的错误日志中。

html_errors(boolean)：设置控制是否在错误信息中采用HTML格式 。

 */

/*
值 常量 说明 备注
1     E_ERROR (integer)  致命的运行时错误。这类错误一般是不可恢复的情况，例如内存分配导致的问题。后果是导致脚本终止不再继续运行。
2     E_WARNING (integer)  运行时警告 (非致命错误)。仅给出提示信息，但是脚本不会终止运行。
4     E_PARSE (integer)  编译时语法解析错误。解析错误仅仅由分析器产生。
8     E_NOTICE (integer)  运行时通知。表示脚本遇到可能会表现为错误的情况，但是在可以正常运行的脚本里面也可能会有类似的通知。
16    E_CORE_ERROR (integer)  在PHP初始化启动过程中发生的致命错误。该错误类似 E_ERROR，但是是由PHP引擎核心产生的。  since PHP 4
32    E_CORE_WARNING (integer)  PHP初始化启动过程中发生的警告 (非致命错误) 。类似 E_WARNING，但是是由PHP引擎核心产生的。  since PHP 4
64    E_COMPILE_ERROR (integer)  致命编译时错误。类似E_ERROR, 但是是由Zend脚本引擎产生的。  since PHP 4
128   E_COMPILE_WARNING (integer)  编译时警告 (非致命错误)。类似 E_WARNING，但是是由Zend脚本引擎产生的。  since PHP 4
256   E_USER_ERROR (integer)  用户产生的错误信息。类似 E_ERROR, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。  since PHP 4
512   E_USER_WARNING (integer)  用户产生的警告信息。类似 E_WARNING, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。  since PHP 4
1024  E_USER_NOTICE (integer)  用户产生的通知信息。类似 E_NOTICE, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。  since PHP 4
2048  E_STRICT (integer)  启用 PHP 对代码的修改建议，以确保代码具有最佳的互操作性和向前兼容性。  since PHP 5
4096  E_RECOVERABLE_ERROR (integer)  可被捕捉的致命错误。 它表示发生了一个可能非常危险的错误，但是还没有导致PHP引擎处于不稳定的状态。 如果该错误没有被用户自定义句柄捕获 (参见 set_error_handler())，将成为一个 E_ERROR　从而脚本会终止运行。  since PHP 5.2.0
8192  E_DEPRECATED (integer)  运行时通知。启用后将会对在未来版本中可能无法正常工作的代码给出警告。  since PHP 5.3.0
16384 E_USER_DEPRECATED (integer)  用户产少的警告信息。 类似 E_DEPRECATED, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。  since PHP 5.3.0
30719 E_ALL (integer)  E_STRICT出外的所有错误和警告信息。  30719 in PHP 5.3.x, 6143 in PHP 5.2.x, 2047 previously

 */

var_dump(error_get_last());
//指定错误处理器
function customError($errno, $errstr, $errfile, $errline)
{
    echo "<b>Custom error:</b> [$errno] $errstr<br />";
}
set_error_handler('customError'); // 默认为所有的错误，就是E_ALL.
