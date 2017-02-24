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
     * 存储验证码
     * @access public
     * @param string key 验证码存储的key
     * @return string
     */
    public function saveCode($key = 'Vcode')
    {

    }

    /**
     * 输出图片
     * @access public
     * @param string type png|jpeg 输出图片类型
     * @return void
     */
    public function outPut($type = 'png')
    {

    }

    /**
     * 检查验证码
     * @access public
     * @param string code 验证码
     * @param string key 验证码报错的key
     * @return true|false
     */
    public function checkVcode($code, $key = 'Vcode')
    {

    }

    /**
     * 获取验证码
     * @access public
     * @return string
     */
    public function getCode()
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
