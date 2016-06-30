<?php
/**
 * daogePHP - A PHP Framework For Web
 *
 * @author   leilu<xiaoleiloverm@gmail.com>
 */

//----------------------------------
// 初始化文件
//----------------------------------

//版本
const DAOGE_VERSION = '1.0.0';

//系统常量设置
defined('DAOGE_VERSION') or define('DAOGE_VERSION', __dir__ . '/'); //框架根目录
defined('LIB_PATH') or define('LIB_PATH', DAOGE_VERSION . 'library' . '/'); //核心类库目录

//加载应用初始化类文件
require_once LIB_PATH . 'init.class.php';
