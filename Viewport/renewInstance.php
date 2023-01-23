<?php
/**
 * @var $routingData array
 */

use DecodoMastodonService\Controller\Log\LogController;
use DecodoMastodonService\Controller\Mastodon\InstanceController;

$instanceController = new InstanceController();

if (!empty($routingData['action'])) {
    $instances = $instanceController::$instances = [
        $routingData['action'] => [
            'name' => $routingData['action'],
            'uri' => 'https://' . $routingData['action'],
        ]
    ];
}

foreach ($instanceController::$instances as $instanceName => $instance) {
    LogController::getLogger()->info('IP:' . $_SERVER['REMOTE_ADDR'] .
        ' renewInstances: ' . $routingData['action'], [__FILE__ . ' ' . __LINE__]);
    $instanceController->getCachedSingleInstanceTrends($instance, true);
}

header('location: ' . HOME_URL . '#instance-refreshed');
exit;
