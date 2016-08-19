<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心控制器 -> Monolog 1.1.* 日志类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace library\controller;

class Log
{

    public $msg   = ''; //错误信息
    public $error = 0; //错误码

    /**
     * 记录日志
     * @param string $str 记录信息
     * @return void
     */
    public function record($str)
    {

        // add records to the log
        $log->addWarning($str);
        $log->addError($str);
    }

    /**
     * 创建日志文件
     * @param string $dir 文件路径
     * @param string $name 文件名
     * @return void
     */
    public function createLogFile($dir, $name)
    {
        if (!$name || !$dir) {
            $this->error = '10001';
            $this->msg   = '目录不能为空或文件名不能为空';
            return;
        }
        if (!is_dir($dir)) {
            $this->error = '10002';
            $this->msg   = '不是一个目录';
            return;
        }
        //目录可写性

        // create a log channel
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler('path/to/your.log', Logger::WARNING));
    }
}
