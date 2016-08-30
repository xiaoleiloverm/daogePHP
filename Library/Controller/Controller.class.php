<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心控制器
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Controller;

class Controller
{

    /**
     *加载视图
     */
    public function display()
    {
        //框架控制器根目录
        include APP_PATH . 'Viem' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'Index' . DIRECTORY_SEPARATOR . 'index.html'
    }

    /**
     *
     */
    public function test()
    {
        echo 'this is test';
    }
}
