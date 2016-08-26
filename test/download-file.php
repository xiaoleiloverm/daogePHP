<?php
/*
 * set here a limit of downloading rate (e.g. 200.20 Kb/s)
 */
function download_file()
{
    set_time_limit(0); //当PHP是执行在安全模式时，set_time_limit( )将不会有结果
    //ini_set('max_execution_time', 0);//php.ini 快捷修改方式
    $download_rate = 200.20;
    $download_file = 'mysql-workbench-community-6.3.5-winx64-noinstall.zip';
    $target_file   = 'target-file.zip';
    if (file_exists($download_file)) {
        /* headers */
        header('Last-Modified:' . gmdate('D, d M Y H:i:s') . 'GMT');
        header('Cache-control: private');
        header('Content-Type: application/octet-stream'); //网络文件的类型和网页的编码:二进制流，不知道下载文件类型
        header('Content-Length: ' . filesize($download_file));
        header('Content-Disposition: filename=' . $target_file);
        /* flush content */
        flush();
        /* open file */
        $fh = @fopen($download_file, 'r');
        while (!feof($fh)) {
            /* send only current part of the file to browser */
            print fread($fh, round($download_rate * 1024)); //200.2kb/s
            /* flush the content to the browser */
            flush();
            /* sleep for 1 sec */
            sleep(1);
        }
        /* close file */
        @fclose($fh);
        exit;
    } else {
        die('Fatal error: the ' . $download_file . ' file does not exist!');
    }

}

function download_file_2()
{
    $file = TEMP_DIR . '/' . $filename;
    $cmd  = 'php ' . ROOT_PATH . '/cron/xxx.php';
    if (!file_exists($file)) {
        //判断是否已经在后台执行
        $count = exec('/bin/ps xaww | grep -v grep | grep "' . $cmd . '" |wc -l');
        if (intval($count) == 0) {
            //先检查有无数据

            if ('') {
                //退出操作
            }
            //异步执行
            exec("{$cmd} {arguments}> /dev/null &");
        }
        echo '请10分钟后再刷新此页面';
        exit;
    } else {
        //输出文件
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        unlink($file);
        exit;
    }
    //ps:脚本中可以用$argv接收参数
}

//------------windows/linux/x86...系统下通用解决下载超时问题-------------------//
//download_file();

function largeFile()
{
    $sourceFile     = "mysql-workbench-community-6.3.5-winx64-noinstall.zip"; //要下载的临时文件名
    $outFile        = __FUNCTION__ . ".zip"; //下载保存到客户端的文件名
    $file_extension = strtolower(substr(strrchr($sourceFile, "."), 1)); //获取文件扩展名
    //echo $sourceFile;
    if (!ereg("[tmp|txt|rar|pdf|doc|zip]", $file_extension)) {
        exit("非法资源下载");
    }

    //检测文件是否存在
    if (!is_file($sourceFile)) {
        die("<b>404 File not found!</b>");
    }
    $len               = filesize($sourceFile); //获取文件大小
    $filename          = basename($sourceFile); //获取文件名字
    $outFile_extension = strtolower(substr(strrchr($outFile, "."), 1)); //获取文件扩展名
    //根据扩展名 指出输出浏览器格式
    switch ($outFile_extension) {
        case "exe":
            $ctype = "application/octet-stream";
            break;
        case "zip":
            $ctype = "application/zip";
            break;
        case "mp3":
            $ctype = "audio/mpeg";
            break;
        case "mpg":
            $ctype = "video/mpeg";
            break;
        case "avi":
            $ctype = "video/x-msvideo";
            break;
        default:
            $ctype = "application/force-download"; //强制下载的MIME类型 防止直接浏览器打开
    }
    //Begin writing headers
    header("Cache-Control:");
    header("Cache-Control: public");
    //设置输出浏览器格式
    header("Content-Type: $ctype");
    header("Content-Disposition: attachment; filename=" . $outFile);
    header("Accept-Ranges: bytes"); //WEB服务器表明自己是否接受获取其某个实体的一部分（比如文件的一部分）的请求。bytes：表示接受，none：表示不接受。
    $size = filesize($sourceFile);
    //如果有$_SERVER['HTTP_RANGE']参数
    if (isset($_SERVER['HTTP_RANGE'])) {
        /*Range头域 Range头域可以请求实体的一个或者多个子范围。
        例如，
        表示头500个字节：bytes=0-499
        表示第二个500字节：bytes=500-999
        表示最后500个字节：bytes=-500
        表示500字节以后的范围：bytes=500-
        第一个和最后一个字节：bytes=0-0,-1
        同时指定几个范围：bytes=500-600,601-999
        但是服务器可以忽略此请求头，如果无条件GET包含Range请求头，响应会以状态码206（PartialContent）返回而不是以200 （OK）。
         */
        // 断点后再次连接 $_SERVER['HTTP_RANGE'] 的值 bytes=4390912-
        list($a, $range) = explode("=", $_SERVER['HTTP_RANGE']);
        //if yes, download missing part
        str_replace($range, "-", $range); //这句干什么的呢。。。。
        $size2      = $size - 1; //文件总字节数
        $new_length = $size2 - $range; //获取下次下载的长度
        header("HTTP/1.1 206 Partial Content");
        header("Content-Length: $new_length"); //输入总长
        header("Content-Range: bytes $range$size2/$size"); //Content-Range: bytes 4908618-4988927/4988928 95%的时候
    } else {
        //第一次连接
        $size2 = $size - 1;
        header("Content-Range: bytes 0-$size2/$size"); //Content-Range: bytes 0-4988927/4988928
        header("Content-Length: " . $size); //输出总长
    }
    //打开文件
    $fp = fopen("$sourceFile", "rb");
    //设置指针位置
    fseek($fp, $range);
    //虚幻输出
    while (!feof($fp)) {
        //设置文件最长执行时间
        set_time_limit(0);
        print(fread($fp, 1024 * 8)); //输出文件
        flush(); //输出缓冲
        ob_flush();
    }
    fclose($fp);
    exit();
}

//largeFile();
require 'FileDownload.class.php';
$file = 'mysql-workbench-community-6.3.5-winx64-noinstall.zip';
$name = time() . '.zip';
$obj  = new FileDownload();
//下载速度
$obj->setSpeed(200.2);
//$flag = $obj->download($file, $name);
$flag = $obj->download($file, $name, true); // 断点续传

if (!$flag) {
    echo 'file not exists';
}
