<?php
/**
 * +------------------------------------------------------------------
 * |daogePHP： 框架核心日志类 -> 日志PDO处理器 写入日志到数据库
 * +------------------------------------------------------------------
 * |athor：leilu<xiaoleiloverm@gmail.com>
 * +------------------------------------------------------------------
 * |Copyright (c) 2016-2018 All rights reserved.
 * +------------------------------------------------------------------
 */
namespace Library\Controller\Log;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class PDOHandler extends AbstractProcessingHandler
{
    private $initialized = false;
    private $pdo;
    private $statement;

    public function __construct(PDO $pdo, $level = Logger::DEBUG, $bubble = true)
    {
        //pdo依赖注入
        $this->pdo = $pdo;
        parent::__construct($level, $bubble);
    }

    /**
     *写日志操作
     * @param  string  $channel 频道(日志处理器) 默认 local
     * @param  string  $level 日志等级
     * @param  string  $formatted 日志消息
     * @param  string  $time 时间
     * @return void
     */
    protected function write(array $record)
    {
        if (!$this->initialized) {
            $this->initialize();
        }
        $this->statement->execute(
            [
                'channel' => $record['channel'],
                'level'   => $record['level'],
                'message' => $record['formatted'],
                'time'    => $record['datetime']->format('U'),
            ]
        );
    }

    //日志的数据表
    private function initialize()
    {
        $dbname = 'app_' . date('Y-m', time()) . '.log';
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS ' . $dbname
            . ' (channel VARCHAR(255), level INTEGER, message LONGTEXT, time INTEGER UNSIGNED)'
        );
        $this->statement = $this->pdo->prepare(
            'INSERT INTO monolog (channel, level, message, time) VALUES (:channel, :level, :message, :time)'
        );
    }
}
