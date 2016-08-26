<?php
interface father
{
    public function shuchu();
}
interface mother
{
    function dayin($my = '');
}
interface fam extends father, mother
{
    public function cook($name);
    function dayin($my = '2');
}
class test implements father, mother
//class test implements fam

{
    function dayin($my = '3')
    {
        echo "我的名字是：" . $my;
        echo "<br>";
    }
    function shuchu()
    {
        echo "接口继承，要实现两个抽象方法";
        echo "<br>";
    }
    function cook($name)
    {
        echo "平时经常做饭的人是：" . $name;
    }
}
$t = new test();
$t->shuchu();
$t->dayin();
$t->cook("妈妈");
