<?php
/**
 * daogePHP - A PHP Framework For Web
 *
 * @author   leilu<xiaoleiloverm@gmail.com>
 */

//----------------------------------
// 核心函数库
//----------------------------------

/**
 * 类库实例化函数
 *
 */
function A()
{

}

/**
 * 行为函数
 *
 */
function B()
{

}

/**
 * 配置函数,获取配置/(批量)配置
 *
 * @param  array|string  $key  配置键名
 * @param  mixed  $value 配置值
 * @param  mixed  $default 默认值,用于读取
 * @return mixed
 */
function C($key = null, $value = null, $default = null)
{
    static $_config = [];
    //获取所有
    if ($key == '') {
        return $_config;
    }
    //批量设置
    else if (is_array($key)) {
        //多维数组递归替换键为大写
        $array_change_key_case_recursive = function ($key) use (&$array_change_key_case_recursive) {
            return array_map(function ($item) use (&$array_change_key_case_recursive) {
                if (is_array($item)) {
                    $item = $array_change_key_case_recursive($item);
                }
                return $item;
            }, array_change_key_case($key, CASE_UPPER));
        };
        $key     = $array_change_key_case_recursive($key);
        $_config = array_merge($_config, $key);
        return null;
    }
    //赋值
    else if (is_string($key)) {
        if (!strrpos('.', $key)) {
            $key = strtoupper($key);
            //读取
            if ($value === null) {
                return isset($_config[$key]) ? $_config[$key] : $default;
            }
            $_config[$key] = $value;
        } else {
            $key    = explode('.', $key);
            $key[0] = strtoupper($key[0]);
            //读取
            if ($value === null) {
                return isset($_config[$key[0]][$key[1]]) ? $_config[$key[0]][$key[1]] : $default;
            }
            $_config[$key[0]][$key[1]] = $value;
        }
    }
    return null;
}

/**
 * 数据库模型实例化函数
 *
 */
function D()
{

}

/**
 * 实例化一个没有模型文件的Model
 * @param string $name Model名称 支持指定基础模型 例如 MongoModel:User
 * @param string $tablePrefix 表前缀
 * @param mixed $pdo 数据库连接信息
 * @param mixed $connection 数据库连接信息
 * @return Think\Model
 */
function M($name = '', $tablePrefix = '', $pdo = null, $driver = 'mysql')
{
    static $_model = null;
    //数据库
    $dns = C('DB_TYPE') ?: 'mysql';
    //主机
    $dns .= ':host=' . C('DB_HOST');
    //端口
    if (C('DB_PORT')) {
        $dns .= ';port=' . C('DB_PORT');
    }
    // use unix socket
    if (C('UNIX_SOCKET')) {
        $dns = 'mysql:unix_socket=' . C('UNIX_SOCKET');
    }
    //数据库名
    $dns .= ';dbname=' . C('DB_NAME');
    //编码
    $dns .= ';charset=' . C('DB_CHARSET');

    //创建连接实例
    $pdo = new \PDO($dns, C('DB_USER'), C('DB_PWD'));
    if (strpos($name, ':')) {
        list($class, $name) = explode(':', $name);
    } else {
        $class = '\\Library\\Model\\Model';
    }
    if (!isset($_model)) {
        $_model = new $class($name, $tablePrefix, $pdo, C('DB_TYPE'));
    }

    return $_model;
}

/**
 * 错误处理函数
 *
 * @param  array|string  $error  错误信息
 * @param  string  $handler 异常类型
 * @return void
 */
function E($error, $handler = 'ErrorException')
{
    $errorHandler = [
        '0'      => 'Exception',
        '01'     => 'ErrorException',
        '02'     => 'LogicException',
        '0201'   => 'BadFunctionCallException',
        '020101' => 'BadMethodCallException',
        '0202'   => 'DomainException',
        '0203'   => 'InvalidArgumentException',
        '0204'   => 'LengthException',
        '0205'   => 'OutOfRangeException',
        '03'     => 'RuntimeException',
        '0301'   => 'OutOfBoundsException',
        '0302'   => 'OverflowException',
        '0303'   => 'RangeException',
        '0304'   => 'UnderflowException',
        '0305'   => 'UnexpectedValueException',
    ];
    if (!in_array($handler, $errorHandler)) {
        $handler = $errorHandler['01'];
    }
    if (!is_array($error)) {
        //自定义输出参数
        $trace        = debug_backtrace();
        $e['message'] = $error;
        $e['type']    = 0;
        $e['file']    = $trace[0]['file'];
        $e['line']    = $trace[0]['line'];
    } else {
        $e = $error;
    }
    if ($handler == 'ErrorException') {
        throw new \ErrorException($e['message'], 0, $e['type'], $e['file'], $e['line']); //抛出错误异常
    } else {
        throw new $handler($e['message'], $e['type']); //抛出异常
    }
}

/**
 * 获取设置语言函数
 *
 * @param  array|string  $key  键名
 * @param  mixed  $value 值
 * @return mixed
 */
