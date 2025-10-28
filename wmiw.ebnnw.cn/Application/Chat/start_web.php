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
use \Workerman\WebServer;
use \Workerman\Autoloader;

// 自动加载类
require_once __DIR__ . '/../../Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);

$config = require __DIR__ . '/config.php';

// WebServer
$web = new WebServer($config['web']['listen']);
// WebServer数量
$web->count = max(1, (int) $config['web']['processes']);
// 设置站点根目录
$documentRoot = $config['web']['document_root'];
if (!is_dir($documentRoot)) {
    throw new \RuntimeException(sprintf('Web document root "%s" is not a directory.', $documentRoot));
}
$web->addRoot($config['web']['domain'], $documentRoot);

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

