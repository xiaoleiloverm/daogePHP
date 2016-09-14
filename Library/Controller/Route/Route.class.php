<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心控制器-路由类 -> 路由URL调度
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Controller\Route;

class Route
{
    private static $url_mode;
    private static $var_controller;
    private static $var_action;
    private static $var_module;

    /**
     * 初始化方法
     * @param type $config
     */
    public static function init($config)
    {
        self::$url_mode       = $config['URL_MODE'];
        self::$var_controller = $config['VAR_CONTROLLER'];
        self::$var_action     = $config['VAR_ACTION'];
        self::$var_module     = $config['VAR_MODULE'];
    }

    /**
     * 获取url打包参数
     * @return type
     */
    public static function makeUrl()
    {
        if (IS_CLI) {
            // CLI模式下 index.php module/controller/action/params/...(或者 ?m=module&c=controller&a=action&...)
            $_SERVER['PATH_INFO'] = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
            if (($url_parm = isset($_SERVER['argv'][2])) === true) {
                //module/controller/action/params/...
                if ($query_string = strstr('?', $url_parm) === false) {
                    $query_arr = empty($query_string) ? [] : explode('/', trim($query_string));
                    return self::getParamByPathinfo($query_arr);
                }
                //?m=module&c=controller&a=action&...
                else {
                    $query_arr = empty($query_string) ? [] : explode('&', $query_string);
                    return self::getParamByDynamic($query_arr);
                }
            }
        } else {
            switch (self::$url_mode) {
                //动态url传参 模式
                case 0:
                    return self::getParamByDynamic();
                    break;
                //pathinfo 模式
                case 1:
                    return self::getParamByPathinfo();
                    break;
            }
        }
    }

    /**
     * 获取参数通过url传参模式
     */
    private static function getParamByDynamic(array $arr = [])
    {
        empty($arr) && $arr = empty($_SERVER['QUERY_STRING']) ? array() : explode('&', strip_tags(trim($_SERVER['QUERY_STRING'])));
        $data               = array(
            'module'     => '',
            'controller' => '',
            'action'     => '',
            'param'      => array(),
        );
        if (is_array($arr)) {
            $tmp  = array();
            $part = array();
            foreach ($arr as $v) {
                $tmp           = explode('=', $v);
                $tmp[1]        = isset($tmp[1]) ? trim($tmp[1]) : '';
                $part[$tmp[0]] = $tmp[1];
            }
            if (isset($part[self::$var_module])) {
                $data['module'] = $part[self::$var_module];
                unset($part[self::$var_module]);
            }
            if (isset($part[self::$var_controller])) {
                $data['controller'] = $part[self::$var_controller];
                unset($part[self::$var_controller]);
            }
            if (isset($part[self::$var_action])) {
                $data['action'] = $part[self::$var_action];
                unset($part[self::$var_action]);
            }
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    unset($_GET[self::$var_controller], $_GET[self::$var_action], $_GET[self::$var_module]);
                    $data['param'] = array_merge($part, $_GET);
                    unset($_GET);
                    break;
                case 'POST':
                    unset($_POST[self::$var_controller], $_POST[self::$var_action], $_GET[self::$var_module]);
                    $data['param'] = array_merge($part, $_POST);
                    unset($_POST);
                    break;
                case 'HEAD':
                    break;
                case 'PUT':
                    break;
            }
        }
        return $data;
    }

    /**
     * 获取参数通过pathinfo模式
     */
    private static function getParamByPathinfo(array $part = [])
    {
        empty($part) && $part = $part = explode('/', trim(strip_tags($_SERVER['REQUEST_URI']), '/'));

        $data = array(
            'module'     => '',
            'controller' => '',
            'action'     => '',
            'param'      => array(),
        );
        if (!empty($part)) {
            krsort($part);
            //子域名映射不走module位路由(www主机方式除外)
            if (!C('SUB_DOMAIN_MAP_DEPLOY') || strpos(strtolower($_SERVER['HTTP_HOST']), 'www') !== false || end($part) == C('DEFAULT_MODULE')) {
                $data['module'] = array_pop($part);
            }
            $data['controller'] = array_pop($part);
            $data['action']     = array_pop($part);
            ksort($part);
            $part = array_values($part);
            $tmp  = array();
            if (count($part) > 0) {
                foreach ($part as $k => $v) {
                    if ($k % 2 == 0) {
                        $tmp[$v] = isset($part[$k + 1]) ? $part[$k + 1] : '';
                    }
                }
            }
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    unset($_GET[self::$var_controller], $_GET[self::$var_action]);
                    $data['param'] = array_merge($tmp, $_GET);
                    unset($_GET);
                    break;
                case 'POST':
                    unset($_POST[self::$var_controller], $_POST[self::$var_action]);
                    $data['param'] = array_merge($tmp, $_POST);
                    unset($_POST);
                    break;
                case 'HEAD':
                    break;
                case 'PUT':
                    break;
            }
        }
        return $data;
    }
}