/*
 * zh-cn 简体中文
 * zh-tw 繁体中文
 * da-dk 丹麦语
 * nl-nl 荷兰语
 * en-us 英语
 * fi-fi 芬兰语
 * fr-fr 法语
 * de-de 德语
 * it-it 意大利语
 * ja-jp 日语
 * ko-kr 朝鲜语
 * nb-no 挪威语
 * pt-br 葡萄牙语
 * es-es 西班牙语
 * es-us 西班牙语（美国）
 * sv-se 瑞典语
 * ......
 */
function L($key = null, $value = null)
{
    static $_lang = [];
    //获取所有
    if ($key == '') {
        return $_lang;
    }
    //批量设置
    else if (is_array($key)) {
        //多维数组递归替换键为大写
        $array_change_key_case_recursive = function ($key) use (&$array_change_key_case_recursive) {
            return array_map(function ($item) use (&$array_change_key_case_recursive) {
                if (is_array($item)) {
                    $item = $array_change_key_case_recursive($item);
                }
                return $item;
            }, array_change_key_case($key, CASE_UPPER));
        };
        $key   = $array_change_key_case_recursive($key);
        $_lang = array_merge_recursive($_lang, $key);
        return null;
    }
    //赋值
    else if (is_string($key)) {
        $key = strtoupper($key);
        //读取
        if ($value === null) {
            //替换变量
            //var_dump(preg_replace('/\{\$([\w]+)\}/', "$1", $_lang[$key]));
            return isset($_lang[$key]) ? $_lang[$key] : null;
        } else if (is_array($value)) {
            //替换变量
            $search = array_key($value);
            foreach ($search as &$v) {
                $v = '{$' . $v . '}';
            }
            //若不存在,直接返回全大写$key
            return str_replace($search, $value, isset($_lang[$key]) ? $_lang[$key] : $key);
        }
        $_lang[$key] = $value;
    }
    return null;
}

/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time = 0, $msg = '')
{
    $url = str_replace(["\r", "\n"], '', $url);
    if ($msg == '') {
        $msg = L('_SYS_REDIRECT_MSG_');
    }
    eval("\$msg = \"$msg\";"); //解析字符串变量
    if (!headers_sent()) {
        if ($time === 0) {
            header("Location:" . $url);
        } else {
            header("refresh:{$time};url={$url}");
            exit($msg);
        }
    } else {
        $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        ($time != 0) && $str .= $msg;
        exit($str);
    }
}

/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code)
{
    static $_status = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded',
    );
    if (isset($_status[$code])) {
        header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:' . $code . ' ' . $_status[$code]);
    }
}

/**
 * 判断是否SSL协议
 * @return boolean
 */
function is_ssl()
{
    if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
        return true;
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        return true;
    }
    return false;
}

/**
 * session管理函数
 * @param string|array $name session名称 如果为数组则表示进行session设置
 * @param mixed $value session值
 * @return mixed
 */
