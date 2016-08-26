<?php

echo "1) " . dirname("/etc/passwd") . PHP_EOL; // 1) /etc
echo "2) " . dirname("/etc/index.php/a") . PHP_EOL; // 2) / (or \ on Windows)
echo "3) " . dirname(".") . PHP_EOL; // 3) .

$allConstans = get_defined_constants();
var_dump($allConstans);

function c()
{
    ob_start();
    //打印一条回溯
    debug_print_backtrace();
    $tmp = ob_get_clean();
    var_dump($tmp);
    //产生一条回溯跟踪(backtrace)
    $trace = debug_backtrace();
    var_dump($trace);
}

c();

//trigger_error("Cannot divide by zero11", E_USER_ERROR);

echo PHP_SAPI;
var_dump(get_magic_quotes_gpc());
var_dump($_SERVER);

//php5.6+版本有了新特性，函数可以传入不定数目的参数
function getSum(...$numbers)
{
    var_dump($numbers);
    $result = 0;
    foreach ($numbers as $n) {
        $result += $n;
    }
    return $result;
}
$parm = [];
for ($i = 0; $i <= 1000; $i += 2) {
    $parm[] = $i;
}
echo 'result:' . call_user_func_array('getSum', $parm);
