<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心数据库操作类，mysql
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Model\Driver;

use Library\Construct\Model\Db\Db as DbAbstract;
use PDO;

class Mysql extends DbAbstract
{
    public $dbh; //连接对象

    public $lastStatement; //最后的结果集

    /**
     * 构造方法
     * @param  sql 需要预处理的sql
     * @param  $fetchMode 结果集类型
     * @return \PDOStatement
     */
    public function __construct($dbh, $fetchMode = \PDO::FETCH_ASSOC)
    {
        $this->setDbh($dbh);
        $this->setFetchMode($fetchMode);
    }

    // 设置连接实例 需要继承才能调用
    protected function setDbh($dbh)
    {
        return $this->dbh = $dbh;
    }

    //获取连接实例
    public function getDbh()
    {
        return $this->dbh;
    }

    /**
     * PDOStatement执行一条预处理语句
     * @param  sql 需要预处理的sql
     * @param  params 预处理参数
     * @return \PDOStatement
     */
    public function execute($sql, $params = [])
    {
        return parent::execute($sql, $params);
    }

    /**
     * PDOStatement 设置结果集获取对象
     *
     * @return \PDOStatement
     */
    protected function setLastStatement(\PDOStatement $cursor)
    {
        return $this->lastStatement = $cursor;
    }

    /**
     * PDOStatement 获取结果集获取对象
     *
     * @return \PDOStatement
     */
    protected function getLastStatement()
    {
        return $this->lastStatement;
    }

    /**
     * 是否存在获取结果集对象
     * @return bool
     */
    protected function hasLastStatement()
    {
        return $this->lastStatement ? true : false;
    }

    /**
     * 清除结果集获取对象
     *
     * @return mysql
     */
    public function clearLastStatement()
    {
        $this->lastStatement = null;
        return $this;
    }

    /**
     * @param int $fetchMode
     *
     * @return Mysql
     */
    protected function setFetchMode($fetchMode)
    {
        $this->fetchMode = $fetchMode;

        return $this;
    }

    /**
     * @return int
     */
    protected function getFetchMode()
    {
        return (int) $this->fetchMode;
    }

    /**
     * \PDOStatement 预处理绑定参数
     * @param \PDOStatement $pdoStatement
     * @param array $params
     * @param int length 数据类型的长度。为表明参数是一个存储过程的 OUT 参数，必须明确地设置此长度。
     * @param mixed driver_options
     *
     * @return \PDOStatement
     */
    protected function setParams(\PDOStatement $pdoStatement, array $params, $length = 0, $driver_options = null)
    {
        foreach ($params as $key => &$val) {
            //?占位符 bindParam($key,$val) $key需要从1开始
            $key === 0 && $key += 1;
            $pdoStatement->bindParam($key, $val, $this->getParamType($val));
        }

        return $pdoStatement;
    }

    /**
     * 预定义常量类型 用于绑定参数类型
     * @param mixed $paramValue
     *
     * @return int
     * @throws \PDOException
     */
    protected function getParamType($paramValue)
    {
        if (strpos($paramValue, '|') !== false) {
            //使用 INOUT 参数调用一个存储过程 PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT
            list($paramValue, $paramExtends) = explode('|', $paramValue);
        }
        switch ($paramValue) {
            case is_int($paramValue):
                return \PDO::PARAM_INT; //表示 SQL 中的整型。

            case is_bool($paramValue):
                return \PDO::PARAM_BOOL; //表示布尔数据类型。

            case is_string($paramValue):
                return \PDO::PARAM_STR; //表示 SQL 中的 CHAR、VARCHAR 或其他字符串类型。

            case is_float($paramValue):
                return \PDO::PARAM_STR;

            case is_double($paramValue):
                return \PDO::PARAM_STR;

            case is_null($paramValue):
                return \PDO::PARAM_NULL; //表示 SQL 中的 NULL 数据类型。

            default:
                throw new \PDOException("Invalid param type: {$paramValue} with type {gettype($paramValue)}");
        }
    }

    /**
     * 生成预处理错误代码
     * @param array $errorInfo
     *
     * @return array
     */
    protected function prepareErrorInfo(array $errorInfo)
    {
        return [
            'sqlStateCode' => $errorInfo[0], //SQLSTATE 错误码（一个由5个字母或数字组成的在 ANSI SQL 标准中定义的标识符）。
            'code'         => $errorInfo[1], //具体驱动错误码。
            'message'      => $errorInfo[2], //具体驱动错误信息。
        ];
    }

