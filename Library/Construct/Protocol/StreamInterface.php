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
namespace Library\Construct\Protocol;

/**
 * 自定义协议接口
 *stream_register_wrapper() 允许用户实现自定义的协议处理器和流，用于所有其它的文件系统函数中（例如 fopen()，fread() 等）。
 */
interface StreamInterface
{

    // /* 属性 */

    // public resource $context ;

    // /* 方法 */

    // __construct ( void )

    // __destruct ( void )

    // public bool dir_closedir ( void )
    public function dir_closedir();

    // public bool dir_opendir ( string $path , int $options )
    public function dir_opendir($path, $options);

    // public string dir_readdir ( void )
    public function dir_readdir();

    // public bool dir_rewinddir ( void )
    public function dir_rewinddir();

    // public bool mkdir ( string $path , int $mode , int $options )
    public function mkdir($path, $mode, $options);

    // public bool rename ( string $path_from , string $path_to )
    public function rename($path_from, $path_to);

    // public bool rmdir ( string $path , int $options )
    public function rmdir($path, $options);

    // public resource stream_cast ( int $cast_as )
    public function stream_cast($cast_as);

    // public void stream_close ( void )
    public function stream_close();

    // public bool stream_eof ( void )
    public function stream_eof();

    // public bool stream_flush ( void )
    public function stream_flush();

    // public bool stream_lock ( int $operation )
    public function stream_lock($operation);

    // public bool stream_metadata ( string $path , int $option , mixed $value )
    public function stream_metadata($path, $option, $value);

    // public bool stream_open ( string $path , string $mode , int $options , string &$opened_path )
    public function stream_open($path, $mode, $options, &$opened_path);

    // public string stream_read ( int $count )
    public function stream_read($count);

    // public bool stream_seek ( int $offset , int $whence  = SEEK_SET )
    public function stream_seek($offset, $whence = SEEK_SET);

    // public bool stream_set_option ( int $option , int $arg1 , int $arg2 )
    public function stream_set_option($option, $arg1, $arg2);

    // public array stream_stat ( void )
    public function stream_stat();

    // public int stream_tell ( void )
    public function stream_tell();

    // public bool stream_truncate ( int $new_size )
    public function stream_truncate($new_size);

    // public int stream_write ( string $data )
    public function stream_write($data);

    // public bool unlink ( string $path )
    public function unlink($path);

    // public array url_stat ( string $path , int $flags )
    public function url_stat($path, $flags);
}
