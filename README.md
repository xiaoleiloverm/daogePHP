#daogePHP

#控制器操作
    控制器
    传值：
    $this->assign('waitSecond', 1);
    加载模版
    例1：$this->display();
    例2：$this->display('admin/index');

#sql操作
	/**
     *sql model example
     */
##查询 getOnce/fetchRow
### getOnce
    M()->getOnce("SELECT username,id,password,email,last_logintime,last_loginip FROM tz_admin WHERE username = ?", ['admin'], null, true);
	M()->getOnce("SELECT condition,name,m,c,a,data FROM {$this->_config['AUTH_USER']} WHERE uid =:id", ['uid' => $uid]);
### fetchRowMany/getAll
#### exp1.
	M()->getAll("SELECT *FROM tz_admin WHERE username = ?", ['admin'], 0);
#### exp2.
	M()->getAll("SELECT condition,name,m,c,a,data FROM {$this->_config['AUTH_RULE']} WHERE id IN (:ids) and type =:type and status =:status", ['ids' => $ids, 'type' => $type, 'status' => 1]);
#### exp3 join select sql
    M()->getAll("SELECT `uid`,`group_id`,`title`,`rules` FROM {$this->_config['AUTH_GROUP_ACCESS']} a INNER JOIN {$this->_config['AUTH_GROUP']} g on a.group_id=g.id  WHERE ( a.uid=:uid and g.status=:status )", ['uid' => $uid, 'status' => '1']);
#### fetchColumn
    var_dump(M()->fetchColumn("SELECT *FROM tz_admin WHERE username = ?", ['admin'], 0));
#### fetchColumnMany
    var_dump(M()->fetchColumnMany("SELECT *FROM tz_admin WHERE username = ?", ['admin']));

## update 更新数据
    例1 update 方法
    $conds = [
        'username' => 'admin',
        'id'       => 1,
    ];
    $data = [
        'email' => '502928809@qq.com.cn',
    ];
    //condsQuery 默认会采用and连接　当where有复杂的条件关系时可以使用该参数
    $condsQuery = 'username = :username and id = :id';
    $res        = M()->update('tz_admin', $conds, $data, $condsQuery);
    例2 execute 方法
    $ip = $_SERVER['REMOTE_ADDR'];
    $sql   = "UPDATE hb_admin SET last_loginip = '{$ip}'  WHERE username = :username";
    $param = ['username' => 'admin'];
    $res   = M()->execute($sql, $param);


## insert 增加数据
    $data = array(
        'id'       => false,
        'username' => 'localhost',
        'email'    => 'localhost',
    );
    $id = M()->insert('tz_admin', $data);
    var_dump($id); // 14 || bool

## replace 替换
	$data = [
        'id'       => false,
        'username' => 'localhost',
        'email'    => 'localhost',
    ];
    $id = M()->replace('tz_admin', $data);
    var_dump($id); // array || bool

## delete 删除
	//使用 IN 删除多个
    $conds      = ['ids' => ['15', '16', '17', '18']];
    $condsQuery = 'id IN (:ids)';
    $result     = M()->delete('tz_admin', $conds, $condsQuery);
   	var_dump($result); // true || false

    //delete =或者>=
    $conds = ['id' => 32]; //id=32
    //$condsQuery = 'id >= :id';//使用该句条件变为id>=32
    $result = M()->delete('tz_admin', $conds, $condsQuery);
    var_dump($result); // true || false

    //executeSql （一般用于增删改） 执行一条无参数绑定的sql
    M()->executeSql('use xxxDb');
    //execute （一般用于增删改）执行一条参数绑定的sql (PDOStatement执行一条预处理语句) 返回true false
    //例： login_count字段值加1
    $sql   = 'UPDATE hb_admin SET login_count = login_count+1  WHERE username = :username';
    $param = ['username' => 'admin'];
    $res   = M()->execute($sql, $param);
    if($res){
        //成功
    }else{
        //失败
    }
    
