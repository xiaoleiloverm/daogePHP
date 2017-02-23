<?php

/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心,视图-图形资源句柄生成 接口类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Construct\View;

interface ImgHandleInterface
{
    /**
     * 生成随机码
     * @access public
     */
    public function createCode();

    /**
     * 生成随机码
     * @access public
     */
    public function createBg();

    /**
     * 生成文字颜色
     * @access public
     */
    public function createFontColor();

    /**
     * 生成文字
     * @access public
     */
    public function createFont();

    /**
     * 生成干扰（线条、雪花）
     * @access public
     */
    public function createDisturb();

    /**
     * 获取验证码
     * @access public
     */
    public function getCode();

}