    //打印一条 SQL 预处理命令 调试模式
    protected function debugDumpParams()
    {
        if ($this->hasLastStatement() === false) {
            return false;
        }
        $this->getLastStatement()->debugDumpParams();
    }

    /**
     * IN 条件查询 拼接预定义语句和参数
     * @param string $query
     * @param array $params
     *
     * @return bool
     *
     *  example:
     *  $conds = array('ids' => array(1,2,3));
     *  $query = "SELECT * FROM users WHERE id IN (:ids)";
     * //params:['ids'=>[1,2,3],['status' =>[0]] keys:[':ids1',':ids2',':ids3',':status1']
     * //params:[':ids1'=>1,':ids2'=>2,':ids3'=>3,':status1'=>0]
     */
    protected function handleInCondition(&$query, &$params)
    {
        if (empty($params)) {
            return true;
        }

        foreach ($params as $key => $val) {
            if (is_array($val)) {
                $keys = [];

                foreach ($val as $k => $v) {
                    // new param name
                    $keyString = ':' . $key . $k;

                    // cache new params
                    $keys[] = $keyString;

                    // add new params
                    $params[$keyString] = $v;
                }

                // include new params
                $query = str_replace(':' . $key, join(',', $keys), $query);

                // remove actual param
                unset($params[$key]);
            }
        }

        return true;
    }

    /**
     * Select 查询 预处理
     * @param string $query
     * @param array $conds
     *
     * @return \PDOStatement
     * @throws \PDOException
     */
    protected function prepareSelect($query, array $conds)
    {
        // 清除 最后的 statement
        $this->clearLastStatement();

        // IN 条件查询 拼接
        $this->handleInCondition($query, $conds);

        // 设置 query
        $pdoStatement = $this->getDbh()->prepare($query);

        // 绑定指定参数
        $pdoStatement = $this->setParams($pdoStatement, $conds);

        // 执行预处理
        $pdoStatement->execute();

        // 检查错误 00000为sql预处理成功
        if ($pdoStatement->errorCode() !== '00000') {
            $error = [
                'query'     => $query,
                'params'    => $conds,
                'errorInfo' => $this->prepareErrorInfo($pdoStatement->errorInfo()),
            ];
            $errorInfo = json_encode($error);
            throw new \PDOException($errorInfo);
        }

        // 缓存 statement
        $this->setLastStatement($pdoStatement);

        return $pdoStatement;
    }

    /**
     * Insert 操作 预处理
     * @param string $query
     * @param array $rowsMany
     *
     * @return array
     * @throws \PDOException
     */
    protected function prepareInsertReplace($query, array $rowsMany)
    {
        $dbh       = $this->getDbh();
        $responses = [];

        // 清除 最后的 statement
        $this->clearLastStatement();

        // 设置 query
        $pdoStatement = $dbh->prepare($query);

        // 遍历行
        while ($row = array_shift($rowsMany)) {
            // 绑定参数
            $pdoStatement = $this->setParams($pdoStatement, $row);

            // 执行预处理
            $pdoStatement->execute();

            // SQL预处理异常
            if ($pdoStatement->errorCode() !== '00000') {
                $error = [
                    'query'     => $query,
                    'errorInfo' => $this->prepareErrorInfo($pdoStatement->errorInfo()),
                ];

                $errorInfo = json_encode($error);

                throw new \PDOException($errorInfo);
            }

            // 最后插入ID int|null
            $lastInsert = $dbh->lastInsertId();

            // 缓存结果
            $responses[] = $lastInsert ? (int) $lastInsert : true;
        }

        return $responses;
    }

    /**
     * Update 操作 预处理
     * @param string $query 查询语句
     * @param array $conds 条件数组
     * @param array $data 数据数组
     *
     * @return null|bool
     * @throws PDOException
     */
    protected function prepareUpdate($query, array $conds, array $data)
    {
        // 清除 最后的 statement
        $this->clearLastStatement();

        // 设置 query
        $pdoStatement = $this->getDbh()->prepare($query);

        // 绑定指定参数
        $pdoStatement = $this->setParams($pdoStatement, $conds);

        // 绑定指定参数
        $pdoStatement = $this->setParams($pdoStatement, $data);

        // 执行预处理
        $pdoStatement->execute();

        // 检查错误 00000为sql预处理成功
        if ($pdoStatement->errorCode() !== '00000') {
            $error = [
                'query'     => $query,
                'params'    => $conds,
                'errorInfo' => $this->prepareErrorInfo($pdoStatement->errorInfo()),
            ];
            $errorInfo = json_encode($error);
            throw new \PDOException($errorInfo);
        }

        if ($this->getRowCount() === 0) {
            return null;
        }
        return true;

    }

