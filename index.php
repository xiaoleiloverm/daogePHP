<?php
/**
 *入口文件
 */

// 应用入口文件

// 设置页面输出编码
header('Content-Type:text/html;charset=utf-8');

// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    die('require PHP > 5.3.0 !');
}

define('APP_PATH', __DIR__ . '/app/'); //应用目录
define('APP_COMMON_PATH', __DIR__ . '/Common/'); //公共目录
define('APP_DEBUG', true); //开启调试模式

//加载框架核心
require './daogePHP/init.php';
