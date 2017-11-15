<?php

namespace Library\Protocol;

use Library\Construct\Protocol\StreamAbstract;

//自定义流协议
class Stream extends StreamAbstract
{

    //设置
    public function setData($string)
    {
        return $this->string = $string;
    }

    //获取
    public function getData()
    {
        return $this->string ?: '';
    }

    //打开流资源
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        // $url = parse_url($path);
        // if ($url == false) {
        //     $index = (int) strpos($path, '//');
        //     $index = $index ? $index + 2 : 0;
        //     $file  = substr($path, $index);
        // } else {
        //     $file = $url['path'];
        // }
        $index = (int) strpos($path, '//');
        $index = $index ? $index + 2 : 0;
        $file  = substr($path, $index);
        $file  = trim($file, '/');
        if (is_file($file)) {
            $this->string = file_get_contents($file);
            // try {
            //     //file_get_contents('file_path or url');
            //     $this->string = file_get_contents($file);
            // } catch (Exception $e) {
            //     var_dump('错误');
            //     var_dump($e);exit;
            // }
        } else {
            $this->string = $file;
        }
        //var_dump($url, $file, $path, $this->string);
        $this->position = 0;
        return true; //return true/false 直接输出内容/不输出
    }

    //读取流
    public function stream_read($count)
    {
        $ret = substr($this->string, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    //流结束
    public function stream_eof()
    {

    }

    //流开始
    public function stream_stat()
    {

    }
}
