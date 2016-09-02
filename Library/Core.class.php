<?php
/**
 * daogePHP - A PHP Framework For Web
 *
 * @author   leilu<xiaoleiloverm@gmail.com>
 */

//----------------------------------
// 初始化类文件
//----------------------------------
namespace Library;

class Core
{

    /**
     *应用程序初始化
     */
    public static function init()
    {
        // 注册AUTOLOAD方法
        spl_autoload_register('\Library\Core::autoload');
        //定义PHP程序执行完成后发生的错误
        register_shutdown_function('\Library\Core::fatalError');
        // 设置自定义的错误处理：函数会捕获用户自定义的和非致命类错误
        set_error_handler('\Library\Core::customError'); //过程中用户自定义错误trigger_error触发
        // 捕获未处理的异常
        set_exception_handler('\Library\Core::customException');
        // 加载composer 依赖
        if (file_exists(VENDOR_PATH . 'autoload.php')) {
            require_once VENDOR_PATH . 'autoload.php';
        }
        //加载核心配置
        \Library\Core::coreConfig();
        //加载框架底层语言包
        L(include LIB_PATH . 'Lang/' . strtolower(C('DEFAULT_LANG')) . '.php');
        //加载系统日志抽象类接口
        if (file_exists(CONSTRUCT_PATH . 'Controller/Log/Log.php')) {
            require_once CONSTRUCT_PATH . 'Controller/Log/Log.php';
        }
        // 系统设置
        date_default_timezone_set(C('DEFAULT_TIMEZONE')); //设置系统时区
    }

    /**
     *加载核心配置
     */
    public static function coreConfig()
    {
        $mode = include CONFIG_PATH . 'core' . CONFIG_EXT;
        // 加载核心文件
        foreach ($mode['core'] as $file) {
            if (is_file($file)) {
                include $file;
            }
        }
        //加载核心通用配置文件
        foreach ($mode['config'] as $file) {
            if (is_file($file)) {
                C(include $file);
            }
        }
    }

    /**
     *加载应用配置
     */
    public static function appConfig()
    {
        //加载应用公共配置
        if (is_file(APP_CONFIG_PATH . 'config' . CONFIG_EXT)) {
            C(include APP_CONFIG_PATH . 'config' . CONFIG_EXT);
        }
        ////加载应用项目配置,可覆盖公共配置
        // if (is_file(APP_PATH . 'Config/config' . CONFIG_EXT)) {
        //     C(include APP_PATH . 'Config/config' . CONFIG_EXT);
        // }
    }

    /**
     *加载http 控制器 C层
     */
    public static function http_C()
    {
        //实例化核心控制器C
        $controller = new \Library\Controller\Controller();
        $controller->test();
    }

    /**
     *加载http 模型 M层
     */
    public static function http_M()
    {
        //实例化核心模型M
        $Model = new \Library\Model\Model();
        $Model->test();

    }

    /**
     *加载http 视图 V层
     */
    public static function http_V()
    {
        //实例化核心视图V
        $View = new \Library\View\View();
        $View->test();

    }

    /**
     *应用程序执行入口
     */
    public static function start()
    {
        //初始化
        \Library\Core::init();

        //加载应用配置文件
        \Library\Core::appConfig();

        //var_dump(C());
        $log = new \Library\Controller\Log\MonoLog('error', 'emergency', APP_LOG_PATH . 'app_' . date('Y-m-d', time()) . '.log');

        //路由调度

        //加载http C层
        \Library\Core::http_C();

        //加载http M层
        //\Library\Core::http_M();

        //加载http V层
        //\Library\Core::http_V();
    }

