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
    //sealog TODO
    //$handler = (object) ['path' => (C('LOG_PATH') ? C('LOG_PATH') : APP_LOG_PATH)];

    //monolog
