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
        self::$url_mode       = $config['URL_MODEL'];
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
                    $query_arr = empty($query_string) ? [] : explode('/', ucwords(trim($query_string)));
                    return self::getParamByPathinfo($query_arr);
                }
                //?m=module&c=controller&a=action&...
                else {
                    $query_arr = empty($query_string) ? [] : explode('&', ucwords($query_string));
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
        //$_SERVER['REDIRECT_URL'] 部分环境下会出现 http://xxx 地址
        empty($part) && $part = isset($_SERVER['REQUEST_URI']) ? explode('/', trim(strip_tags($_SERVER['REQUEST_URI']), '/')) : [];
        $part                 = preg_replace('/(\.htm|\.html|\.php|\.jsp|\.aspx?|\.action|\.daoge|\?.*)/', '', $part);
        $data                 = array(
            'module'     => '',
            'controller' => '',
            'action'     => '',
            'param'      => array(),
        );
        if (is_array($part)) {
            //PATHINFO URL分割符
            $urlDepr = C('URL_PATHINFO_DEPR') ?: '/';
            $partStr = $urlDepr . trim(implode($urlDepr, $part), $urlDepr) . $urlDepr; //URL字符串
            krsort($part); //倒置
            if (!empty(C('DOMAIN_URL_MAP'))) {
                foreach (C('DOMAIN_URL_MAP') as $key => $value) {
                    $key   = $urlDepr . trim(strtolower($key), $urlDepr) . $urlDepr; //映射字符串
                    $value = explode($urlDepr, $value);
                    //网站前端
                    if ($key === $urlDepr . $urlDepr && $partStr === $urlDepr . $urlDepr) {
                        $data['module']     = $value[0] ?: (C('DEFAULT_MODULE') ?: 'Home');
                        $data['controller'] = $value[1] ?: (C('DEFAULT_CONTROLLER') ?: 'Index');
                        $data['action']     = $value[2] ?: (C('DEFAULT_ACTION') ?: 'index');
                    }
                    //自定义路由匹配替换必须是域名后面第一个位置
                    //如定义了login=>home/public/login/映射 www.domain.com/login/p/...则是符合自定义路由 如www.domain.com/home/public/login则不会被替换
                    else if (strpos($partStr, $key) === 0) {
                        $partStr            = str_replace($key, '', $partStr);
                        $data['module']     = $value[0] ?: (C('DEFAULT_MODULE') ?: 'Home');
                        $data['controller'] = $value[1] ?: (C('DEFAULT_CONTROLLER') ?: 'Index');
                        $data['action']     = $value[2] ?: (C('DEFAULT_ACTION') ?: 'index');
                        $part               = explode($urlDepr, trim($partStr, $urlDepr)); //路由替换
                        krsort($part); //倒置
                    } else {

                    }
                }
            }
            //子域名映射开关
            if (empty(C('SUB_DOMAIN_MAP_DEPLOY'))) {
                $data['module'] = array_pop($part);
            }
            //子域名映射到模块
            else {
                $SUB_DOMAIN_MAP = C('SUB_DOMAIN_MAP');
                foreach ($SUB_DOMAIN_MAP as $key => $value) {
                    if (strpos($_SERVER['HTTP_HOST'], strtolower($key) . '.') !== false) {
                        $data['module'] = !empty($value) ? $value : C('DEFAULT_MODULE');
                        if (end($part) == $value) {
                            array_pop($part);
                        }
                    }
                }
            }
            $data['module']     = $data['module'] ?: array_pop($part) ?: (C('DEFAULT_MODULE') ?: 'Home');
            $data['controller'] = $data['controller'] ?: array_pop($part);
            $data['action']     = $data['action'] ?: (array_pop($part) ?: (C('DEFAULT_ACTION') ?: 'index'));
            if ($suffix = C('URL_HTML_SUFFIX')) {
                $suffix         = preg_replace('/\W/', '', $suffix);
                $data['action'] = preg_replace("/\.{$suffix}/", '', $data['action']); //替换url后缀
            }
            $data['action'] = preg_replace('/(\.htm|\.html|\.php|\.jsp|\.aspx?|\.action|\.daoge|\?.*)/', '', $data['action']);
            ksort($part); //恢复顺序
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
