<?php
//-----------------数组键名递归转大写 闭包方式---------------------//
function C($key)
{
    $array_change_key_case_recursive = function ($key) use (&$array_change_key_case_recursive) {
        return array_map(function ($item) use (&$array_change_key_case_recursive) {
            if (is_array($item)) {
                $item = $array_change_key_case_recursive($item);
            }
            return $item;
        }, array_change_key_case($key, CASE_UPPER));
    };
    return $array_change_key_case_recursive($key);
}
$_c = C(['a' => ['aa' => 1, 'bb' => ['bbb' => 1]], 'b' => 2, 'c' => 3]);
var_dump($_c);
//----------------------------------------------------------------//

$test = null;
$test = function ($a) use (&$test) {
    echo $a;
    $a--;

    if ($a > 0) {
        return $test($a);
    }
};

$test(10);

//-----------------数组键名递归转大写----------------------------//
function array_change_key_case_recursive($key)
{
    $_config = [];
    //多维数组递归替换键为大写
    return array_map(function ($item) {
        if (is_array($item)) {
            $item = array_change_key_case_recursive($item);
        }
        return $item;
    }, array_change_key_case($key, CASE_UPPER));
}
$_config = array_change_key_case_recursive(['a' => ['aa' => 1, 'bb' => 2], 'b' => 2, 'c' => 3]);
var_dump($_config);
//----------------------------------------------------------------//

if ([] == '') {
    echo "[] == '' true <br />";
}

if (empty([])) {
    echo "empty([]) true <br />";
}

if (null == '') {
    echo "null == '' true <br />";
}

if (null === '') {
    echo "null === '' true <br />";
}

echo "1) " . dirname("/etc/passwd") . PHP_EOL; // 1) /etc
echo "2) " . dirname("/etc/index.php/a") . PHP_EOL; // 2) / (or \ on Windows)
echo "3) " . dirname(".") . PHP_EOL; // 3) .

$allConstans = get_defined_constants();
var_dump($allConstans);

function trace()
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

trace();

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
