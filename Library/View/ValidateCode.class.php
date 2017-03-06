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
        $this->getImgHandle($imgHandle);
    }

    /**
     * 生成验证码
     * @access public
     * @return void
     */
    public function createVcode()
    {
        // createBg 创建一个背景
        // createCode 生成验证码字符
        // createDisturb 绘制干扰
        // createFontColor 返回一个标识符，代表了由给定的 RGB 成分组成的颜色。
        // createFont 绘制图片字符
        $this->imgHandle->createBg()->createCode()->createDisturb()->createFontColor()->createFont();
        return $this;
    }

    /**
     * 输出图片
     * @access public
     * @param string type png|jpeg 输出图片类型
     * @return void
     */
    public function outPut($type = 'png')
    {
        if ($type == 'png') {
            $suffix = $type;
        } else if ($type == 'jpg' || $type == 'jpeg') {
            $suffix = 'jpeg';
        }
        header('Content-type:image/' . $suffix);
        if ($this->imgHandle && is_object($this->imgHandle)) {
            imagepng($this->imgHandle->imgHandle);
            imagedestroy($this->imgHandle->imgHandle);
        }
    }

    /**
     * 存储验证码
     * @access public
     * @param string key 验证码存储的key
     * @return string
     */
    public function saveCode($key = 'Vcode')
    {
        //获取当前生成好的验证码
        $code = $this->getCode();
        //存储到session
        session($key, $code);
        return session($key);
    }

    /**
     * 对外生成验证码图片
     * @access public
     * @param string type png|jpeg 输出图片类型
     * @param string key  保存验证码的key
     * @return void
     */
    public function doimg($type = 'png', $key = 'Vcode')
    {
        $this->createVcode(); //生成验证码
        $this->saveCode($key); //保存验证码
        $this->outPut($type); //输出图片
    }

    /**
     * 检查验证码
     * @access public
     * @param string code 验证码
     * @param string key 验证码保存的key
     * @param boole flag 是否区分大小写
     * @return true|false
     */
    public function checkVcode($code, $key = 'Vcode', $flag = true)
    {
        if (empty($code)) {
            return false;
        }
        //当前验证码字符
        $currentCode          = $this->getSaveCode($key);
        $flag && $currentCode = strtolower($currentCode);
        if ($code && $code == $currentCode) {
            return true;
        }
        return false;
    }

    //获取验证码(生成的验证码 未保存)
    public function getCode()
    {
        return $this->imgHandle->getCode();
    }

    /**
     * 获取存储的验证码(保存的验证码,即生成的验证码需要保存供外部调用)
     * @access public
     * @param string key 验证码存储的key
     * @return string
     */
    public function getSaveCode($key = 'Vcode')
    {
        return session($key);
    }
}
