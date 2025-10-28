<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
use \Workerman\Worker;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

// 自动加载类
require_once __DIR__ . '/../../Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);

$config = require __DIR__ . '/config.php';

// bussinessWorker 进程
$worker = new BusinessWorker();
// worker名称
$worker->name = 'ChatBusinessWorker';
// bussinessWorker进程数量
$worker->count = max(1, (int) $config['business']['processes']);
// 服务注册地址
$worker->registerAddress = sprintf('%s:%d', $config['register']['host'], $config['register']['port']);

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