## mysql表达式查询 
### select表达式查询多条：
    table 数据表；
    limit 偏移量；用数组 $rows条数,$offset偏移量(默认0) (如[10,1])或者字符串,隔开(如:10,1)；
    order 排序；单个的话'order'=>'create_time DESC' 多个数组 如 'order' => ['create_time DESC', 'status DESC']
    group 分组；gourp和order类似 用字符串或数组均可，数组或者,(英文半角逗号)隔开字符 表示多个
    inner 内连接；'inner'=>['tableName'=>'','alias'=>'',condsQuery=>'']
    left 左连接；同inner参数
    where 条件；1.必须是一个sql表达式
                2.可以为字符串
                3.数组 多个表达式用and关系 也可以组织复杂的where关系 见下文例3
    例1.简单的字符串where
    $options = [
        'table' => 'hb_admin',
        'where' => 'status !=-1',
        'order' => ['create_time DESC', 'status DESC'],
    ];
    $res = M()->select($options);
    例2.组织复杂的where关系
    $options = [
            'table' => 'hb_admin',
            'where' => [
                ['status !=-1'],
                ['create_time >0'],
            ],
            'order' => ['create_time DESC', 'status DESC'],
        ];
        $res = M()->select($options);
    //SELECT * FROM hb_admin WHERE status !=-1 AND create_time >0 ORDER BY `create_time` DESC, `status` DESC
    例3.组织复杂的where关系
    $options = [
            'table' => 'hb_admin',
            'where' => [
                ['status !=-1', 'OR'],
                ['create_time >0'],
            ],
            'order' => ['create_time DESC', 'status DESC'],
        ];
        $res = M()->select($options);
    //SELECT * FROM hb_admin WHERE status !=-1 OR create_time >0 ORDER BY `create_time` DESC, `status` DESC
    
### 表达式查询单条find
    $res = M()->find($options);

    left连接查询
    SELECT * FROM hb_members LEFT JOIN hb_members_info AS info ON info.uid = hb_members.uid WHERE status !=-1 AND create_time >0 GROUP BY hb_members.uid ORDER BY `create_time` DESC, `status` DESC LIMIT 0, 10
    查询完整例子1：
    $options = [
        'table' => 'hb_members',
        'left'  => [
            'tableName'  => 'hb_members_info',
            'alias'      => 'info',
            'condsQuery' => 'info.uid = hb_members.uid',
        ],
        'where' => [
            ['status !=-1'],
            ['create_time >0'],
        ],
        'order' => ['create_time DESC', 'status DESC'],
        'group' => ['hb_members.uid'],
        'limit' => [10, 0], //等价'limit' => '10,0'
    ];
    $res = M()->select($options);
    例子2：使用占位符
    $options = [
        'table' => 'hb_members',
        'left'  => [
            'tableName'  => 'hb_members_info',
            'alias'      => 'info',
            'condsQuery' => 'info.uid = hb_members.uid',
        ],
        'where' => [
            ['status !=:status'],
            ['create_time >:ctime'],
        ],
        'order' => ['create_time DESC', 'status DESC'],
        'group' => ['hb_members.uid'],
        'limit' => [10, 0], //等价'limit' => '10,0'
    ];
    //作用是替换占位符
    $cond = [
        'status' => -1,
        'ctime'  => 0,
    ];
    $res = M()->select($options, $cond);

### 获取表达式查询的sql语句
    $sql = M()->getSql();

### 统计数据条数
    $count = M()->count($options);

    /**
     *log example
     */
    //$log = new Log('info', 'local');
    //Log::record('debug', ['this is a {userName} info', 'framwork:{userName}'], ['extend' => ['function' => 'start', 'method' => 'public static'], 'replace' => ['{userName}' => 'daogePHP']]);

# 日志
    
## 快捷调用 根据配置使用
    Library\Controller\Log\Log::record('info', $message);

## 直接调用 seaslog
    //sealog格式统一为： {type} | {pid} | {timeStamp} |{dateTime} | {logInfo} 如
    //1. ERRO | 7670 | 1393171368.875 | 2014:02:24 00:02:48 | test error 3 
    //2. INFO | 7670 | 1393171372.344 | 2014:02:24 00:02:52 | this is a info
    //sealog是php的扩展 配置 php.ini
    
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
    
        //------- 调用 seaslog -------//
        //由于seaslog是php扩展实现的 调用的是系统默认时区
        //如果日志记录的{dateTime}部分 时间不正确 可以设置php.ini 设置默认时区  如上海时区 date.timezone ="asia/shanghai"
        //调用seaslog
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

## 直接调用 monolog
    //记录日志
    // $handler = new StreamHandler(APP_LOG_PATH . 'app_' . date('Y-m-d', time()) . '.log');
    // $handler->setFormatter(new LineFormatter(null, null, true, true)); //格式化消息,格式化时间,允许消息内有换行,忽略空白的消息(去掉[])
    // $log = new MonoLog('local', 'emergency', $handler);
    // $log->record('', 'fatalError', $e);
