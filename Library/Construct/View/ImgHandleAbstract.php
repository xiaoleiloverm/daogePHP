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

    /**
     * 取得图像信息
     * @static
     * @access public
     * @param string $image 图像文件名
     * @return mixed
     */

    public function getImageInfo($img)
    {

    }

    /**
     * 为图片添加水印
     * @static public
     * @param string $source 原文件名
     * @param string $water  水印图片
     * @param string $$savename  添加水印后的图片名
     * @param string $alpha  水印的透明度
     * @return void
     */
    public function water($source, $water, $savename = null, $alpha = 80)
    {

    }

    /**
     * 输出图片
     * @static public
     * @return void
     */
    public function showImg($imgFile, $text = '', $x = '10', $y = '10', $alpha = '50')
    {

    }

    /**
     * 生成缩略图
     * @public
     * @access public
     * @param string $image  原图
     * @param string $type 图像格式
     * @param string $thumbname 缩略图文件名
     * @param string $maxWidth  宽度
     * @param string $maxHeight  高度
     * @param string $position 缩略图保存目录
     * @param boolean $interlace 启用隔行扫描
     * @return void
     */
    public function thumb($image, $thumbname, $type = '', $maxWidth = 200, $maxHeight = 50, $interlace = true)
    {

    }

    /**
     * 生成特定尺寸缩略图 解决原版缩略图不能满足特定尺寸的问题 PS：会裁掉图片不符合缩略图比例的部分
     * @public
     * @access public
     * @param string $image  原图
     * @param string $type 图像格式
     * @param string $thumbname 缩略图文件名
     * @param string $maxWidth  宽度
     * @param string $maxHeight  高度
     * @param boolean $interlace 启用隔行扫描
     * @return void
     */
    public function thumb2($image, $thumbname, $type = '', $maxWidth = 200, $maxHeight = 50, $interlace = true)
    {

    }

    /**
     * 把图像转换成字符显示
     * @static
     * @access public
     * @param string $image  要显示的图像
     * @param string $type  图像类型，默认自动获取
     * @return string
     */
    public function showASCIIImg($image, $string = '', $type = '')
    {

    }

    /**
     * 生成UPC-A条形码
     * @static
     * @param string $type 图像格式
     * @param string $type 图像格式
     * @param string $lw  单元宽度
     * @param string $hi   条码高度
     * @return string
     */
    public function UPCA($code, $type = 'png', $lw = 2, $hi = 100)
    {

    }
}
