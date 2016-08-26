<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架文件类库-管道通信类,PHP多进程编程:管道通信
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
class Pipe
{
    public $fifoPath;
    private $w_pipe;
    private $r_pipe;

    /**
     * 自动创建一个管道
     *
     * @param string $name 管道名字
     * @param int $mode  管道的权限，默认任何用户组可以读写
     */
    public function __construct($name = 'pipe', $mode = 0666)
    {
        $fifoPath = "/tmp/$name." . posix_getpid();
        if (!file_exists($fifoPath)) {
            if (!posix_mkfifo($fifoPath, $mode)) {
                error("create new pipe ($name) error.");
                return false;
            }
        } else {
            error("pipe ($name) has exit.");
            return false;
        }
        $this->fifoPath = $fifoPath;
    }

///////////////////////////////////////////////////
    //  写管道函数开始
    ///////////////////////////////////////////////////
    public function open_write()
    {
        $this->w_pipe = fopen($this->fifoPath, 'w');
        if ($this->w_pipe == null) {
            error("open pipe {$this->fifoPath} for write error.");
            return false;
        }
        return true;
    }

    public function write($data)
    {
        return fwrite($this->w_pipe, $data);
    }

    public function write_all($data)
    {
        $w_pipe = fopen($this->fifoPath, 'w');
        fwrite($w_pipe, $data);
        fclose($w_pipe);
    }

    public function close_write()
    {
        return fclose($this->w_pipe);
    }
/////////////////////////////////////////////////////////
    /// 读管道相关函数开始
    ////////////////////////////////////////////////////////
    public function open_read()
    {
        $this->r_pipe = fopen($this->fifoPath, 'r');
        if ($this->r_pipe == null) {
            error("open pipe {$this->fifoPath} for read error.");
            return false;
        }
        return true;
    }

    public function read($byte = 1024)
    {
        return fread($this->r_pipe, $byte);
    }

    public function read_all()
    {
        $r_pipe = fopen($this->fifoPath, 'r');
        $data   = '';
        while (!feof($r_pipe)) {
            //echo "read one K\n";
            $data .= fread($r_pipe, 1024);
        }
        fclose($r_pipe);
        return $data;
    }

    public function close_read()
    {
        return fclose($this->r_pipe);
    }
////////////////////////////////////////////////////
    /**
     * 删除管道
     *
     * @return boolean is success
     */
    public function rm_pipe()
    {
        return unlink($this->fifoPath);
    }
}
