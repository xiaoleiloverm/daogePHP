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
    protected function setParams(\PDOStatement $pdoStatement, array $params,$length=0,$driver_options=null)
    {
        foreach ($params as $key => &$val) {
            $pdoStatement->bindParam($key, $val, $this->getParamType($val));
        }

        return $pdoStatement;
    }

    /**
     * 预定义常量类型 用于绑定参数类型
     * @param mixed $paramValue
     *
     * @return int
     * @throws MysqlException
     */
    protected function getParamType($paramValue)
    {   
        if(strpos($paramValue, '|') !== false){
            list($paramValue,$paramExtends) = explode('|', $paramValue)
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

            case (strpos($paramValue, '|') !== false):
                return \PDO::PARAM_STR; //使用 INOUT 参数调用一个存储过程 PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT
            case is_null($paramValue):
                return \PDO::PARAM_NULL; //表示 SQL 中的 NULL 数据类型。

            default:
                throw new MysqlException("Invalid param type: {$paramValue} with type {gettype($paramValue)}");
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
     * @throws MysqlException
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

            throw new MysqlException($errorInfo);
        }

        // 缓存 statement
        $this->setLastStatement($pdoStatement);

        return $pdoStatement;
    }

    /**
     * Update 操作 预处理
     * @param string $query 查询语句
     * @param array $conds 条件数组
     * @param array $data 数据数组
     *
     * @return null|bool
     * @throws MysqlException
     */
    protected function prepareUpdate($query, array $conds, array $data)
    {
        // 清除 最后的 statement
        $this->clearLastStatement();

        // 设置 query
        $pdoStatement = $this->getDbh()->prepare($query);

        // 绑定指定参数
        /////TODO
        $pdoStatement = $this->bindParam($pdoStatement, $conds);

    }

    //打印一条 SQL 预处理命令 调试模式
    protected function debugDumpParams(){

    }
}
