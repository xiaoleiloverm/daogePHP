<?php
/**
 * daogePHP - A PHP Framework For Web
 *
 * @author   leilu<xiaoleiloverm@gmail.com>
 */

//----------------------------------
// daogePHP 核心配置加载引导数组
//----------------------------------
return [
    //配置文件
    'config' => [
        CONFIG_PATH . 'config/tradition' . CONFIG_EXT, //惯例配置
    ],
    //核心配置文件
    'core'   => [
        COMMON_PATH . 'functions' . CONFIG_EXT, //核心函数
    ],
];
