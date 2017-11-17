<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心视图
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\View;

class View
{
    /**
     * 模板输出变量
     * @var tplVar
     * @access protected
     */
    protected $tplVar = [];

    /**
     *加载视图模版文件
     * @access public
     * @param string $templateFile 模板文件名
     * @param string $charset 模板输出字符集
     * @param string $contentType 输出类型
     * @param string $content 模板输出内容
     * @return mixed
     */
    public function display($templateFile = '', $charset = '', $contentType = '', $content = '')
    {
        // 解析并获取模板内容
        if (!$content) {
            //自动输出
            $content = $this->fetch($templateFile);
        } else {
            $content = $this->fetch($templateFile) . $content;
            // 输出模板内容
            $this->render($content, $charset, $contentType);
        }
    }

    /**
     * 输出内容文本可以包括Html
     * @access private
     * @param string $content 输出内容
     * @param string $charset 模板输出字符集
     * @param string $contentType 输出类型
     * @return mixed
     */
    private function render($content, $charset = '', $contentType = '')
    {
        if (empty($charset)) {
            $charset = C('DEFAULT_CHARSET') ?: 'utf-8';
        }

        if (empty($contentType)) {
            $contentType = C('TMPL_CONTENT_TYPE') ?: 'text/html';
        }

        // 网页字符编码
        header('Content-Type:' . $contentType . '; charset=' . $charset);
        header('Cache-control: ' . C('HTTP_CACHE_CONTROL')); // 页面缓存控制
        header('X-Powered-By:daogePHP');
        // 输出模板文件
        echo $content;
    }

    /**
     * 解析和获取模板内容 用于输出
     * @access public
     * @param string $templateFile 模板文件名
     * @return string
     */
    public function fetch($templateFile = '')
    {
        $fileDir = T($templateFile);
        // 模板文件不存在直接返回
        if (!is_file($fileDir)) {
            E(L('_TEMPLATE_NOT_EXIST_') . ':' . $fileDir);
        }
        //变量输出到模版
        if (!empty($this->tplVar)) {
            extract($this->tplVar, EXTR_OVERWRITE);
        }
        // 视图解析标签 TODO
        $params = array('var' => $this->tplVar, 'file' => $fileDir);
        // Hook::listen('view_parse',$params);
        $view_parse = new \Library\View\Template\Template();
        $tpl        = $view_parse->fetch($params['file'], $params['var']);
        //加载
        //$tpl = include $fileDir;
        // //解析php:方法1 TODO
        // $tpl = eval($tpl);
        // 解析php:方法2 TODO
        //注册自定义流,别名 stream_wrapper_register
        stream_register_wrapper("var", "\\Library\\Protocol\\Stream");
        //错误位置提示TODO
        // try {
        //     //file_get_contents('file_path or url');
        //     readfile("var://$tpl");
        //     var_dump($tpl);
        // } catch (Exception $e) {
        //     var_dump('错误');
        //     var_dump($e);exit;
        // }
        //$res  = file("var://$tpl");
        // $test = readfile("php://filter/resource=var://$tpl");
        // echo $test;
        //include "var://$tpl"; //能正常解析 但无法方便定位错误位置

        //错误检测
        /*$check_code = "return true; ?>";
        $tpl        = $check_code . $tpl . "<?php ";*/
        //\Library\Core::setErrorHandler('\Library\Core::customError');
        //restore_error_handler();
        // 设置自定义的错误处理：函数会捕获用户自定义的和非致命类错误
        //set_error_handler('\Library\View\View::fatalError'); //过程中用户自定义错误trigger_error触发
        //test

        $str = '<?php if (!defined(DAOGE_PATH)) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
    <title><?php echo ($_headtitle); ?></title>
 ';
        register_shutdown_function('\Library\View\View::fatalError', $str);
        $check_code = "return true; ?>";
        $str        = $check_code . $str . "<?php ";
        @eval($str);
        $str = '<div class="grzx_head" id="large-header">
        ';
        register_shutdown_function('\Library\View\View::fatalError', $str);
        $check_code = "return true; ?>";
        $str        = $check_code . $str . "<?php ";
        @eval($str);

        //<div class="grzx_head" id="large-header">
        $res = file("var://$tpl");
        foreach ($res as $key => &$value) {
            $value      = $lineStr      = str_replace(PHP_EOL, '', $value);
            $check_code = "return true; ?>";
            $lineStr    = $check_code . $lineStr . "<?php ";
            var_dump($lineStr);
            //定义PHP程序执行完成后发生的错误
            register_shutdown_function('\Library\View\View::fatalError', $value);

            if (!@eval($lineStr)) {
                $error_message = "file: " . realpath($file_name) . " have syntax error";
                //var_dump($error_message);
                return false;
            }
        }
        //var_dump($res, "var://$tpl", $tpl, $test);
        //$this->include_text($tpl); //解析html中的php endif的问题TODO
        return $tpl;
        /*if (!function_exists('php_check_syntax')) {
    function php_check_syntax($file_name, &$error_message = null)
    {
    $file_content = file_get_contents($file_name);

    $check_code   = "return true; ?>";
    $file_content = $check_code . $file_content . "<?php ";

    if (!@eval($file_content)) {
    $error_message = "file: " . realpath($file_name) . " have syntax error";
    return false;
    }

    return true;
    }
    }

    if (!php_check_syntax("file.php", $msg)) {
    echo $msg;
    } else {
    echo "Woohoo, OK!";
    }
     */
    }

    //致命错误捕获
    public function fatalError($parm)
    {
        $trace = debug_backtrace();
        if ($e = error_get_last()) {
            var_dump($e, $trace);
            //$e example
            // array (size=4)
            //   'type' => int 4
            //   'message' => string 'syntax error, unexpected '}', expecting ',' or ';'' (length=50)
            //   'file' => string 'E:\mysite\hosbook\daogePHP\Library\View\View.class.php(123) : eval()'d code' (length=75)
            //   'line' => int 1
            switch ($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    ob_end_clean();
                    //self::halt($e);
                    echo "模版语法有误;错误：{$e['message']};错误位置：";
                    var_dump($parm);
                    break;
            }
        }
    }

    //执行一段代码
    public function include_text($text)
    {
        while (substr_count($text, '<?php') > 0) {
            //loop while there's code in $text
            list($html, $text) = explode('<?php', $text, 2); //split at first open php tag
            echo $html; //echo text before tag
            list($code, $text) = explode('?>', $text, 2); //split at closing tag
            try {
                eval($code); //exec code (between tags)
                //echo $code;
            } catch (Exception $e) {
                var_dump($e);
            }

        }
        echo $text; //echo whatever is left
    }

    /**
     * 模板变量赋值
     * @access public
     * @param mixed $name
     * @param mixed $value
     */
    public function assign($name, $value = '')
    {
        if (is_array($name)) {
            $this->tplVar = array_merge($this->tplVar, $name);
        } else {
            $this->tplVar[$name] = $value;
        }
    }

    /**
     * 取得模板变量的值
     * @access public
     * @param string $name
     * @return mixed
     */
    public function get($name = '')
    {
        if ($name === '') {
            return $this->tplVar;
        }
        return isset($this->tplVar[$name]) ? $this->tplVar[$name] : false;
    }

}
