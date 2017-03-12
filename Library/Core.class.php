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

use Library\Controller\Log\Log;

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
        // 加载核心文件 核心配置和核心函数等
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
     *加载扩展函数库
     */
    public static function extFunc()
    {
        //1.公共COMMON扩展函数
        is_file($path = APP_COMM_PATH . 'function.php') && include $path;
        //2.项目扩展函数 路由调度后按 MODULE_NAME 加载
        is_file($path = APP_PATH . MODULE_NAME . '/Common/function.php') && include $path;
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

        // //加载应用项目配置,可覆盖公共配置
        // var_dump(APP_PATH . MODULE_NAME . '/Config/config' . CONFIG_EXT);
        // if (is_file(APP_PATH . MODULE_NAME . '/Config/config' . CONFIG_EXT)) {
        //     C(include APP_PATH . MODULE_NAME . '/Config/config' . CONFIG_EXT);
        // }

        //加载扩展配置文件
        if ($extConf = C('LOAD_EXT_CONFIG')) {
            $extConf = explode(',', $extConf);
            foreach ($extConf as $value) {
                is_file(APP_CONFIG_PATH . $value . CONFIG_EXT) && C(include APP_CONFIG_PATH . $value . CONFIG_EXT);
            }
        }
        //扩展函数
        if ($extFile = C('LOAD_EXT_FILE')) {
            $extFile = explode(',', $extFile);
            foreach ($extFile as $value) {
                is_file(APP_COMM_PATH . $value . '.php') && include APP_COMM_PATH . $value . '.php';
            }
        }
    }

    /**
     *加载应用项目配置 路由调度后按 MODULE_NAME 加载
     */
    public static function ModuleConifg()
    {
        //加载应用项目配置,可覆盖公共配置
        if (is_file(APP_PATH . MODULE_NAME . '/Config/config' . CONFIG_EXT)) {
            C(include APP_PATH . MODULE_NAME . '/Config/config' . CONFIG_EXT);
        }
    }

    /**
     *路由调度
     */
    public static function urlDispatch()
    {
        //路由参数
        $router = [
            'URL_MODEL'      => C('URL_MODEL') ?: 0,
            'VAR_CONTROLLER' => C('VAR_CONTROLLER') ?: 'c',
            'VAR_ACTION'     => C('VAR_ACTION') ?: 'a',
            'VAR_MODULE'     => C('VAR_MODULE') ?: 'm',
        ];
        //初始化
        \Library\Controller\Route\Route::init($router); //传入参数进行初始化
        //url参数打包
        $makeUrl = \Library\Controller\Route\Route::makeUrl();
        return $makeUrl;
    }

    /**
     *加载http 控制器 C层
     */
    public static function http_C()
    {
        // //非设置url后缀 不调度
        // if (isset($_SERVER['REDIRECT_URL']) && strrpos($_SERVER['REDIRECT_URL'], '.' . C('URL_HTML_SUFFIX')) === false) {
        //     return '';
        // }
        define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])) ? true : false);
        //路由调度
        $makeUrl = self::urlDispatch();
        //控制器定义
        define('MODULE_NAME', $makeUrl['module'] ?: C('DEFAULT_MODULE')); //模块名常量
        define('CONTROLLER_NAME', $makeUrl['controller'] ?: C('DEFAULT_CONTROLLER')); //控制器名常量
        define('ACTION_NAME', $makeUrl['action'] ?: C('DEFAULT_ACTION')); //方法名常量
        $GLOBALS['_urlParam'] = $makeUrl['param']; //url 参数
        //var_dump($makeUrl, $_SERVER);
        //加载系统默认扩展函数
        \Library\Core::extFunc();
        //加载应用项目配置 路由调度后按 MODULE_NAME 加载
        \Library\Core::ModuleConifg();
        // 默认路由
        $df_module     = C('DEFAULT_MODULE') ? C('DEFAULT_MODULE') : 'Default'; // 默认模块名
        $df_controller = C('DEFAULT_CONTROLLER') ? C('DEFAULT_CONTROLLER') : 'Index'; // 默认控制器
        $df_action     = C('DEFAULT_ACTION') ? C('DEFAULT_ACTION') : 'index'; // 默认方法
        //根据路由加载控制器
        $layer  = C('DEFAULT_C_NAME');
        $class  = '\\' . MODULE_NAME . '\\' . $layer . '\\' . CONTROLLER_NAME . C('CONTROLLER_SUFFIX');
        $exe    = new $class;
        $action = ACTION_NAME . C('ACTION_SUFFIX');
        if (!preg_match('/^[A-Za-z](\w)*$/', $action)) {
            // 非法操作
            throw new \ReflectionException();
        }
        //执行控制器方法
        $exe->$action();
    }

    /**
     *加载http 模型 M层
     */
    public static function http_M()
    {
        //加载模型

        //实例化核心模型M
        //$Model = new \Library\Model\Model();
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

        //$log = new Log('info', 'local');
        //Log::record('debug', ['this is a {userName} info', 'framwork:{userName}'], ['extend' => ['function' => 'start', 'method' => 'public static'], 'replace' => ['{userName}' => 'daogePHP']]);

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
            require_once DAOGE_PATH . $class . EXT;
        } elseif (file_exists(APP_PATH . $class . EXT)) {
            require_once APP_PATH . $class . EXT;
        }
        //普通文件自动加载
        elseif (file_exists(DAOGE_PATH . $class . '.php')) {
            require_once DAOGE_PATH . $class . '.php';
        }
        //加载自定义外部命名空间类文件
        elseif (file_exists($class . EXT)) {
            require_once $class . EXT;
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
            $handler = new \Monolog\Handler\StreamHandler(APP_LOG_PATH . 'app_' . date('Y-m-d', time()) . '.log');
            $handler->setFormatter(new \Monolog\Formatter\LineFormatter(null, null, true, true)); //格式化消息,格式化时间,允许消息内有换行,忽略空白的消息(去掉[])
            $log = new \Library\Controller\Log\Log('emergency', 'local', $handler);
            $log->record('emergency', $e);
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
