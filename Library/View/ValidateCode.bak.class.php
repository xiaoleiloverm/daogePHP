<?php
namespace Library\View;

use \Library\Construct\View\ValidateCodeAbstract;

//验证码类
class ValidateCode extends ValidateCodeAbstract
{
    public $charset; //随机因子
    public $code; //验证码
    public $codelen; //验证码长度
    public $width; //宽度
    public $height; //高度
    public $img; //图形资源句柄
    public $font; //指定的字体
    public $fontsize; //指定字体大小
    public $fontcolor; //指定字体颜色

    /**
     * 构造方法初始化
     * @access public
     * @param string $charset 随机因子
     * @param int $codelen 验证码长度
     * @param int $width 宽度
     * @param int $height 高度
     * @param int $font 指定的字体文件
     * @param int $fontsize 指定字体大小
     * @return void
     */
    public function __construct($charset, $codelen = 4, $width = 130, $height = 50, $font, $fontsize)
    {
        $this->charset  = $charset ?: 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
        $this->codelen  = $codelen ?: 4;
        $this->width    = $width ?: 130;
        $this->height   = $height ?: 50;
        $this->fontsize = $fontsize ?: 20;
        $this->font     = dirname(__FILE__) . '/font/elephant.ttf'; //注意字体路径要写对，否则显示不了图片
    }

    /**
     * 生成随机码
     * @access public
     * @return void
     */
    public function createCode()
    {
        $_len = strlen($this->charset) - 1;
        for ($i = 0; $i < $this->codelen; $i++) {
            $this->code .= $this->charset[mt_rand(0, $_len)];
        }
    }

    //生成背景
    public function createBg()
    {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color     = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
    }

    //生成文字
    public function createFont()
    {
        $_x = $this->width / $this->codelen;
        for ($i = 0; $i < $this->codelen; $i++) {
            //$this->fontcolor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $this->fontcolor, $this->font, $this->code[$i]);
        }
    }

    //生成干扰（线条、雪花）
    public function createDisturb()
    {
        //线条
        for ($i = 0; $i < 6; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        //雪花
        for ($i = 0; $i < 100; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
    }

    //输出
    public function outPut()
    {
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }

    //对外生成
    public function doimg()
    {
        $this->createBg();
        $this->createCode();
        $this->createLine();
        $this->createFont();
        $this->outPut();
    }

    //获取验证码
    public function getCode()
    {
        return strtolower($this->code);
    }
}
