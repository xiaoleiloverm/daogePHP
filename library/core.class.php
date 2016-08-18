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
        set_error_handler('\library\core::customError');
    }

    /**
     *加载配置
     */
    public static function loadConfig()
    {

    }

    /**
     *应用程序执行入口
     */
    public static function start()
    {
        //初始化
        \library\core::init();
        //加载配置
        \library\core::loadConfig();
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
                $trace        = $debug_backtrace();
                $e['message'] = $error;
                $e['file']    = $trace['file'];
                $e['line']    = $trace['line'];
                //缓存区控制
                ob_start();
                debug_print_backtrace();
                $e['trace'] = ob_get_clean();
            } else {
                $e = $error;
            }
            //CLI下输出
            if (IS_CLI) {
                //加载climate 库
                //file_exists(VENDOR_PATH.'climate-3.2.1/src');
                //require_once VENDOR_PATH.'climate-3.2.1/src';
                //友好输出
                //if(class_exists(\))
                //普通输出
                exit(iconv('UTF-8', 'gbk', $e['message']) . PHP_EOL . 'file:' . $e['file'] . PHP_EOL . 'line:' . $e['line']);
            }
        }
        //非调试模式 一般是正式环境
        else {

        }
    }

    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    public static function customError($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_ERROR: //1 致命的运行时错误
            case E_PARSE: //4 编译时语法解析错误。解析错误仅仅由分析器产生。
            case E_CORE_ERROR: //16 在PHP初始化启动过程中发生的致命错误。该错误类似 E_ERROR，但是是由PHP引擎核心产生的。
            case E_COMPILE_ERROR: //64 致命编译时错误。类似E_ERROR, 但是是由Zend脚本引擎产生的。
            case E_USER_ERROR: //256 用户产生的错误信息。类似 E_ERROR, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。
                self::halt();
                break;
            default:
                break;
        }
    }

}
