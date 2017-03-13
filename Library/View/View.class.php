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
        include "var://$tpl";
        //var_dump($tpl);
        return $tpl;
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
