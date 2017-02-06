<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心控制器
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Controller;

class Controller
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
        //变量输出到模版
        if (!empty($this->tplVar)) {
            extract($this->tplVar, EXTR_OVERWRITE);
        }
        // 解析并获取模板内容

        if (!$content) {
            $content = $this->fetch($templateFile);
        } else {
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
        //模版路径
        $path = [];
        if (empty($templateFile)) {
            $path = [MODULE_NAME, CONTROLLER_NAME, ACTION_NAME];
        } elseif (count(explode('/', $templateFile)) >= 1) {
            if (count($tplDirArr) == 1) {
                $path = [MODULE_NAME, CONTROLLER_NAME, $templateFile[0]];
            }
            if (count($tplDirArr) == 2) {
                $path = [MODULE_NAME, $templateFile[0], $templateFile[1]];
            }
            if (count($tplDirArr) >= 3) {
                $path = [$templateFile[0], $templateFile[1], $templateFile[2]];
            }
        }
        $fileDir = APP_PATH . $path[0] . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . C('DEFAULT_THEME') . DIRECTORY_SEPARATOR . $path[1] . DIRECTORY_SEPARATOR . $path[2] . C('TMPL_TEMPLATE_SUFFIX');
        // 模板文件不存在直接返回
        if (!is_file($fileDir)) {
            E(L('_TEMPLATE_NOT_EXIST_') . ':' . $fileDir);
        }
        //加载
        $tpl = include $fileDir;
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
     *
     */
    public function test()
    {
        echo 'this is test';
    }
}
