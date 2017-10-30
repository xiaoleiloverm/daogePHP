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
     * @param string $handType 处理类型 1:纯图片处理;0(默认):验证码图片处理
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
    public function __construct($handType = 0, $charset, $codelen = 4, $width = 130, $height = 50, $font, $fontsize, $fontcolor)
    {
        //存图片处理
        if ($handType) {

        }
        //验证码图片处理
        else {

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
        return $this->code;
    }

    /**
     * 取得图像信息
     * @public
     * @access public
     * @param string $image 图像文件名
     * @return mixed
     */

    public function getImageInfo($img)
    {
        $imageInfo = getimagesize($img);
        if ($imageInfo !== false) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
            $imageSize = filesize($img);
            $info      = array(
                "width"  => $imageInfo[0],
                "height" => $imageInfo[1],
                "type"   => $imageType,
                "size"   => $imageSize,
                "mime"   => $imageInfo['mime'],
            );
            return $info;
        } else {
            return false;
        }
    }

    /**
     * 为图片添加水印
     * @public
     * @param string $source 原文件名
     * @param string $water  水印图片
     * @param string $$savename  添加水印后的图片名
     * @param string $alpha  水印的透明度
     * @return void
     */
    public function water($source, $water, $savename = null, $alpha = 80)
    {
        //检查文件是否存在
        if (!file_exists($source) || !file_exists($water)) {
            return false;
        }

        //图片信息
        $sInfo = self::getImageInfo($source);
        $wInfo = self::getImageInfo($water);

        //如果图片小于水印图片，不生成图片
        if ($sInfo["width"] < $wInfo["width"] || $sInfo['height'] < $wInfo['height']) {
            return false;
        }

        //建立图像
        $sCreateFun = "imagecreatefrom" . $sInfo['type'];
        $sImage     = $sCreateFun($source);
        $wCreateFun = "imagecreatefrom" . $wInfo['type'];
        $wImage     = $wCreateFun($water);

        //设定图像的混色模式
        imagealphablending($wImage, true);

        //图像位置,默认为右下角右对齐
        $posY = $sInfo["height"] - $wInfo["height"];
        $posX = $sInfo["width"] - $wInfo["width"];

        //生成混合图像
        imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'], $wInfo['height'], $alpha);

        //输出图像
        $ImageFun = 'Image' . $sInfo['type'];
        //如果没有给出保存文件名，默认为原图像名
        if (!$savename) {
            $savename = $source;
            @unlink($source);
        }
        //保存图像
        $ImageFun($sImage, $savename);
        imagedestroy($sImage);
    }

    /**
     * 输出图片
     * @static public
     * @return void
     */
    public function showImg($imgFile, $text = '', $x = '10', $y = '10', $alpha = '50')
    {
        //获取图像文件信息
        //2007/6/26 增加图片水印输出，$text为图片的完整路径即可
        $info = self::getImageInfo($imgFile);
        if ($info !== false) {
            $createFun = str_replace('/', 'createfrom', $info['mime']);
            $im        = $createFun($imgFile);
            if ($im) {
                $ImageFun = str_replace('/', '', $info['mime']);
                //水印开始
                if (!empty($text)) {
                    $tc = imagecolorallocate($im, 0, 0, 0);
                    if (is_file($text) && file_exists($text)) {
//判断$text是否是图片路径
                        // 取得水印信息
                        $textInfo   = self::getImageInfo($text);
                        $createFun2 = str_replace('/', 'createfrom', $textInfo['mime']);
                        $waterMark  = $createFun2($text);
                        //$waterMark=imagecolorallocatealpha($text,255,255,0,50);
                        $imgW = $info["width"];
                        $imgH = $info["width"] * $textInfo["height"] / $textInfo["width"];
                        //$y    =   ($info["height"]-$textInfo["height"])/2;
                        //设置水印的显示位置和透明度支持各种图片格式
                        imagecopymerge($im, $waterMark, $x, $y, 0, 0, $textInfo['width'], $textInfo['height'], $alpha);
                    } else {
                        imagestring($im, 80, $x, $y, $text, $tc);
                    }
                    //ImageDestroy($tc);
                }
                //水印结束
                if ($info['type'] == 'png' || $info['type'] == 'gif') {
                    imagealphablending($im, false); //取消默认的混色模式
                    imagesavealpha($im, true); //设定保存完整的 alpha 通道信息
                }
                Header("Content-type: " . $info['mime']);
                $ImageFun($im);
                @ImageDestroy($im);
                return;
            }

            //保存图像
            $ImageFun($sImage, $savename);
            imagedestroy($sImage);
            //获取或者创建图像文件失败则生成空白PNG图片
            $im  = imagecreatetruecolor(80, 30);
            $bgc = imagecolorallocate($im, 255, 255, 255);
            $tc  = imagecolorallocate($im, 0, 0, 0);
            imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
            imagestring($im, 4, 5, 5, "no pic", $tc);
            self::output($im);
            return;
        }
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
        // 获取原图信息
        $info = self::getImageInfo($image);
        if ($info !== false) {
            $srcWidth  = $info['width'];
            $srcHeight = $info['height'];
            $type      = empty($type) ? $info['type'] : $type;
            $type      = strtolower($type);
            $interlace = $interlace ? 1 : 0;
            unset($info);
            $scale = min($maxWidth / $srcWidth, $maxHeight / $srcHeight); // 计算缩放比例
            if ($scale >= 1) {
                // 超过原图大小不再缩略
                $width  = $srcWidth;
                $height = $srcHeight;
            } else {
                // 缩略图尺寸
                $width  = (int) ($srcWidth * $scale);
                $height = (int) ($srcHeight * $scale);
            }

            // 载入原图
            $createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
            if (!function_exists($createFun)) {
                return false;
            }
            $srcImg = $createFun($image);

            //创建缩略图
            if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
                $thumbImg = imagecreatetruecolor($width, $height);
            } else {
                $thumbImg = imagecreate($width, $height);
            }

            //png和gif的透明处理 by luofei614
            if ('png' == $type) {
                imagealphablending($thumbImg, false); //取消默认的混色模式（为解决阴影为绿色的问题）
                imagesavealpha($thumbImg, true); //设定保存完整的 alpha 通道信息（为解决阴影为绿色的问题）
            } elseif ('gif' == $type) {
                $trnprt_indx = imagecolortransparent($srcImg);
                if ($trnprt_indx >= 0) {
                    //its transparent
                    $trnprt_color = imagecolorsforindex($srcImg, $trnprt_indx);
                    $trnprt_indx  = imagecolorallocate($thumbImg, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                    imagefill($thumbImg, 0, 0, $trnprt_indx);
                    imagecolortransparent($thumbImg, $trnprt_indx);
                }
            }
            // 复制图片
            if (function_exists("ImageCopyResampled")) {
                imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
            } else {
                imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
            }

            // 对jpeg图形设置隔行扫描
            if ('jpg' == $type || 'jpeg' == $type) {
                imageinterlace($thumbImg, $interlace);
            }

            // 生成图片
            $imageFun = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
            $imageFun($thumbImg, $thumbname);
            imagedestroy($thumbImg);
            imagedestroy($srcImg);
            return $thumbname;
        }
        return false;
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
        // 获取原图信息
        $info = self::getImageInfo($image);
        if ($info !== false) {
            $srcWidth  = $info['width'];
            $srcHeight = $info['height'];
            $type      = empty($type) ? $info['type'] : $type;
            $type      = strtolower($type);
            $interlace = $interlace ? 1 : 0;
            unset($info);
            $scale = max($maxWidth / $srcWidth, $maxHeight / $srcHeight); // 计算缩放比例
            //判断原图和缩略图比例 如原图宽于缩略图则裁掉两边 反之..
            if ($maxWidth / $srcWidth > $maxHeight / $srcHeight) {
                //高于
                $srcX      = 0;
                $srcY      = ($srcHeight - $maxHeight / $scale) / 2;
                $cutWidth  = $srcWidth;
                $cutHeight = $maxHeight / $scale;
            } else {
                //宽于
                $srcX      = ($srcWidth - $maxWidth / $scale) / 2;
                $srcY      = 0;
                $cutWidth  = $maxWidth / $scale;
                $cutHeight = $srcHeight;
            }

            // 载入原图
            $createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
            $srcImg    = $createFun($image);

            //创建缩略图
            if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
                $thumbImg = imagecreatetruecolor($maxWidth, $maxHeight);
            } else {
                $thumbImg = imagecreate($maxWidth, $maxHeight);
            }

            // 复制图片
            if (function_exists("ImageCopyResampled")) {
                imagecopyresampled($thumbImg, $srcImg, 0, 0, $srcX, $srcY, $maxWidth, $maxHeight, $cutWidth, $cutHeight);
            } else {
                imagecopyresized($thumbImg, $srcImg, 0, 0, $srcX, $srcY, $maxWidth, $maxHeight, $cutWidth, $cutHeight);
            }

            if ('gif' == $type || 'png' == $type) {
                //imagealphablending($thumbImg, false);//取消默认的混色模式
                //imagesavealpha($thumbImg,true);//设定保存完整的 alpha 通道信息
                $background_color = imagecolorallocate($thumbImg, 0, 255, 0); //  指派一个绿色
                imagecolortransparent($thumbImg, $background_color); //  设置为透明色，若注释掉该行则输出绿色的图
            }

            // 对jpeg图形设置隔行扫描
            if ('jpg' == $type || 'jpeg' == $type) {
                imageinterlace($thumbImg, $interlace);
            }

            // 生成图片
            $imageFun = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
            $imageFun($thumbImg, $thumbname);
            imagedestroy($thumbImg);
            imagedestroy($srcImg);
            return $thumbname;
        }
        return false;
    }

    /**
     * 把图像转换成字符显示
     * @public
     * @access public
     * @param string $image  要显示的图像
     * @param string $type  图像类型，默认自动获取
     * @return string
     */
    public function showASCIIImg($image, $string = '', $type = '')
    {
        $info = self::getImageInfo($image);
        if ($info !== false) {
            $type = empty($type) ? $info['type'] : $type;
            unset($info);
            // 载入原图
            $createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
            $im        = $createFun($image);
            $dx        = imagesx($im);
            $dy        = imagesy($im);
            $i         = 0;
            $out       = '<span style="padding:0px;margin:0;line-height:100%;font-size:1px;">';
            set_time_limit(0);
            for ($y = 0; $y < $dy; $y++) {
                for ($x = 0; $x < $dx; $x++) {
                    $col = imagecolorat($im, $x, $y);
                    $rgb = imagecolorsforindex($im, $col);
                    $str = empty($string) ? '*' : $string[$i++];
                    $out .= sprintf('<span style="margin:0px;color:#%02x%02x%02x">' . $str . '</span>', $rgb['red'], $rgb['green'], $rgb['blue']);
                }
                $out .= "<br>\n";
            }
            $out .= '</span>';
            imagedestroy($im);
            return $out;
        }
        return false;
    }

    /**
     * 生成UPC-A条形码
     * @public
     * @param string $type 图像格式
     * @param string $type 图像格式
     * @param string $lw  单元宽度
     * @param string $hi   条码高度
     * @return string
     */
    public function UPCA($code, $type = 'png', $lw = 2, $hi = 100)
    {
        static $Lencode = array('0001101', '0011001', '0010011', '0111101', '0100011',
            '0110001', '0101111', '0111011', '0110111', '0001011');
        static $Rencode = array('1110010', '1100110', '1101100', '1000010', '1011100',
            '1001110', '1010000', '1000100', '1001000', '1110100');
        $ends   = '101';
        $center = '01010';
        /* UPC-A Must be 11 digits, we compute the checksum. */
        if (strlen($code) != 11) {
            die("UPC-A Must be 11 digits.");
        }
        /* Compute the EAN-13 Checksum digit */
        $ncode = '0' . $code;
        $even  = 0;
        $odd   = 0;
        for ($x = 0; $x < 12; $x++) {
            if ($x % 2) {
                $odd += $ncode[$x];
            } else {
                $even += $ncode[$x];
            }
        }
        $code .= (10 - (($odd * 3 + $even) % 10)) % 10;
        /* Create the bar encoding using a binary string */
        $bars = $ends;
        $bars .= $Lencode[$code[0]];
        for ($x = 1; $x < 6; $x++) {
            $bars .= $Lencode[$code[$x]];
        }
        $bars .= $center;
        for ($x = 6; $x < 12; $x++) {
            $bars .= $Rencode[$code[$x]];
        }
        $bars .= $ends;
        /* Generate the Barcode Image */
        if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($lw * 95 + 30, $hi + 30);
        } else {
            $im = imagecreate($lw * 95 + 30, $hi + 30);
        }
        $fg = ImageColorAllocate($im, 0, 0, 0);
        $bg = ImageColorAllocate($im, 255, 255, 255);
        ImageFilledRectangle($im, 0, 0, $lw * 95 + 30, $hi + 30, $bg);
        $shift = 10;
        for ($x = 0; $x < strlen($bars); $x++) {
            if (($x < 10) || ($x >= 45 && $x < 50) || ($x >= 85)) {
                $sh = 10;
            } else {
                $sh = 0;
            }
            if ($bars[$x] == '1') {
                $color = $fg;
            } else {
                $color = $bg;
            }
            ImageFilledRectangle($im, ($x * $lw) + 15, 5, ($x + 1) * $lw + 14, $hi + 5 + $sh, $color);
        }
        /* Add the Human Readable Label */
        ImageString($im, 4, 5, $hi - 5, $code[0], $fg);
        for ($x = 0; $x < 5; $x++) {
            ImageString($im, 5, $lw * (13 + $x * 6) + 15, $hi + 5, $code[$x + 1], $fg);
            ImageString($im, 5, $lw * (53 + $x * 6) + 15, $hi + 5, $code[$x + 6], $fg);
        }
        ImageString($im, 4, $lw * 95 + 17, $hi - 5, $code[11], $fg);
        /* Output the Header and Content. */
        self::output($im, $type);
    }

    //输出
    public function output($im, $type = 'png', $filename = '')
    {
        header("Content-type: image/" . $type);
        $ImageFun = 'image' . $type;
        if (empty($filename)) {
            $ImageFun($im);
        } else {
            $ImageFun($im, $filename);
        }
        imagedestroy($im);
    }
}
