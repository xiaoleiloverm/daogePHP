#daogePHP

	/**
     *sql model example
     */
    //getOnce/fetchRow
    var_dump(M()->getOnce("SELECT username,id,password,email,last_logintime,last_loginip FROM tz_admin WHERE username = ?", ['admin'], null, true));
    // //fetchRowMany/getAll
    //exp1. var_dump(M()->getAll("SELECT *FROM tz_admin WHERE username = ?", ['admin'], 0));
    //exp2.M()->getAll("SELECT condition,name,m,c,a,data FROM {$this->_config['AUTH_RULE']} WHERE id IN (:ids) and type =:type and status =:status", ['ids' => $ids, 'type' => $type, 'status' => 1]);
    //exp3 join select sql
    //M()->getAll("SELECT `uid`,`group_id`,`title`,`rules` FROM {$this->_config['AUTH_GROUP_ACCESS']} a INNER JOIN {$this->_config['AUTH_GROUP']} g on a.group_id=g.id  WHERE ( a.uid=:uid and g.status=:status )", ['uid' => $uid, 'status' => '1']);
    // //fetchColumn
    // var_dump(M()->fetchColumn("SELECT *FROM tz_admin WHERE username = ?", ['admin'], 0));
    // //fetchColumnMany
    // var_dump(M()->fetchColumnMany("SELECT *FROM tz_admin WHERE username = ?", ['admin']));
    // //getOnce
    // M()->getOnce("SELECT condition,name,m,c,a,data FROM {$this->_config['AUTH_USER']} WHERE uid =:id", ['uid' => $uid])

    //update
    // $conds = [
    //     'username' => 'admin',
    //     'id'       => 1,
    // ];
    // $data = [
    //     'email' => '502928809@qq.com.cn',
    // ];
    // $condsQuery = 'username = :username and id = :id';
    // $res        = M()->update('tz_admin', $conds, $data, $condsQuery);
    // var_dump($res);

    // //insert
    // $data = array(
    //     'id'       => false,
    //     'username' => 'localhost',
    //     'email'    => 'localhost',
    // );
    // $id = M()->insert('tz_admin', $data);
    // var_dump($id); // 14 || bool

    //replace
    // $data = [
    //     'id'       => false,
    //     'username' => 'localhost',
    //     'email'    => 'localhost',
    // ];
    // $id = M()->replace('tz_admin', $data);
    // var_dump($id); // array || bool

    // //delete IN
    // $conds      = ['ids' => ['15', '16', '17', '18']];
    // $condsQuery = 'id IN (:ids)';
    // $result     = M()->delete('tz_admin', $conds, $condsQuery);
    // var_dump($result); // true || false

    // //delete =或者>=
    // $conds = ['id' => 32]; //id=32
    // //$condsQuery = 'id >= :id';//使用该句条件变为id>=32
    // $result = M()->delete('tz_admin', $conds, $condsQuery);
    // var_dump($result); // true || false

    /**
     *log example
     */
    //$log = new Log('info', 'local');
    //Log::record('debug', ['this is a {userName} info', 'framwork:{userName}'], ['extend' => ['function' => 'start', 'method' => 'public static'], 'replace' => ['{userName}' => 'daogePHP']]);

    //日志
    
    //快捷调用 根据配置使用
        //Library\Controller\Log\Log::record('info', $message);

    //直接调用sealog
    //sealog格式统一为： {type} | {pid} | {timeStamp} |{dateTime} | {logInfo} 如
    //1. ERRO | 7670 | 1393171368.875 | 2014:02:24 00:02:48 | test error 3 
    //2. INFO | 7670 | 1393171372.344 | 2014:02:24 00:02:52 | this is a info
    //sealog是php的扩展 配置 php.ini
    /*
    [seaslog]
        ; configuration for php SeasLog module
        extension = php_seaslog.dll
        seaslog.default_basepath = /log/seaslog-test            ;默认log根目录
        seaslog.default_logger = default                        ;默认logger目录
        seaslog.disting_type = 0                                ;是否以type分文件 1是 0否(默认)
        seaslog.disting_by_hour = 0                             ;是否每小时划分一个文件 1是 0否(默认)
        seaslog.use_buffer = 1                                  ;是否启用buffer 1是 0否(默认)
        seaslog.buffer_size = 100                               ;buffer中缓冲数量 默认0(不使用buffer_size)
        seaslog.level = 0                                       ;记录日志级别 默认0(所有日志)
        seaslog.trace_error = 1                                 ;自动记录错误 默认1(开启)
        seaslog.trace_exception = 0                             ;自动记录异常信息 默认0(关闭)
        seaslog.default_datetime_format = "Y:m:d H:i:s"         ;日期格式配置 默认"Y:m:d H:i:s"
        ; version 1.6.0
        seaslog.appender = 1                                    ;日志存储介质 1File 2TCP 3UDP (默认为1)
        seaslog.remote_host = 127.0.0.1                         ;接收ip 默认127.0.0.1 (当使用TCP或UDP时必填)
        seaslog.remote_port = 514                               ;接收端口 默认514 (当使用TCP或UDP时必填)
    */
        //------- 调用 sealog -------//
        //调用sealog
        $message = "[系统] test {$time}";
        //初始化
        //hander方法 日志级别（默认值为构造方法初始化的值），消息
        $handler = (object) ['path' => APP_LOG_PATH];
        $log     = new \Library\Controller\Log\Log('info', 'local', $handler, 'seaslog');
        //1).调用固定方法 日志级别默认debug
        $log->record('info', $message);
        //2)动态调用hander方法
        $log->getLogHander()->record('info', $message); //也可以写成record('', $message) 第一参数可以不给 默认值是构造方法初始化的

        //------- 调用 monolog -------//
        $message = "[系统] test {$time}";
        $handler = new \Monolog\Handler\StreamHandler(APP_LOG_PATH . 'app_' . date('Y-m-d', time()) . '.log');
        $handler->setFormatter(new \Monolog\Formatter\LineFormatter(null, null, true, true)); //格式化消息,格式化时间,允许消息内有换行,忽略空白的消息(去掉[])
        $log = new \Library\Controller\Log\Log('info', 'local', $handler, 'monolog');
        $log->record('info', $message);

    //monolog
    //记录日志
    // $handler = new StreamHandler(APP_LOG_PATH . 'app_' . date('Y-m-d', time()) . '.log');
    // $handler->setFormatter(new LineFormatter(null, null, true, true)); //格式化消息,格式化时间,允许消息内有换行,忽略空白的消息(去掉[])
    // $log = new MonoLog('local', 'emergency', $handler);
    // $log->record('', 'fatalError', $e);
