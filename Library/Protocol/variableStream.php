<?php
//自定义协议
class VariableStream
{
    private $string;
    private $position;
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $url = parse_url($path);
        $id  = $url["host"];

//根据ID到数据库中取出php字符串代码
        $this->string   = mysql_get($id);
        $this->position = 0;
        return true;
    }
    public function stream_read($count)
    {
        $ret = substr($this->string, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }
    public function stream_eof()
    {}
    public function stream_stat()
    {}
}
