<?php
/**
 * daogePHP - A PHP Framework For Web
 *
 * @author   leilu<xiaoleiloverm@gmail.com>
 */

//----------------------------------
// 核心函数库
//----------------------------------

/**
 * 类库实例化函数
 *
 */
function A()
{

}

/**
 * 配置函数,获取配置/(批量)配置
 *
 * @param  array|string  $key  配置键名
 * @param  mixed  $value 配置值
 * @return mixed
 */
function C($key = null, $value = null)
{
    static $_config = [];
    //获取所有
    if ($key == '') {
        return $_config;
    }
    //批量设置
    else if (is_array($key)) {
        //多维数组递归替换键为大写
        $array_change_key_case_recursive = function ($key) use (&$array_change_key_case_recursive) {
            return array_map(function ($item) use (&$array_change_key_case_recursive) {
                if (is_array($item)) {
                    $item = $array_change_key_case_recursive($item);
                }
                return $item;
            }, array_change_key_case($key, CASE_UPPER));
        };
        $key     = $array_change_key_case_recursive($key);
        $_config = array_merge_recursive($_config, $key);
        return null;
    }
    //赋值
    else if (is_string($key)) {
        if (!strrpos('.', $key)) {
            $key = strtoupper($key);
            //读取
            if ($value === null) {
                return isset($_config[$key]) ? $_config[$key] : null;
            }
            $_config[$key] = $value;
        } else {
            $key    = explode('.', $key);
            $key[0] = strtoupper($key[0]);
            //读取
            if ($value === null) {
                return isset($_config[$key[0]][$key[1]]) ? $_config[$key[0]][$key[1]] : null;
            }
            $_config[$key[0]][$key[1]] = $value;
        }
    }
    return null;
}

/**
 * 错误处理函数
 *
 * @param  array|string  $error  错误信息
 * @param  string  $handler 异常类型
 * @return void
 */
function E($error, $handler = 'ErrorException')
{
    $errorHandler = [
        '0'      => 'Exception',
        '01'     => 'ErrorException',
        '02'     => 'LogicException',
        '0201'   => 'BadFunctionCallException',
        '020101' => 'BadMethodCallException',
        '0202'   => 'DomainException',
        '0203'   => 'InvalidArgumentException',
        '0204'   => 'LengthException',
        '0205'   => 'OutOfRangeException',
        '03'     => 'RuntimeException',
        '0301'   => 'OutOfBoundsException',
        '0302'   => 'OverflowException',
        '0303'   => 'RangeException',
        '0304'   => 'UnderflowException',
        '0305'   => 'UnexpectedValueException',
    ];
    if (!in_array($handler, $errorHandler)) {
        $handler = $errorHandler['01'];
    }
    if (!is_array($error)) {
        //自定义输出参数
        $trace        = debug_backtrace();
        $e['message'] = $error;
        $e['type']    = 0;
        $e['file']    = $trace[0]['file'];
        $e['line']    = $trace[0]['line'];
    } else {
        $e = $error;
    }
    if ($handler == 'ErrorException') {
        throw new \ErrorException($e['message'], 0, $e['type'], $e['file'], $e['line']); //抛出错误异常
    } else {
        throw new $handler($e['message'], $e['type']); //抛出异常
    }
}

/**
 * 获取设置语言函数
 *
 * @param  array|string  $key  键名
 * @param  mixed  $value 值
 * @return mixed
 */
function L($key = null, $value = null)
{
    static $_lang = [];
    //获取所有
    if ($key == '') {
        return $_lang;
    }
    //批量设置
    else if (is_array($key)) {
        //多维数组递归替换键为大写
        $array_change_key_case_recursive = function ($key) use (&$array_change_key_case_recursive) {
            return array_map(function ($item) use (&$array_change_key_case_recursive) {
                if (is_array($item)) {
                    $item = $array_change_key_case_recursive($item);
                }
                return $item;
            }, array_change_key_case($key, CASE_UPPER));
        };
        $key   = $array_change_key_case_recursive($key);
        $_lang = array_merge_recursive($_lang, $key);
        return null;
    }
    //赋值
    else if (is_string($key)) {
        $key = strtoupper($key);
        //读取
        if ($value === null) {
            return isset($_lang[$key]) ? $_lang[$key] : null;
        } else if (is_array($value)) {
            //替换变量
            //$search = array_key($value);//['a'=>'apple']
            //若不存在,直接返回全大写$key
            //return str_replace($search, $value, isset($_lang[$key]) ? $_lang[$key] : $key);
        }
        $_lang[$key] = $value;
    }
    return null;
}
