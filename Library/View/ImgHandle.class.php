<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心,视图-图形资源句柄 类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\View;

use \Library\Construct\View\ImgHandleAbstract;

class ImgHandle extends ImgHandleAbstract
{

    /**
     * 构造方法初始化
     * @access public
     * @param string $charset 随机因子
     * @param int $codelen 验证码长度
     * @param int $width 宽度
     * @param int $height 高度
     * @param int $font 指定的字体文件
     * @param int $fontsize 指定字符大小
     * @param array|string fontcolor
     *        字符串逗号','隔开或者数组依次分别是所需要的颜色的红，绿，蓝成分;这些参数是0到 255 的整数或者十六进制的 0x00 到 0xFF
     * @return void
     */
    public function __construct($charset, $codelen = 4, $width = 130, $height = 50, $font, $fontsize, $fontcolor)
    {
        $charset  = $charset ?: 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
        $codelen  = $codelen ?: 4;
        $width    = $width ?: 130;
        $height   = $height ?: 50;
        $fontsize = $fontsize ?: 20;
        $font     = $font ?: dirname(__FILE__) . '/ValidateCode/font/elephant.ttf'; //注意字体路径要写对，否则显示不了图片

        $this->setCharset($charset);
        $this->setCodelen($codelen);
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setFont($font);
        $this->setFontsize($fontsize);
        $this->setFontcolor($fontcolor);
    }

    /**
     * 生成随机码
     * @access public
     */
    public function createCode()
    {
        $_len = strlen($this->charset) - 1;
        for ($i = 0; $i < $this->codelen; $i++) {
            $this->code .= $this->charset[mt_rand(0, $_len)];
        }
        return $this;
    }

    /**
     * 生成背景
     * @access public
     */
    public function createBg()
    {
        $this->imgHandle = imagecreatetruecolor($this->width, $this->height);
        $color           = imagecolorallocate($this->imgHandle, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($this->imgHandle, 0, $this->height, $this->width, 0, $color);
        return $this;
    }

    /**
     * 生成文字颜色
     * @access public
     */
    public function createFontColor()
    {
        //为一幅图像分配颜色
        $red                = $this->fontcolor[0] ?: mt_rand(0, 156);
        $green              = $this->fontcolor[1] ?: mt_rand(0, 156);
        $blue               = $this->fontcolor[2] ?: mt_rand(0, 156);
        $this->imgFontcolor = imagecolorallocate($this->imgHandle, $red, $green, $blue);
        return $this;
    }

    /**
     * 绘制图片字符
     * @access public
     */
    public function createFont()
    {
        $_x = $this->width / $this->codelen;
        for ($i = 0; $i < $this->codelen; $i++) {
            //为一幅图像分配颜色
            //$this->imgFontcolor = imagecolorallocate($this->imgHandle, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext($this->imgHandle, $this->fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $this->imgFontcolor, $this->font, $this->code[$i]);
        }
        return $this;
    }

    /**
     * 绘制干扰（线条、雪花等）
     * @access public
     */
    public function createDisturb()
    {
        //线条
        for ($i = 0; $i < 6; $i++) {
            $color = imagecolorallocate($this->imgHandle, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($this->imgHandle, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        //雪花
        for ($i = 0; $i < 100; $i++) {
            $color = imagecolorallocate($this->imgHandle, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($this->imgHandle, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
        return $this;
    }

    /**
     * 获取验证码字符
     * @access public
     */
    public function getCode()
    {
        return strtolower($this->code);
    }
}