    /**
     * Delete 操作 预处理
     * @param string $query 查询语句
     * @param array $conds 条件数组
     *
     * @return null|bool
     * @throws PDOException
     */
    protected function prepareDelete($query, array $conds)
    {
        // 清除 最后的 statement
        $this->clearLastStatement();

        // IN 条件查询 拼接
        $this->handleInCondition($query, $conds);

        // 设置 query
        $pdoStatement = $this->getDbh()->prepare($query);

        // 绑定指定参数
        $pdoStatement = $this->setParams($pdoStatement, $conds);

        // 执行预处理
        $pdoStatement->execute();

        // 检查错误 00000为sql预处理成功
        if ($pdoStatement->errorCode() !== '00000') {
            $error = [
                'query'     => $query,
                'params'    => $conds,
                'errorInfo' => $this->prepareErrorInfo($pdoStatement->errorInfo()),
            ];
            $errorInfo = json_encode($error);
            throw new \PDOException($errorInfo);
        }

        if ($this->getRowCount() === 0) {
            return null;
        }
        return true;

    }

    /**
     *  执行一条无参数绑定的查询
     * @param string $query
     *
     * @return bool
     * @throws PDOException
     */
    public function executeSql($query)
    {
        $dbh = $this->getDbh();
        $res = $dbh->exec($query);
        if ($res !== false) {
            return true;
        }
        $error = [
            'query'     => $query,
            'errorInfo' => $this->prepareErrorInfo($dbh->errorInfo()),
        ];
        $errorInfo = json_encode($error);
        throw new \PDOException($errorInfo);
    }

    /**
     * 选取数据库
     * @param string $dbName
     *
     * @return bool
     * @throws \PDOException
     */
    public function selectDb($dbName)
    {
        return $this->executeSql('use ' . $dbName);
    }

    /**
     * 从结果集中获取下一行
     * @param string $query 查询语句
     * @param array $conds 绑定参数
     * @param \PDOStatement $fetchMode 结果集类型 默认 \PDO::FETCH_ASSOC
     *
     * @return array|null
     */
    public function fetchRow($query, array $conds = [], \PDOStatement $fetchMode = null)
    {
        $fetchMode == '' && $fetchMode = $this->getFetchMode();
        $response                      = $this->prepareSelect($query, $conds)->fetch($fetchMode);

        if ($response === false) {
            return null;
        }

        return (array) $response;
    }

    /**
     * 从结果集中获取全部行
     * @param string $query 查询语句
     * @param array $conds 绑定参数
     * @param \PDOStatement $fetchMode 结果集类型 默认 \PDO::FETCH_ASSOC
     *
     * @return array|null
     */
    public function fetchRowMany($query, array $conds = [], \PDOStatement $fetchMode = null)
    {
        $responsesMany                 = [];
        $fetchMode == '' && $fetchMode = $this->getFetchMode();
        $pdoStatement                  = $this->prepareSelect($query, $conds);
        while ($response = $pdoStatement->fetch($fetchMode)) {
            $responsesMany[] = $response;
        }

        if (empty($responsesMany)) {
            return null;
        }

        return (array) $responsesMany;
    }

    /**
     * 从结果集中获取下一行 最为对象返回
     * @PHP 5 >= 5.1.0, PHP 7, PECL pdo >= 0.2.4
     * @param string $class_name 创建类的名称
     * @param array $ctor_args 此数组的元素被传递给构造函数
     *
     * @return bool|object
     */
    public function fetchObject($class_name = __CLASS__, array $ctor_args = [])
    {
        if ($this->hasLastStatement() === false) {
            return false;
        }
        return (object) $this->getLastStatement()->fetchObject($class_name, $ctor_args);
    }

