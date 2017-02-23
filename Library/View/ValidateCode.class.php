<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心,视图-验证码生成 类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\View;

use \Library\Construct\View\ImgHandleAbstract;
use \Library\Construct\View\ValidateCodeAbstract;

//验证码类
class ValidateCode extends ValidateCodeAbstract
{

    /**
     * 构造方法初始化
     * @access public
     * @param obj ImgHandleAbstract 图形资源句柄
     * @return void
     */
    public function __construct(ImgHandleAbstract $imgHandle)
    {
        //var_dump($imgHandle);
        $this->getImgHandle($imgHandle);
    }

    /**
     * 生成验证码
     * @access public
     * @return void
     */
    public function createVcode()
    {
        //TODO
        // createFontColor 返回一个标识符，代表了由给定的 RGB 成分组成的颜色。
        //red，green 和 blue 分别是所需要的颜色的红，绿，蓝成分。这些参数是 0 到 255 的整数或者十六进制的 0x00 到 0xFF
        $this->imgHandle->createBg()->createCode()->createDisturb()->createFontColor()->createFont();
        return $this;
    }

    //输出
    public function outPut()
    {
        //TODO
        var_dump($this->imgHandle);exit;
        header('Content-type:image/png');
        if ($this->imgHandle && is_object($this->imgHandle)) {
            imagepng($this->imgHandle);
            imagedestroy($this->imgHandle);
        }
    }

    //对外生成
    public function doimg()
    {
        $this->createVcode(); //生成
        $this->outPut(); //输出
    }

    //获取验证码
    public function getCode()
    {
        return $this->imgHandle->getCode();
    }
}
