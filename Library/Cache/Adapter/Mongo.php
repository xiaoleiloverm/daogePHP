<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心缓存适配器 Mongo MongoDb
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Cache\Adapter;

use Library\Construct\Cache\AbstractAdapter;

class Mongo extends AbstractAdapter
{
    protected $collection; //连接实例

    protected $databaseName; //数据库名(新版php驱动mongodb)

    protected $collectionName; //连接名(新版php驱动mongodb)

    protected $writeConcern; //

    /**
     * PHP Driver可以查看安装后phpinfo扩展名(mongodb 老版mongo)和版本号
     *
     * 扩展对MOngoDB版本的支持
     * PHP Driver       mongoDB2.4       mongoDB2.6       mongoDB3.0       mongoDB3.2
     * mongodb-1.1      √                √                √                √
     * mongodb-1.0      √                √                √
     * mongo-1.6        √                √                √
     * mongo-1.5        √                √
     *
     *扩展对php版本的支持
     *
     * PHP Driver       PHP5.3       PHP5.4       PHP5.5       PHP5.6       PHP7       HHVM3.9
     * mongodb-1.1                   √            √            √            √          √
     * mongodb-1.0                   √            √            √                       √
     * mongo-1.6        √            √            √            √
     * mongo-1.5        √            √            √            √
     */

    /**
     * 构造方法 初始化缓存连接实例
     * @param  object $collection 连接实例
     * @param  string $database 初始连接数据库名
     * @param  string $database 初始连接名
     * @param  object \MongoDB\Driver\WriteConcern $writeConcern 写操作对象
     * @return void
     */
    public function __construct($collection, $databaseName, $collectionName = 'collection', $writeConcern)
    {
        if (!is_object($collection) || !isset($collection)) {
            //兼容老版本
            if (class_exist('MongoCollection')) {
                $collection                 = new \MongoClient();
                $databaseName ? $collection = $collection->selectDatabase($databaseName) : $collection = $collection->selectDatabase('cache');
            } elseif (class_exist('\MongoDB\Client')) {
                $collection                 = new \MongoDB\Client();
                $databaseName ? $collection = $collection->selectDatabase($databaseName) : $collection = $collection->selectDatabase('cache');
            }
            //新版Driver 1.1.9
            elseif (class_exist('\MongoDB\Driver\Manager')) {
                $collection = new \MongoDB\Driver\Manager('mongodb://127.0.0.1:27017');
            }
        }
        //初始连接数据库名和连接名
        $databaseName ? $this->databaseName     = $databaseName : $this->databaseName     = 'cache';
        $collectionName ? $this->collectionName = $collectionName : $this->collectionName = 'collection';
        //writeConcern
        if ($writeConcern instanceof \MongoDB\Driver\WriteConcern) {
            $this->writeConcern = $writeConcern;
        } else if (is_null($writeConcern)) {
            $this->writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        }
        //旧版使用 MongoCollection|\MongoDB\Collection|MongoDB\Database|MongoDB
        //新版本 使用 \MongoDB\Driver\Manager
        if ($collection instanceof \MongoCollection ||
            $collection instanceof \MongoDB\Collection ||
            $collection instanceof \MongoDB\Driver\Manager
        ) {
            $this->collection = $collection;
        }
        //旧版另外一种连接方式
        elseif ($backend instanceof \MongoDB || $backend instanceof \MongoDB\Database) {
            $this->collection = $collection->selectCollection('items');
        } else {
            $type = (is_object($collection) ? get_class($collection) . ' ' : '') . gettype($collection);
            throw new CacheException("Database should be a database (MongoDB or MongoDB\Database) or " .
                " collection (MongoCollection or MongoDB\Collection or \MongoDB\Driver\Manager) object, not a $type");
        }
    }

    /**
     * 删除缓存
     * @param  string $key 键
     * @return bool
     */
    public function del($key)
    {
        $this->delete($key);
    }