    /**
     * 从结果集中获取下一行 同 fetchRow
     * @param string $query 查询语句
     * @param array $conds 绑定参数
     * @param \PDOStatement $fetchMode 结果集类型 默认 \PDO::FETCH_ASSOC
     * @param bool $isDebug 是否需要调试 true 调试时会打印一条 SQL 预处理命令
     *
     * @return array|null
     */
    public function getOnce($query, array $conds = [], \PDOStatement $fetchMode = null, $isDebug = false)
    {
        $res = $this->fetchRow($query, $conds, $fetchMode);
        if ($isDebug) {
            $this->debugDumpParams();
        }
        return $res;
    }

    /**
     * 从结果集中获取全部行 同 fetchRowMany
     * @param string $query 查询语句
     * @param array $conds 绑定参数
     * @param \PDOStatement $fetchMode 结果集类型 默认 \PDO::FETCH_ASSOC
     *
     * @return array|null
     */
    public function getAll($query, array $conds = [], \PDOStatement $fetchMode = null)
    {
        $res = $this->fetchRowMany($query, $conds, $fetchMode);
        return $res;
    }

    /**
     * 获取影响记录的行数
     * @param void
     *
     * @return bool|int
     */
    public function getRowCount()
    {
        if ($this->hasLastStatement() === false) {
            return false;
        }
        return $this->getLastStatement()->rowCount();
    }

    /**
     * 从结果集中的下一行返回单独的一列
     * @param string $query 查询语句
     * @param array $conds 绑定参数
     * @param int $index 列偏移量 初始值为0 第一列 n表示为n+1列
     *
     * @return string|null
     */
    public function fetchColumn($query, array $conds = [], $index = 0)
    {
        $response = $this->prepareSelect($query, $conds)->fetchColumn(intval($index));

        if ($response === false) {
            return null;
        }

        return (string) $response;
    }

    /**
     * 从结果集中的下一行返回所有列
     * @param string $query 查询语句
     * @param array $conds 绑定参数
     *
     * @return array|null
     */
    public function fetchColumnMany($query, array $conds = [])
    {
        $responsesMany = [];
        $pdoStatment   = $this->prepareSelect($query, $conds);

        while ($response = $pdoStatment->fetchColumn()) {
            $responsesMany[] = $response;
        }

        if (empty($responsesMany)) {
            return null;
        }

        return (array) $responsesMany;
    }

    /**
     * 插入单条数据
     * @param string $tableName 表名
     * @param array $data 数据
     * @param bool $insertIgnore 是否忽略存在数据 false:不忽略(默认),true:忽略(忽略后可以防止数据重复插入)
     *
     * @return int|bool
     * @throws \PDOException
     */
    public function insert($tableName, array $data, $insertIgnore = false)
    {
        if (isset($data[0])) {
            throw new \PDOException("数据格式不正确. 请使用 'Mysql::insertMany()' 插入");
        }

        $response = $this->insertMany($tableName, [$data], $insertIgnore);

        if ($response === false) {
            return false;
        }

        return array_pop($response);
    }

    /**
     * 插入多条数据
     * @param string $tableName 表名
     * @param array $data 数据
     * @param bool $insertIgnore 是否忽略存在数据 false:不忽略(默认),true:忽略(忽略后可以防止数据重复插入)
     *
     * @return array|bool
     * @throws \PDOException
     */
    public function insertMany($tableName, array $data, $insertIgnore = false)
    {
        if (!isset($data[0])) {
            throw new \PDOException("数据格式不正确. 请使用 'Mysql::insert()' 插入");
        }

        $query = 'INSERT' . ($insertIgnore === true ? ' IGNORE ' : null) . ' INTO ' . $tableName . ' (:COLUMN_NAMES) VALUES (:PARAM_NAMES)';

        $placeholder = [
            'column_names' => [],
            'param_names'  => [],
        ];

        foreach ($data[0] as $columnName => $value) {
            $placeholder['column_names'][] = '`' . $columnName . '`';
            $placeholder['param_names'][]  = ':' . $columnName;
        }

        $query = str_replace(':COLUMN_NAMES', join(', ', $placeholder['column_names']), $query);
        $query = str_replace(':PARAM_NAMES', join(', ', $placeholder['param_names']), $query);

        $response = $this->prepareInsertReplace($query, $data);

        if (empty($response)) {
            return false;
        }

        return (array) $response;
    }

