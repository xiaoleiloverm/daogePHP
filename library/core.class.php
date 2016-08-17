<?php
/**
 * daogePHP - A PHP Framework For Web
 *
 * @author   leilu<xiaoleiloverm@gmail.com>
 */

//----------------------------------
// 初始化类文件
//----------------------------------
namespace library;

class core
{

    /**
     *应用程序初始化
     */
    public static function init()
    {
        // 注册AUTOLOAD方法
        spl_autoload_register('\library\core::autoload');
        //定义PHP程序执行完成后发生的错误
        register_shutdown_function('\library\core::fatalError');
        // 设置自定义的错误处理
        set_error_handler('\library\customError');
    }

    /**
     * 类库自动加载
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class)
    {
        if (strpos($class, '\\') !== false) {
            $name = strstr($class, '\\', true);
            if ($name == '') {
                //命名空间
                $name = strstr(substr($class, 1), '\\', true);
                //var_dump($name);
            }
        }
        if (file_exists($name)) {
            require $name;
        }
    }

    /**
     *致命错误捕获
     */
    public static function fatalError()
    {
        //记录日志
        //Log::save();
        //获取最后发生的错误,php>5.2
        if ($e = error_get_last()) {
            switch ($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    ob_end_clean();
                    self::halt($e);
                    break;
            }
        }
    }

    /**
     * 错误输出
     * @param mixed $error 错误
     * @return void
     */
    public static function halt($error)
    {
        $e = [];
        //调试模式或者cli命令行模式下输出错误
        if (APP_DEBUG || IS_CLI) {
            if (!is_array($error)) {
                //自定义输出参数
                /////
            } else {
                $e = $error;
            }
            //CLI下输出
            if (IS_CLI) {

            }
        }
    }

    /**
     *自定义错误
     */
    public static function customError($errno, $errstr, $errfile, $errline)
    {

    }

}
