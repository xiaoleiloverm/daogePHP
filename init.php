<?php
/**
 * daogePHP - A PHP Framework For Web
 *
 * @author   leilu<xiaoleiloverm@gmail.com>
 */

//----------------------------------
// 初始化文件
//----------------------------------

// 记录开始运行时间
$GLOBALS['_beginTime'] = microtime(true);
// 记录内存初始使用
define('MEMORY_LIMIT_ON', function_exists('memory_get_usage'));
if (MEMORY_LIMIT_ON) {
    $GLOBALS['_startUseMems'] = memory_get_usage();
}

//版本
const DAOGE_VERSION = '1.0.0';

// 类文件后缀
const EXT = '.class.php';
//配置文件后缀
const CONFIG_EXT = '.php';

//系统常量设置
defined('DAOGE_PATH') or define('DAOGE_PATH', __dir__ . '/'); //框架根目录
defined('LIB_PATH') or define('LIB_PATH', DAOGE_PATH . 'Library' . '/'); //核心类库目录
defined('COMMON_PATH') or define('COMMON_PATH', LIB_PATH . 'Common' . '/'); //核心文件（函数）目录
defined('CONFIG_PATH') or define('CONFIG_PATH', LIB_PATH . 'Config' . '/'); //核心配置目录
defined('VENDOR_PATH') or define('VENDOR_PATH', LIB_PATH . 'vendor' . '/'); //第三方库目录

defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/');
defined('APP_DEBUG') or define('APP_DEBUG', false); //是否开启调试模式
//cgi模式常量
define('IS_CGI', (0 === strpos(PHP_SAPI, 'cgi') || false !== strpos(PHP_SAPI, 'fcgi')) ? 1 : 0);
//win环境常量
define('IS_WIN', strstr(PHP_OS, 'WIN') ? 1 : 0);
//cli命令行模式常量
define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);

//请求时间常量
define('NOW_TIME', $_SERVER['REQUEST_TIME']);
//http参数常量
define('REQUEST_METHOD', strtolower($_SERVER['REQUEST_METHOD']));
define('IS_GET', (REQUEST_METHOD === 'get') ? true : false);
define('IS_POST', (REQUEST_METHOD === 'post') ? true : false);
define('IS_PUT', (REQUEST_METHOD === 'put') ? true : false);
define('IS_DELETE', (REQUEST_METHOD === 'delete') ? true : false);

//加载应用核心类文件
require_once LIB_PATH . 'Core.class.php';
// 应用程序执行入口
\Library\Core::start();
