<?php
$registerHost = getenv('CHAT_REGISTER_HOST');
if($registerHost === false || $registerHost === '')
{
    $registerHost = '0.0.0.0';
}

$registerPort = getenv('CHAT_REGISTER_PORT');
if($registerPort === false || $registerPort === '')
{
    $registerPort = 1236;
}
else
{
    $registerPort = (int) $registerPort;
}

$registerProtocol = getenv('CHAT_REGISTER_PROTOCOL');
if($registerProtocol === false || $registerProtocol === '')
{
    $registerProtocol = 'text';
}

$gatewayHost = getenv('CHAT_GATEWAY_HOST');
if($gatewayHost === false || $gatewayHost === '')
{
    $gatewayHost = '0.0.0.0';
}

$gatewayLanIp = getenv('CHAT_GATEWAY_LAN_IP');
if($gatewayLanIp === false || $gatewayLanIp === '')
{
    $gatewayLanIp = '127.0.0.1';
}

$gatewayPort = getenv('CHAT_GATEWAY_PORT');
if($gatewayPort === false || $gatewayPort === '')
{
    $gatewayPort = 7272;
}
else
{
    $gatewayPort = (int) $gatewayPort;
}

$gatewayStartPort = getenv('CHAT_GATEWAY_START_PORT');
if($gatewayStartPort === false || $gatewayStartPort === '')
{
    $gatewayStartPort = 4000;
}
else
{
    $gatewayStartPort = (int) $gatewayStartPort;
}

$gatewayProcesses = getenv('CHAT_GATEWAY_PROCESSES');
if($gatewayProcesses === false || $gatewayProcesses === '')
{
    $gatewayProcesses = 2;
}
else
{
    $gatewayProcesses = (int) $gatewayProcesses;
}

$gatewayPingInterval = getenv('CHAT_GATEWAY_PING_INTERVAL');
if($gatewayPingInterval === false || $gatewayPingInterval === '')
{
    $gatewayPingInterval = 25;
}
else
{
    $gatewayPingInterval = (int) $gatewayPingInterval;
}

$pingDataEnv = getenv('CHAT_GATEWAY_PING_DATA');
$defaultPingPayload = array(
    'type' => 'ping',
    'info' => '昂酷网络(oncoo.net)',
);
if($pingDataEnv === false || $pingDataEnv === '')
{
    $pingOptions = defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0;
    $pingData = json_encode($defaultPingPayload, $pingOptions);
    if($pingData === false)
    {
        $pingData = json_encode($defaultPingPayload);
    }
}
else
{
    $pingData = $pingDataEnv;
}

$businessProcesses = getenv('CHAT_BUSINESS_PROCESSES');
if($businessProcesses === false || $businessProcesses === '')
{
    $businessProcesses = 2;
}
else
{
    $businessProcesses = (int) $businessProcesses;
}

$webListen = getenv('CHAT_WEB_LISTEN');
if($webListen === false || $webListen === '')
{
    $webListen = 'http://0.0.0.0:55151';
}

$webProcesses = getenv('CHAT_WEB_PROCESSES');
if($webProcesses === false || $webProcesses === '')
{
    $webProcesses = 1;
}
else
{
    $webProcesses = (int) $webProcesses;
}

$webDomain = getenv('CHAT_WEB_DOMAIN');
if($webDomain === false || $webDomain === '')
{
    $webDomain = 'localhost';
}

$webDocumentRoot = getenv('CHAT_WEB_DOCUMENT_ROOT');
if($webDocumentRoot === false || $webDocumentRoot === '')
{
    $webDocumentRoot = __DIR__ . '/../../Public';
}
$webDocumentRootResolved = realpath($webDocumentRoot);
if($webDocumentRootResolved !== false)
{
    $webDocumentRoot = $webDocumentRootResolved;
}

return array(
    'register' => array(
        'host' => $registerHost,
        'port' => $registerPort,
        'protocol' => $registerProtocol,
    ),
    'gateway' => array(
        'host' => $gatewayHost,
        'lan_ip' => $gatewayLanIp,
        'port' => $gatewayPort,
        'start_port' => $gatewayStartPort,
        'processes' => $gatewayProcesses,
        'ping_interval' => $gatewayPingInterval,
        'ping_data' => $pingData,
    ),
    'business' => array(
        'processes' => $businessProcesses,
    ),
    'web' => array(
        'listen' => $webListen,
        'processes' => $webProcesses,
        'domain' => $webDomain,
        'document_root' => $webDocumentRoot,
    ),
);
