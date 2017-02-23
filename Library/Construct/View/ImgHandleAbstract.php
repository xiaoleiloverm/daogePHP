<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心,视图-图形资源句柄生成 抽象类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Construct\View;

abstract class ImgHandleAbstract implements ImgHandleInterface
{
    public $imgHandle;

    public $charset; //随机因子
    public $code; //验证码
    public $codelen; //验证码长度
    public $width; //宽度
    public $height; //高度
    public $font; //指定的字体
    public $fontsize; //指定字体大小
    public $fontcolor; //指定字体颜色

    /**
     * 获取随机因子
     * @access public
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * 设置随机因子
     * @access public
     */
    public function setCharset($charset)
    {
        $charset              = $charset ?: 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
        return $this->charset = $charset;
    }

    /**
     * 获取验证码长度
     * @access public
     */
    public function getCodelen()
    {
        return $this->codelen;
    }

    /**
     * 获取验证码长度
     * @access public
     */
    public function setCodelen($codelen = 4)
    {
        return $this->codelen = $codelen;
    }

    /**
     * 获取验证码宽度
     * @access public
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * 设置验证码宽度
     * @access public
     */
    public function setWidth($width = 130)
    {
        return $this->width = $width;
    }

    /**
     * 获取验证码高度
     * @access public
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * 设置验证码高度
     * @access public
     */
    public function setHeight($height = 50)
    {
        return $this->height = $height;
    }

    /**
     * 获取验证码字体
     * @access public
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * 设置验证码字体
     * @access public
     */
    public function setFont($font)
    {
        return is_file($font) ? $this->font = $font : null;
    }

    /**
     * 获取验证码字体大小
     * @access public
     */
    public function getFontsize()
    {
        return $this->fontsize;
    }

    /**
     * 设置验证码字体
     * @access public
     */
    public function setFontsize($fontsize = 20)
    {
        return $this->fontsize = $fontsize;
    }

    /**
     * 获取验证码字体颜色
     * @access public
     */
    public function getFontcolor()
    {
        return $this->fontcolor;
    }

    /**
     * 设置验证码字体颜色
     * @param array fontcolor red,green,blue分别是所需要的颜色的红，绿，蓝成份;这些参数是 0 到 255 的整数或者十六进制的 0x00 到 0xFF
     * @access public
     */
    public function setFontcolor($fontcolor = [])
    {
        if (!is_array($fontcolor)) {
            $fontcolor = explode(',', $fontcolor);
        }
        //默认为随机三种颜色成份
        $red             = $this->fontcolor[0] ?: mt_rand(0, 156);
        $green           = $this->fontcolor[1] ?: mt_rand(0, 156);
        $blue            = $this->fontcolor[2] ?: mt_rand(0, 156);
        $this->fontcolor = [$red, $green, $blue];
        return $this;
    }

    /**
     * 生成随机码
     * @access public
     */
    public function createCode()
    {

    }

    /**
     * 生成背景
     * @access public
     */
    public function createBg()
    {

    }

    /**
     * 生成文字颜色
     * @access public
     */
    public function createFontColor()
    {

    }

    /**
     * 生成文字
     * @access public
     */
    public function createFont()
    {

    }

    /**
     * 生成干扰（线条、雪花）
     * @access public
     */
    public function createDisturb()
    {

    }

    /**
     * 获取验证码
     * @access public
     */
    public function getCode()
    {

    }
}