function session($name = '', $value = '')
{
    $prefix = C('SESSION_PREFIX');
    if (is_array($name)) {
        // session初始化 在session_start 之前调用
        if (isset($name['prefix'])) {
            C('SESSION_PREFIX', $name['prefix']);
        }

        if (C('VAR_SESSION_ID') && isset($_REQUEST[C('VAR_SESSION_ID')])) {
            session_id($_REQUEST[C('VAR_SESSION_ID')]);
        } elseif (isset($name['id'])) {
            session_id($name['id']);
        }
        if ('common' == APP_MODE) {
            // 其它模式可能不支持
            ini_set('session.auto_start', 0);
        }
        if (isset($name['name'])) {
            session_name($name['name']);
        }

        if (isset($name['path'])) {
            session_save_path($name['path']);
        }

        if (isset($name['domain'])) {
            ini_set('session.cookie_domain', $name['domain']);
        }

        if (isset($name['expire'])) {
            ini_set('session.gc_maxlifetime', $name['expire']);
            ini_set('session.cookie_lifetime', $name['expire']);
        }
        if (isset($name['use_trans_sid'])) {
            ini_set('session.use_trans_sid', $name['use_trans_sid'] ? 1 : 0);
        }

        if (isset($name['use_cookies'])) {
            ini_set('session.use_cookies', $name['use_cookies'] ? 1 : 0);
        }

        if (isset($name['cache_limiter'])) {
            session_cache_limiter($name['cache_limiter']);
        }

        if (isset($name['cache_expire'])) {
            session_cache_expire($name['cache_expire']);
        }

        if (isset($name['type'])) {
            C('SESSION_TYPE', $name['type']);
        }

        if (C('SESSION_TYPE')) {
            // 读取session驱动
            $type   = C('SESSION_TYPE');
            $class  = strpos($type, '\\') ? $type : 'Library\\Controller\\Session\\Driver\\' . ucwords(strtolower($type));
            $hander = new $class();
            session_set_save_handler(
                array(&$hander, "open"),
                array(&$hander, "close"),
                array(&$hander, "read"),
                array(&$hander, "write"),
                array(&$hander, "destroy"),
                array(&$hander, "gc"));
        }
        // 启动session
        if (C('SESSION_AUTO_START')) {
            session_start();
        }

    } elseif ('' === $value) {
        if ('' === $name) {
            // 获取全部的session
            return $prefix ? $_SESSION[$prefix] : $_SESSION;
        } elseif (0 === strpos($name, '[')) {
            // session 操作
            if ('[pause]' == $name) {
                // 暂停session
                session_write_close();
            } elseif ('[start]' == $name) {
                // 启动session
                session_start();
            } elseif ('[destroy]' == $name) {
                // 销毁session
                $_SESSION = array();
                session_unset();
                session_destroy();
            } elseif ('[regenerate]' == $name) {
                // 重新生成id
                session_regenerate_id();
            }
        } elseif (0 === strpos($name, '?')) {
            // 检查session
            $name = substr($name, 1);
            if (strpos($name, '.')) {
                // 支持数组
                list($name1, $name2) = explode('.', $name);
                return $prefix ? isset($_SESSION[$prefix][$name1][$name2]) : isset($_SESSION[$name1][$name2]);
            } else {
                return $prefix ? isset($_SESSION[$prefix][$name]) : isset($_SESSION[$name]);
            }
        } elseif (is_null($name)) {
            // 清空session
            if ($prefix) {
                unset($_SESSION[$prefix]);
            } else {
                $_SESSION = array();
            }
        } elseif ($prefix) {
            // 获取session
            if (strpos($name, '.')) {
                list($name1, $name2) = explode('.', $name);
                return isset($_SESSION[$prefix][$name1][$name2]) ? $_SESSION[$prefix][$name1][$name2] : null;
            } else {
                return isset($_SESSION[$prefix][$name]) ? $_SESSION[$prefix][$name] : null;
            }
        } else {
            if (strpos($name, '.')) {
                list($name1, $name2) = explode('.', $name);
                return isset($_SESSION[$name1][$name2]) ? $_SESSION[$name1][$name2] : null;
            } else {
                return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
            }
        }
    } elseif (is_null($value)) {
        // 删除session
        if (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            if ($prefix) {
                unset($_SESSION[$prefix][$name1][$name2]);
            } else {
                unset($_SESSION[$name1][$name2]);
            }
        } else {
            if ($prefix) {
                unset($_SESSION[$prefix][$name]);
            } else {
                unset($_SESSION[$name]);
            }
        }
    } else {
        // 设置session
        if (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            if ($prefix) {
                $_SESSION[$prefix][$name1][$name2] = $value;
            } else {
                $_SESSION[$name1][$name2] = $value;
            }
        } else {
            if ($prefix) {
                $_SESSION[$prefix][$name] = $value;
            } else {
                $_SESSION[$name] = $value;
            }
        }
    }
    return null;
}

/**
 * Cookie 设置、获取、删除
 * @param string $name cookie名称
 * @param mixed $value cookie值
 * @param mixed $option cookie参数
 * @return mixed
 */
function cookie($name = '', $value = '', $option = null)
{
    // 默认设置
    $config = array(
        'prefix'   => C('COOKIE_PREFIX'), // cookie 名称前缀
        'expire'   => C('COOKIE_EXPIRE'), // cookie 保存时间
        'path'     => C('COOKIE_PATH'), // cookie 保存路径
        'domain'   => C('COOKIE_DOMAIN'), // cookie 有效域名
        'secure'   => C('COOKIE_SECURE'), //  cookie 启用安全传输
        'httponly' => C('COOKIE_HTTPONLY'), // httponly设置
    );
    // 参数设置(会覆盖黙认设置)
    if (!is_null($option)) {
        if (is_numeric($option)) {
            $option = array('expire' => $option);
        } elseif (is_string($option)) {
            parse_str($option, $option);
        }

        $config = array_merge($config, array_change_key_case($option));
    }
    if (!empty($config['httponly'])) {
        ini_set("session.cookie_httponly", 1);
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE)) {
            return null;
        }

        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {
// 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return null;
    } elseif ('' === $name) {
        // 获取全部的cookie
        return $_COOKIE;
    }
    $name = $config['prefix'] . str_replace('.', '_', $name);
    if ('' === $value) {
        if (isset($_COOKIE[$name])) {
            $value = $_COOKIE[$name];
            if (0 === strpos($value, 'think:')) {
                $value = substr($value, 6);
                return array_map('urldecode', json_decode(MAGIC_QUOTES_GPC ? stripslashes($value) : $value, true));
            } else {
                return $value;
            }
        } else {
            return null;
        }
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
            unset($_COOKIE[$name]); // 删除指定cookie
        } else {
            // 设置cookie
            if (is_array($value)) {
                $value = 'think:' . json_encode(array_map('urlencode', $value));
            }
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
            $_COOKIE[$name] = $value;
        }
    }
    return null;
}
