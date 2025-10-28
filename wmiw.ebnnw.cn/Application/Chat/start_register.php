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
use \GatewayWorker\Register;

// 自动加载类
require_once __DIR__ . '/../../Workerman/Autoloader.php';

$config = require __DIR__ . '/config.php';
$registerListen = sprintf(
    '%s://%s:%d',
    $config['register']['protocol'],
    $config['register']['host'],
    $config['register']['port']
);
$register = new Register($registerListen);

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

