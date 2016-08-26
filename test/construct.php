<?php
class Log
{
    protected $instance;

    //构造函数 构造类实例 instance
    public function __construct(Wite $instance)
    {
        $this->instance = $instance;
    }

    public function test()
    {
        $this->instance->alert('hollow');
    }
}

class Wite
{
    public function alert($msg)
    {
        echo '输出:' . $msg;
    }

}

$log = new Log(new Wite());
$log->test();