    /**
     * 类库自动加载
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class)
    {
        $class = str_replace('\\', '/', $class);
        //类文件自动加载
        if (file_exists(DAOGE_PATH . $class . EXT)) {
            require DAOGE_PATH . $class . EXT;
        }
        //普通文件自动加载
        elseif (file_exists(DAOGE_PATH . $class . '.php')) {
            require DAOGE_PATH . $class . '.php';
        }
    }

    /**
     *致命错误捕获
     */
    public static function fatalError()
    {
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
            //记录日志
            $log = new \Library\Controller\Log\MonoLog('error', 'emergency', APP_LOG_PATH . 'app_' . date('Y-m-d', time()) . '.log');
            //$log->createLogFile(APP_LOG_PATH . 'app_' . date('Y-m-d', time()) . '.log', 'emergency'); //记录文件
            $log->addInfo('fatalError', $e);
            //$log->record('', 'fatalError', $e);
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
                $trace        = debug_backtrace();
                $e['message'] = $error;
                $e['file']    = $trace[0]['file'];
                $e['line']    = $trace[0]['line'];
            } else {
                $e = $error;
            }
            if (!isset($e['trace'])) {
                $e['trace'] = '';
                //缓存区控制
                ob_start();
                debug_print_backtrace();
                $e['trace'] = ob_get_clean();
                $e['trace'] .= '#' . (count($e['trace']) + 1) . ' {main}';
            }
            //CLI下输出
            if (IS_CLI) {
                //加载climate 库
                $climate = new \League\CLImate\CLImate;
                //友好输出
                $climate->out(iconv('UTF-8', 'gbk', $e['message']) . PHP_EOL . 'file:' . $e['file'] . PHP_EOL . 'line:' . $e['line']);
                //普通输出
                //exit(iconv('UTF-8', 'gbk', $e['message']) . PHP_EOL . 'file:' . $e['file'] . PHP_EOL . 'line:' . $e['line']);
            }
            //报错
            echo '<strong>Error:</strong> ' . $e['message'] . PHP_EOL . 'file:' . $e['file'] . PHP_EOL . 'line:' . $e['line'];
            if (APP_DEBUG) {
                echo "<br />" . '<strong>trace:</strong> ' . '<br />' . nl2br($e['trace']);
            }
            //throw new \ErrorException($e['message'], 0, $e['type'], $e['file'], $e['line']); //抛出错误异常
            return (true); //And prevent the PHP error handler from continuing
        }
        //非调试模式 一般是正式环境
        else {
            if (!C('ERROR_PAGE')) {
                redirect(C('ERROR_PAGE'));
            } else {
                $message      = is_array($error) ? $error['message'] : $error;
                $e['message'] = C('SHOW_ERROR_MESSAGE') ? $message : C('ERROR_MESSAGE');
            }
            //输出错误信息
            exit($e['message']);
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
        //throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        switch ($errno) {
            case E_ERROR: //1 致命的运行时错误
            case E_PARSE: //4 编译时语法解析错误。解析错误仅仅由分析器产生。
            case E_CORE_ERROR: //16 在PHP初始化启动过程中发生的致命错误。该错误类似 E_ERROR，但是是由PHP引擎核心产生的。
            case E_COMPILE_ERROR: //64 致命编译时错误。类似E_ERROR, 但是是由Zend脚本引擎产生的。
            case E_USER_ERROR: //256 用户产生的错误信息。类似 E_ERROR, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。
                //self::halt($errstr);
                throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
                break;
            default:
                break;
        }
    }

    /**
     * 自定义异常处理
     * @access public
     * @param string $exception 异常信息
     * @return void
     */
    public static function customException($exception)
    {
        //异常信息
        $e            = [];
        $e['trace']   = $exception->getTraceAsString(); //获取字符串类型异常追踪信息
        $e['message'] = $exception->getMessage(); //获取异常消息内容
        $e['code']    = $exception->getCode(); //异常代码
        $e['file']    = $exception->getFile(); //获取发生异常的程序文件名称
        $e['line']    = $exception->getLine(); //获取发生异常的代码在文件中的行号

        //记录日志
        //error_log($msg);
        // 发送404信息
        header('HTTP/1.1 404 Not Found');
        header('Status:404 Not Found');
        self::halt($e);
        //var_dump($msg);
    }

}