    /**
     * 删除操作
     * @param  string $key 键
     * @return bool
     */
    protected function delete($key)
    {
        $tKey = $this->getKey($key);
        if ($this->collection instanceof \MongoDB\Driver\Manager) {
            $bulk = new \MongoDB\Driver\BulkWrite;
            $bulk->delete(['_id' => $tKey]);
            $connName = $this->databaseName . '.' . $this->collectionName;
            $result   = $this->collection->executeBulkWrite($connName, $bulk, $this->writeConcern);
            //错误
            if ($writeConcernError = $result->getWriteConcernError()) {
                $error = sprintf("%s (%d): %s\n", $writeConcernError->getMessage(), $writeConcernError->getCode(), var_export($writeConcernError->getInfo(), true));
                throw new CacheException($error);
            }
            return $result->getDeletedCount(); //返回影响记录的行数
        } else {
            $this->collection->remove(array('_id' => $tKey));
        }
    }

    /**
     * 获取缓存
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $this->select($key);
    }

    /**
     * 查询操作
     * @param  string $key 键
     * @return bool
     */
    protected function select($key)
    {
        $tKey = $this->getKey($key);
        $tNow = $this->getTtl();
        if ($this->collection instanceof \MongoDB\Driver\Manager) {
            $data = $this->collection->findOne(array('_id' => $tKey, 'ttl' => array('$gte' => $tNow)));
            if (isset($data)) {
                return $this->unPack($data['value']);
            }
        } else {
            //过滤条件 有效期 TODO
            $filter = ['ttl' => ['$gt' => $tNow]];
            //返回条件匹配文档
            $options = [
                "projection" => ["_id" => 0], //查询条件
                //"sort"       => ["_id" => -1],//排序
                "modifiers"  => ['$comment' => "This is a query comment", '$maxTimeMS' => 100], //查询附加参数
            ];
            $query          = new MongoDB\Driver\Query($filter, $options);
            $readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);
            $connName       = $this->databaseName . '.' . $this->collectionName;
            $cursor         = $this->collection->executeQuery($connName, $query, $readPreference);
            if (isset($cursor[0]['value'])) {
                return $this->unPack($cursor[0]['value']);
            }
        }
        return false;

    }

    /**
     * 缓存是否存在
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->get($key) ? true : false;
    }

    /**
     * 设置缓存 新增
     *
     * @param string $key 键
     * @param mixed  $value 值
     * @param bool   $flag：是否用MEMCACHE_COMPRESSED来压缩存储的值，true表示压缩，false表示不压缩。
     * @param int   $ttl 缓存生存周期 设置值为多少s后过期
     */
    public function set($key, $value, $ttl = null)
    {
        $tKey   = $this->getKey($key);
        $tValue = $this->pack($value);
        if (!$ttl) {
            $ttl = $this->ttl;
        }
        $item = array(
            '_id'   => $tKey,
            'value' => $tValue,
            'ttl'   => $this->getTtl($ttl),
        );
        if ($this->collection instanceof \MongoDB\Driver\Manager) {
            $bulk = new \MongoDB\Driver\BulkWrite;
            //TODO 判断缓存是否存在是否过期 存在并未过期就更新
            if ($data = $this->get($key)) {
                //
            }
            $bulk->insert([$tKey => $item]);
            $connName = $this->databaseName . $this->collectionName;
            $result   = $this->collection->executeBulkWrite($connName, $bulk, $this->writeConcern);
            //错误
            if ($writeConcernError = $result->getWriteConcernError()) {
                $error = sprintf("%s (%d): %s\n", $writeConcernError->getMessage(), $writeConcernError->getCode(), var_export($writeConcernError->getInfo(), true));
                throw new CacheException($error);
            }
            return $result->getInsertedCount(); //返回影响记录的行数
        } else {
            $this->collection->update(array('_id' => $tKey), $item, array('upsert' => true));
        }
    }

    /**
     * Get TTL as Date type BSON object
     *
     * @param  int  $ttl
     * @return MongoDate|MongoDB\BSON\UTCDatetime
     */
    protected function getTtl($ttl = 0)
    {
        if ($this->collection instanceof \MongoCollection) {
            return new \MongoDate((int) $ttl + time());
        } else {
            $DTObject = new \MongoDB\BSON\UTCDatetime(((int) $ttl + time() * 1000));
            return (string) $DTObject;
        }

    }
}
