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
function A($class)
{
    return new $class;
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
        if (!strrpos($key, '.')) {
            $key = strtoupper($key);
            //读取
            if ($value === null) {
                return isset($_config[$key]) ? $_config[$key] : $default;
            }
            $_config[$key] = $value;
        } else {
            $key    = explode('.', $key);
            $key[0] = strtoupper($key[0]);
            $key[1] = strtoupper($key[1]);
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
 * @param  string  $modelName  模型名
 * @param  string  $layer 模型层名
 * @return obj
 *
 */
function D($modelName, $layer)
{
    $layer        = $layer ?: C('DEFAULT_M_NAME');
    $defaultClass = "\\Library\\{$layer}\\Model";
    if (empty($modelName)) {
        return M(basename($modelName));
    }
    static $_model = [];
    //存在对象 直接返回
    if (isset($_model[$modelName . $layer])) {
        return $_model;
    }
    if (strpos($modelName, '\\') !== false) {
        $class = $modelName . $mode;
    } else {
        $class = '\\Library\\Common\\' . $modelName . $layer;
    }
    if (!class_exists($class)) {
        $class = "\\Library\\Model\\" . $modelName . $layer;
        if (!class_exists($class)) {
            $class = "\\Library\\Model\\Model";
            return M(basename($modelName));
        }
    }
    $_model[$modelName . $layer] = new $class(basename($modelName));
    return $_model[$modelName . $layer];
}

/**
 * 实例化一个没有模型文件的Model
 * @param string $name Model名称 支持指定基础模型 例如 MongoModel:User
 * @param string $tablePrefix 表前缀
 * @param mixed $pdo 数据库连接信息
 * @param mixed $connection 数据库连接信息
 * @param mixed $layer 模型层名
 * @return obj
 */
function M($name = '', $tablePrefix = '', $pdo = null, $driver = 'mysql', $layer)
{
    $layer         = $layer ?: C('DEFAULT_M_NAME'); //默认Model
    static $_model = array();
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
        $class = "\\Library\\Model\\Model";
    }
    if (!isset($_model[$name . '_' . $tablePrefix])) {
        $_model[$name . '_' . $tablePrefix] = new $class($name, $tablePrefix, $pdo, ucfirst(C('DB_TYPE')));
    }

    return $_model[$name . '_' . $tablePrefix];
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
 * http传值处理函数
 * 使用方法:
 * <code>
 * I('id',0); 获取id参数 自动判断get或者post
 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
 * I('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @param mixed $datas 要获取的额外数据源
 * @return mixed
 */
function I($name, $default = '', $filter = null, $datas = null)
{
    static $_PUT = null;
    if (strpos($name, '/')) {
        // 指定修饰符
        list($name, $type) = explode('/', $name, 2);
    } elseif (C('VAR_AUTO_STRING')) {
        // 默认强制转换为字符串
        $type = 's';
    }
    if (strpos($name, '.')) {
        // 指定参数来源
        list($method, $name) = explode('.', $name, 2);
    } else {
        // 默认为自动判断
        $method = 'param';
    }
    switch (strtolower($method)) {
        case 'get':
            //parse_str(file_get_contents("php://input"), $_GET);
            $input = &$_GET;
            //$input = array_merge($input, $GLOBALS['_urlParam']);
            break;
        case 'post':
            //parse_str(file_get_contents("php://input"), $_POST);
            $input = &$_POST;
            break;
        case 'put':
            if (is_null($_PUT)) {
                parse_str(file_get_contents('php://input'), $_PUT);
            }
            $input = $_PUT;
            break;
        //自动判断
        case 'param':
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    //$input = array_merge((array) $_GET, $GLOBALS['_urlParam']);
                    $input = $_GET;
                    break;
                case 'POST':
                    //当Content-Type仅在取值为application/x-www-data-urlencoded和multipart/form-data两种情况下，PHP才会将http请求数据包中相应的数据填入全局变量$_POST
                    //Content-Type:application/x-www-form-urlencoded; 情况下就得填充POST 否者$_POST会取不到值
                    parse_str(file_get_contents("php://input"), $_POST);
                    $input = $_POST;
                    break;
                case 'PUT':
                    if (is_null($_PUT)) {
                        parse_str(file_get_contents('php://input'), $_PUT);
                    }
                    $input = $_PUT;
                    break;
                default:
                    $input = $_GET;
            }
            break;
        case 'path':
            $input = array();
            if (!empty($_SERVER['PATH_INFO'])) {
                $depr  = C('URL_PATHINFO_DEPR');
                $input = explode($depr, trim($_SERVER['PATH_INFO'], $depr));
            }
            break;
        case 'request':
            $input = &$_REQUEST;
            break;
        case 'session':
            $input = &$_SESSION;
            break;
        case 'cookie':
            $input = &$_COOKIE;
            break;
        case 'server':
            $input = &$_SERVER;
            break;
        case 'globals':
            $input = &$GLOBALS;
            break;
        case 'data':
            $input = &$datas;
            break;
        default:
            return null;
    }
    if ('' == $name) {
        // 获取全部变量
        $data    = $input;
        $filters = isset($filter) ? $filter : C('DEFAULT_FILTER');
        if ($filters) {
            //逗号隔开 按顺序依次使用过滤函数
            if (is_string($filters)) {
                $filters = explode(',', $filters);
            }
            //依次函数过滤
            !empty($filters) && ksort($filters);
            foreach ($filters as $filter) {
                $data = array_map_recursive($filter, $data); // 参数过滤
            }
        }
    } elseif (isset($input[$name])) {
        // 取值操作
        $data    = $input[$name];
        $filters = isset($filter) ? $filter : C('DEFAULT_FILTER');
        if ($filters) {
            if (is_string($filters)) {
                if (0 === strpos($filters, '/')) {
                    if (1 !== preg_match($filters, (string) $data)) {
                        // 支持正则验证
                        return isset($default) ? $default : null;
                    }
                } else {
                    $filters = explode(',', $filters);
                }
            } elseif (is_int($filters)) {
                $filters = array($filters);
            }

            if (is_array($filters)) {
                foreach ($filters as $filter) {
                    if (function_exists($filter)) {
                        $data = is_array($data) ? array_map_recursive($filter, $data) : $filter($data); // 参数过滤
                    } else {
                        $data = filter_var($data, is_int($filter) ? $filter : filter_id($filter));
                        if (false === $data) {
                            return isset($default) ? $default : null;
                        }
                    }
                }
            }
        }
        if (!empty($type)) {
            switch (strtolower($type)) {
                case 'a': // 数组
                    $data = (array) $data;
                    break;
                case 'd': // 数字
                    $data = (int) $data;
                    break;
                case 'f': // 浮点
                    $data = (float) $data;
                    break;
                case 'b': // 布尔
                    $data = (boolean) $data;
                    break;
                case 's': // 字符串
                default:
                    $data = (string) $data;
            }
        }
    } else {
        // 变量默认值
        $data = isset($default) ? $default : null;
    }
    //批量采用默认的 过滤函数
    is_array($data) && array_walk_recursive($data, 'default_filter_func');
    return $data;
}

/**
 *参数过滤
 *
 * @param  string  $filter  过滤函数
 * @param  array  $data 数据
 * @return array 过滤的结果
 */
function array_map_recursive($filter, $data)
{
    $result = array();
    foreach ($data as $key => $val) {
        $result[$key] = is_array($val)
        ? array_map_recursive($filter, $val)
        : call_user_func($filter, $val);
    }
    return $result;
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
    //$url 传入//字符串会出现页面找不到问题
    if ($url == '//') {
        $url = '/';
    }
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
    //参数全为null 取全部session
    if (is_null($name) && is_null($value)) {
        // 启动session
        if (C('SESSION_AUTO_START')) {
            session_start();
        }
        return $_SESSION;
    }
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
        // 启动session
        if (C('SESSION_AUTO_START')) {
            session_start();
        }
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
        if (C('SESSION_AUTO_START')) {
            session_start();
        }
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

/**
 * 导入所需的类库 同java的Import 本函数有缓存功能
 * @param string $class 类库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
 * @return boolean
 */
function import($class, $baseUrl = '', $ext = EXT)
{
    static $_file = array();
    $class        = str_replace(array('.', '#'), array('/', '.'), $class);
    if (isset($_file[$class . $baseUrl])) {
        return true;
    } else {
        $_file[$class . $baseUrl] = true;
    }

    $class_strut = explode('/', $class);
    if (empty($baseUrl)) {
        if ('@' == $class_strut[0] || MODULE_NAME == $class_strut[0]) {
            //加载当前模块的类库
            $baseUrl = APP_PATH . MODULE_NAME . '/';
            $class   = substr_replace($class, '', 0, strlen($class_strut[0]) + 1);
        } elseif ('Common' == $class_strut[0]) {
            //加载公共模块的类库
            $baseUrl = APP_COMMON_PATH;
            $class   = substr($class, 7);
        } elseif (in_array($class_strut[0], array('Org', 'Vendor')) || is_dir(LIB_PATH . $class_strut[0])) {
            // 系统类库包和第三方类库包
            $baseUrl = LIB_PATH;
        } else {
            // 加载其他模块的类库
            $baseUrl = APP_PATH;
        }
    }
    if (substr($baseUrl, -1) != '/') {
        $baseUrl .= '/';
    }

    $classfile = $baseUrl . $class . $ext;
    if (!class_exists(basename($class), false)) {
        // 如果类不存在 则导入类库文件
        return require_cache($classfile);
    }
    return null;
}

/**
 * 优化的require_once
 * @param string $filename 文件地址
 * @return boolean
 */
function require_cache($filename)
{
    static $_importFiles = array();
    if (!isset($_importFiles[$filename])) {
        if (file_exists_case($filename)) {
            require $filename;
            $_importFiles[$filename] = true;
        } else {
            $_importFiles[$filename] = false;
        }
    }
    return $_importFiles[$filename];
}

/**
 * 区分大小写的文件存在判断
 * @param string $filename 文件地址
 * @return boolean
 */
function file_exists_case($filename)
{
    if (is_file($filename)) {
        if (IS_WIN && APP_DEBUG) {
            if (basename(realpath($filename)) != basename($filename)) {
                return false;
            }

        }
        return true;
    }
    return false;
}

/**
 * 去除代码中的空白和注释
 * @param string $content 代码内容
 * @return string
 */
function strip_whitespace($content)
{
    $stripStr = '';
    //分析php源码
    $tokens     = token_get_all($content);
    $last_space = false;
    for ($i = 0, $j = count($tokens); $i < $j; $i++) {
        if (is_string($tokens[$i])) {
            $last_space = false;
            $stripStr .= $tokens[$i];
        } else {
            switch ($tokens[$i][0]) {
                //过滤各种PHP注释
                case T_COMMENT:
                case T_DOC_COMMENT:
                    break;
                //过滤空格
                case T_WHITESPACE:
                    if (!$last_space) {
                        $stripStr .= ' ';
                        $last_space = true;
                    }
                    break;
                case T_START_HEREDOC:
                    $stripStr .= "<<<DAOGE\n";
                    break;
                case T_END_HEREDOC:
                    $stripStr .= "DAOGE;\n";
                    for ($k = $i + 1; $k < $j; $k++) {
                        if (is_string($tokens[$k]) && $tokens[$k] == ';') {
                            $i = $k;
                            break;
                        } else if ($tokens[$k][0] == T_CLOSE_TAG) {
                            break;
                        }
                    }
                    break;
                default:
                    $last_space = false;
                    $stripStr .= $tokens[$i][1];
            }
        }
    }
    return $stripStr;
}

/**
 * 获取模版文件全路径
 * @param string $templateFile 模版资源相对地址
 * @return string
 */
function T($templateFile)
{
    //模版路径
    $path = [];
    //$templateFile = str_replace(':', '/', $templateFile);
    if (empty($templateFile)) {
        $path = [MODULE_NAME, CONTROLLER_NAME, ACTION_NAME];
    } elseif (count($tplDirArr = explode('/', $templateFile)) >= 1) {
        if (count($tplDirArr) == 1) {
            $path = [MODULE_NAME, CONTROLLER_NAME, $tplDirArr[0]];
        }
        if (count($tplDirArr) == 2) {
            $path = [MODULE_NAME, $tplDirArr[0], $tplDirArr[1]];
        }
        if (count($tplDirArr) == 3) {
            $path = [$tplDirArr[0], $tplDirArr[1], $tplDirArr[2]];
        }
        //相对 或者绝对完整路径
        if (count($tplDirArr) >= 3) {
            return $templateFile;
        }
    }
    $fileDir = APP_PATH . $path[0] . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . (C('DEFAULT_THEME') ? C('DEFAULT_THEME') . DIRECTORY_SEPARATOR : '') . $path[1] . DIRECTORY_SEPARATOR . $path[2] . C('TMPL_TEMPLATE_SUFFIX');
    return $fileDir;
}

/**
 * URL生成 TODO
 * @param string $url URL表达式，格式：'[模块/控制器/操作#锚点@域名]?参数1=值1&参数2=值2...'
 * @param string|array $vars 传入的参数，支持数组和字符串
 * @param string|boolean $suffix 伪静态后缀，默认为true表示获取配置值
 * @param boolean $domain 是否显示域名
 * @return string
 */
function U($url = '', $vars = '', $suffix = true, $domain = false)
{
    // 解析URL
    $info = parse_url($url) ?: $url;
    //http://username:password@hostname/path?arg=value#anchor?arg2=value2
    // 'scheme' => string 'http' (length=4)
    // 'host' => string 'hostname' (length=8)
    // 'user' => string 'username' (length=8)
    // 'pass' => string 'password' (length=8)
    // 'path' => string '/path' (length=5)
    // 'query' => string 'arg2=value2' (length=11)
    // 'fragment' => string 'anchor?arg2=value2' (length=18)
    $url = !empty($info['path']) ? $info['path'] : ACTION_NAME;
    if (isset($info['fragment'])) {
        // 解析锚点
        $anchor = $info['fragment'];
        if (false !== strpos($anchor, '?')) {
            // 解析参数
            list($anchor, $info['query']) = explode('?', $anchor, 2);
        }
        if (false !== strpos($anchor, '@')) {
            // 解析域名
            list($anchor, $host) = explode('@', $anchor, 2);
        }
    } elseif (false !== strpos($url, '@')) {
        // 解析域名
        list($url, $host) = explode('@', $info['path'], 2);
    }
    // 解析子域名
    if (isset($host)) {
        $domain = $host . (strpos($host, '.') ? '' : strstr($_SERVER['HTTP_HOST'], '.'));
    }
    //显示域名
    elseif ($domain === true) {
        $domain = $_SERVER['HTTP_HOST'];
        // 开启子域名映射设置
        if (C('SUB_DOMAIN_MAP_DEPLOY')) {
            //域名初始化 二级域名
            $domain = $domain == 'localhost' ? 'localhost' : 'www' . strstr($_SERVER['HTTP_HOST'], '.');
            // URL映射TODO

            // '子域名'=>array('模块[/控制器]');
            foreach (C('SUB_DOMAIN_MAP') as $key => $rule) {
                $rule = is_array($rule) ? $rule[0] : $rule;
                if (false === strpos($key, '*') && 0 === strpos($url, $rule)) {
                    $domain = $key . strstr($domain, '.'); // 生成对应子域名
                    $url    = substr_replace($url, '', 0, strlen($rule));
                    break;
                }
            }
        }
    }

    // 解析参数
    if (is_string($vars)) {
        // aaa=1&bbb=2 转换成数组
        parse_str($vars, $vars);
    } elseif (!is_array($vars)) {
        $vars = array();
    }
    if (isset($info['query'])) {
        // 解析地址里面参数 合并到vars
        parse_str($info['query'], $params);
        $vars = array_merge($params, $vars);
    }

    // URL组装
    $depr    = C('URL_PATHINFO_DEPR') ?: '/'; //PATHINFO URL分割符
    $urlCase = C('URL_CASE_INSENSITIVE');
    $_m      = C('VAR_MODULE'); //模型
    $_c      = C('VAR_CONTROLLER'); //控制器
    $_a      = C('VAR_ACTION'); //操作
    if ($url) {
        $info['path'] = str_replace(':', $depr, $info['path']);
        //模块 控制 方法
        if (empty($info['path'])) {
            $path = [$_m => MODULE_NAME, $_c => CONTROLLER_NAME, $_a => ACTION_NAME];
        } elseif (count($tplDirArr = explode($depr, $info['path'])) >= 1) {
            if (count($tplDirArr) == 1) {
                $path = [$_m => MODULE_NAME, $_c => CONTROLLER_NAME, $_a => $tplDirArr[0]];
            }
            if (count($tplDirArr) == 2) {
                $path = [$_m => MODULE_NAME, $_c => $tplDirArr[0], $_a => $tplDirArr[1]];
            }
            if (count($tplDirArr) >= 3) {
                $path = [$_m => $tplDirArr[0], $_c => $tplDirArr[1], $_a => $tplDirArr[2]];
            }
        }
    }

    //URL模式 0:动态url传参 模式;1:pathinfo 模式
    if (C('URL_MODEL') == 0) {
        //生成URL字符串
        $url = '/' . SCRIPT_NAME . '?' . http_build_query($path);
        if ($urlCase) {
            $url = strtolower($url);
        }
        if (!empty($vars)) {
            $vars = http_build_query($vars);
            $url .= '&' . $vars;
        }
    }
    if (C('URL_MODEL') == 1) {
        //生成URL字符串
        $url = '/' . $path[$_m] . $depr . $path[$_c] . $depr . $path[$_a];
        //var_dump($url);
        //URL映射 自动生成替换后的URL
        $DOMAIN_URL_MAP = C('DOMAIN_URL_MAP');
        if (!empty($DOMAIN_URL_MAP)) {
            $search  = array_values($DOMAIN_URL_MAP);
            $replace = array_keys($DOMAIN_URL_MAP);
            foreach ($search as $key => $value) {
                //preg_replace($value, $replace[$key], $url);
                //只替换全部匹配的 避免替换部分匹配的路由 导致生成的url错误
                //home/bang/zypx会被替换成zypx home/bang/zypx_test不会被替换
                if (($str = strtolower(str_ireplace($value, $replace[$key], $url))) == '/' . strtolower($replace[$key])) {
                    $url = $str;
                }
                //U('index/index')生成//
                if ($url == '//') {
                    $url = '/Home/Index/index';
                }
            }
            // var_dump($search, $replace, $url);
            //$url = strtolower(str_ireplace($search, $replace, $url));
        }
        //var_dump($url);
        //子域名映射替换
        $SUB_DOMAIN_MAP_DEPLOY = C('SUB_DOMAIN_MAP_DEPLOY'); //开关
        $SUB_DOMAIN_MAP        = C('SUB_DOMAIN_MAP'); //子域名映射配置
        if ($SUB_DOMAIN_MAP_DEPLOY && !empty($SUB_DOMAIN_MAP)) {
            //var_dump($SUB_DOMAIN_MAP, $url, $path);
            //子域名映射匹配当前模块 自动生成子域名url
            foreach ($SUB_DOMAIN_MAP as $key => $value) {
                //U方法参数(模块名/控制器/方法)模块名=模块名=映射名
                //var_dump($path[$_m], MODULE_NAME . '|' . $value);
                //if ($path[$_m] == MODULE_NAME && strtolower(MODULE_NAME) == strtolower($value)) {
                if (strtolower($path[$_m]) == strtolower($value)) {
                    $childDomain = strtolower($key);
                    $domain      = $_SERVER['HTTP_HOST'];
                    $domainArr   = explode('.', $domain);
                    //缺省的顶级域名
                    if (count($domainArr) < 2) {
                        $domain = $childDomain . '.' . $domain;
                    }
                    //子域名
                    else {
                        $domainArr[0] = $childDomain; //替换成对应子域名
                        $domain       = implode('.', $domainArr);
                        //var_dump($url, ucfirst($path[$_m]));
                        //去掉多余的分组url
                        $i   = 1; //只替换一次
                        $url = str_ireplace(ucfirst($path[$_m]) . '/', '', strtolower($url), $i);
                    }
                    //var_dump($url);
                }
            }
        }
        if ($urlCase) {
            $url = strtolower($url);
        }
        if (!empty($vars)) {
            // 添加参数
            foreach ($vars as $var => $val) {
                if ('' !== trim($val)) {
                    $url .= $depr . $var . $depr . urlencode($val);
                }

            }
        }
    }

    if (isset($anchor)) {
        $url .= '#' . $anchor;
    }
    if ($domain) {
        $url = (is_ssl() ? 'https://' : 'http://') . $domain . $url;
    }
    //var_dump($url);
    return $url;
}

/**
 *默认过滤函数
 */
function default_filter_func(&$value)
{
    // 过滤查询特殊字符
    if (preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i', $value)) {
        $value .= ' ';
    }
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false)
{
    $type      = $type ? 1 : 0;
    static $ip = null;
    if ($ip !== null) {
        return $ip[$type];
    }

    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }

            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 远程调用控制器的操作方法 URL 参数格式 [资源://][模块/]控制器/操作
 * @param string $url 调用地址
 * @param string|array $vars 调用参数 支持字符串和数组
 * @return mixed
 */
function R($url, $vars = array())
{
    $urlArr     = explode('/', $url);
    $module     = array_shift($urlArr) ?: C('MODULE_NAME');
    $controller = (array_shift($urlArr) ?: C('CONTROLLER_NAME')) . C('DEFAULT_C_NAME');
    $action     = array_shift($urlArr) ?: C('ACTION_NAME');
    $className  = "\\{$module}\\Controller\\{$controller}";
    //var_dump($url, $module, $controller, $action, $className, $vars);exit;
    $class = A($className);
    if ($class) {
        if (is_string($vars)) {
            parse_str($vars, $vars);
        }
        $vars = array_merge($vars, $urlArr);
        return call_user_func_array(array($class, $action . C('ACTION_SUFFIX')), $vars); //这种写法导致$this->assign传参方法会失效 问题TODO
    } else {
        return false;
    }
}
