<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心,视图-验证码生成抽象类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Construct\View;

use \Library\Construct\View\ImgHandleInterface;

abstract class ValidateCodeAbstract implements ValidateCodeInterface
{
    public $imgHandle; //图形资源句柄

    /**
     * 生成验证码
     * @access public
     * @return void
     */
    public function createVcode()
    {

    }

    /**
     * 获取图形资源句柄
     * @access public
     * @param object ImgHandleInterface 图形资源句柄对象
     * @return void
     */
    public function getImgHandle(ImgHandleInterface $imgHandle)
    {
        if (!is_object($imgHandle)) {
            return;
        }
        return $this->imgHandle = $imgHandle;
    }
}
