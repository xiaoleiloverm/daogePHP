<?php
//ini_set("display_errors", "On");
error_reporting(-1);

$utcdatetime = new MongoDB\BSON\UTCDateTime(1416445411987);
$utcdatetime = (string) $utcdatetime;
//var_dump($utcdatetime);exit;

/**
 *旧版连接
 */
//$connection = new \Mongo('mongodb://192.168.1.5:27017'); //链接到 192.168.1.5:27017//27017端口是默认的。
//$connection = new Mongo("example.com"); //链接到远程主机(默认端口)
//$connection = new Mongo("example.com:65432"); //链接到远程主机的自定义的端口

//$connection = new \mongoClient(); // 连接
//$db         = $connection->selectDB("example");
//var_dump($connection, $connection->listDBs()); //能打印出数据库数组，看看有几个数据库。
//

//var_dump(get_extension_funcs('mongodb')); //该扩展中提供的函数
//var_dump(get_declared_classes()); //所有扩展

/**
 *新版的mongodb连接
 */
$con = new MongoDB\Driver\Manager('mongodb://127.0.0.1:27017');
//$con2 = new MongoDB();
//$con3 = new \MongoDB\Collection();
//var_dump($con->findOne(), $con2, $con3);

//curd
function isert($con, $tKey, $tValue, $connName, $writeConcern)
{
    $item = array(
        '_id'   => $tKey,
        'value' => $tValue,
        'ttl'   => time() + 180,
    );
    $bulk = new \MongoDB\Driver\BulkWrite;
    //TODO 判断缓存是否存在是否过期 存在并未过期就更新
    $bulk->insert([$tKey => $item]);
    $result = $con->executeBulkWrite($connName, $bulk, $writeConcern);
    return $result->getInsertedCount();
}
$tKey         = '_test_id1';
$tValue       = 'this is test mongodb value';
$connName     = 'daogePHP.collection';
$writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
//$result       = isert($con, $tKey, $tValue, $connName, $writeConcern);

$filter = ['_test_id1' => $tKey];
$data   = getData($con, $tKey, $connName, $filter);
var_dump($data);

//读取
function getData($con, $tKey, $connName, $filter = [])
{
    //条件 $filter
    //查询参数
    $options = [
        "projection" => ["_test_id1" => 1], //只返回以下字段的匹配文档
        //"projection" => ["_id" => $tKey, 'ttl' => ['$gt' => $tNow]], //查询条件
        //"sort"       => ["_id" => -1],//排序
        //"modifiers"  => ['$comment' => "This is a query comment", '$maxTimeMS' => 100], //查询附加参数
    ];
    $query          = new \MongoDB\Driver\Query($filter, $options);
    $readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);
    $cursor         = $con->executeQuery($connName, $query, $readPreference);
    return $cursor;
    // $it = new IteratorIterator($cursor);
    // $it->rewind();
    // while ($doc = $it->current()) {
    //     print_r($doc);
    //     $it->next();
    //     echo '<br/>';
    // }

}
