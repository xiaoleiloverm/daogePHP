<?php
/**
 * daogePHP - A PHP Framework For Web
 *
 * @author   leilu<xiaoleiloverm@gmail.com>
 */

//----------------------------------
// 初始化类文件
//----------------------------------
namespace library;

class init
{

    /**
     *应用程序初始化
     */
    final public static function start()
    {
        // 注册AUTOLOAD方法
        spl_autoload_register('\library\init::autoload');
        //异常处理

    }

    /**
     * 类库自动加载
     * @param string $class 对象类名
     * @return void
     */
    final public static function autoload($class)
    {
        if (strpos($class, '\\') !== false) {
            $name = strstr($class, '\\', true);
            if (!$name) {
                //命名空间
            }
        }
        if (file_exists($class)) {
            require $class;
        }
    }
}
