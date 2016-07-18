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

//核心初始化
require './daogePHP/init.php';
