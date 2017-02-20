<?php

/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心验证码接口类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Construct\View;

use \Library\Construct\View\ImgHandleInterface;

/**
 * 缓存适配器接口
 */
interface ValidateCodeInterface
{
    /**
     * 生成验证码
     * @access public
     * @return void
     */
    public function createVcode();

    /**
     * 获取图形资源句柄
     * @access public
     * @param object ImgHandleInterface 图形资源句柄对象
     * @return void
     */
    public function getImgHandle(ImgHandleInterface $imgHandle);

}
