<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心,协议-自定义的协议处理器和流 接口类
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Construct\Protocol\Stream;

/**
 * 自定义协议接口
 *stream_register_wrapper() 允许用户实现自定义的协议处理器和流，用于所有其它的文件系统函数中（例如 fopen()，fread() 等）。
 */
interface Stream
{

    // /* 属性 */

    // public resource $context ;

    // /* 方法 */

    // __construct ( void )

    // __destruct ( void )

    // public bool dir_closedir ( void )

    // public bool dir_opendir ( string $path , int $options )

    // public string dir_readdir ( void )

    // public bool dir_rewinddir ( void )

    // public bool mkdir ( string $path , int $mode , int $options )

    // public bool rename ( string $path_from , string $path_to )

    // public bool rmdir ( string $path , int $options )

    // public resource stream_cast ( int $cast_as )

    // public void stream_close ( void )

    // public bool stream_eof ( void )

    // public bool stream_flush ( void )

    // public bool stream_lock ( int $operation )

    // public bool stream_metadata ( string $path , int $option , mixed $value )

    // public bool stream_open ( string $path , string $mode , int $options , string &$opened_path )

    // public string stream_read ( int $count )

    // public bool stream_seek ( int $offset , int $whence  = SEEK_SET )

    // public bool stream_set_option ( int $option , int $arg1 , int $arg2 )

    // public array stream_stat ( void )

    // public int stream_tell ( void )

    // public bool stream_truncate ( int $new_size )

    // public int stream_write ( string $data )

    // public bool unlink ( string $path )

    // public array url_stat ( string $path , int $flags )

    public function stream_open($path, $mode, $options, &$opened_path);

    public function stream_read($count);

    public function stream_eof();

    public function stream_stat();
}