    /**
     * 替换插入单条数据(同上插入数据操作,当有重复的主键或惟一索引的行,旧的行会在插入新行前被删除,防止数据重复插入)
     * @param string $tableName
     * @param array $data
     *
     * @return array|bool
     * @throws \PDOException
     */
    public function replace($tableName, array $data)
    {
        if (isset($data[0])) {
            throw new \PDOException("数据格式不正确. 请使用 'Mysql::replaceMany()' 插入");
        }

        return $this->replaceMany($tableName, [$data]);
    }

    /**
     * 替换插入多条数据
     * @param string $tableName
     * @param array $data
     *
     * @return array|bool
     * @throws \PDOException
     */
    public function replaceMany($tableName, array $data)
    {
        if (!isset($data[0])) {
            throw new \PDOException("数据格式不正确. 请使用 'Mysql::replace()' 插入");
        }

        $query = 'REPLACE INTO ' . $tableName . ' (:COLUMN_NAMES) VALUES (:PARAM_NAMES)';

        $placeholder = [
            'column_names' => [],
            'param_names'  => [],
        ];

        foreach ($data[0] as $columnName => $value) {
            $placeholder['column_names'][] = '`' . $columnName . '`';
            $placeholder['param_names'][]  = ':' . $columnName;
        }

        $query = str_replace(':COLUMN_NAMES', join(', ', $placeholder['column_names']), $query);
        $query = str_replace(':PARAM_NAMES', join(', ', $placeholder['param_names']), $query);

        $response = $this->prepareInsertReplace($query, $data);

        if (empty($response)) {
            return false;
        }

        return (array) $response;
    }

    /**
     * 更新操作
     * @param string $tableName 表名
     * @param array $conds 条件数组（_前缀的为特殊键）
     * @param array $data 更新的字段数据映射数组
     * @param null $condsQuery 条件字符串
     *
     * @return bool
     * @throws \PDOException
     */
    public function update($tableName, array $conds, array $data, $condsQuery = null)
    {
        if (isset($data[0])) {
            throw new \PDOException("数据格式不正确");
        }

        $query = 'UPDATE ' . $tableName . ' SET :PARAMS WHERE :CONDS';

        $placeholder = [
            'params' => [],
            'conds'  => [],
        ];

        foreach ($data as $columnName => $value) {
            $placeholder['params'][] = '`' . $columnName . '` = :DATA_' . $columnName;

            // mark data keys in case CONDS and DATA hold the same keys
            unset($data[$columnName]);
            $data['DATA_' . $columnName] = $value;
        }

        $query = str_replace(':PARAMS', join(', ', $placeholder['params']), $query);
        $query = $this->buildCondsQuery($query, $conds, $condsQuery);

        return $this->prepareUpdate($query, $conds, $data);
    }

    /**
     * 删除操作
     * @param string $tableName 表名
     * @param array $conds 条件数组（_前缀的为特殊键）
     * @param null $condsQuery 条件字符串
     *
     * @return bool
     * @throws \PDOException
     */
    public function delete($tableName, array $conds = [], $condsQuery = null)
    {
        $query    = $this->buildCondsQuery('DELETE FROM ' . $tableName . ' WHERE :CONDS', $conds, $condsQuery);
        $response = $this->prepareDelete($query, $conds);

        if ($response === true) {
            return true;
        }

        return false;
    }

    /**
     * 插入删除操作组织条件语句
     * @param string $query 查询语句
     * @param array $conds 条件数组 为空代表条件无WHERE条件，慎用
     * @param string|null $condsQuery 条件字符串，WHERE后面部分
     *
     * @return string
     */
    private function buildCondsQuery($query, array $conds, $condsQuery = null)
    {
        //条件数组不为空
        if (!empty($conds)) {
            if ($condsQuery === null) {
                $placeholder = [];

                foreach ($conds as $columnName => $value) {
                    if ($this->isColum($columnName)) {
                        $placeholder[] = '`' . $columnName . '` = :' . $columnName;
                    }
                }

                $query = str_replace(':CONDS', join(' AND ', $placeholder), $query);
            }
            //条件字符串组织数组条件
            else {
                $query = str_replace(':CONDS', $condsQuery, $query);
            }
        }
        //无条件
        else {
            $query = str_replace(' WHERE :CONDS', '', $query);
        }

        return $query;
    }

    /**
     * 验证字段条件数组的键为非特殊键 true:非特殊键会组织成条件语句字符串
     * @param string $key
     *
     * @return bool
     */
    private function isColum($key)
    {
        return substr($key, 0, 1) !== '_';
    }

}
