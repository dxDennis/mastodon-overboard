<?php
/**
 * @var $routingData array
 */

use DecodoMastodonService\Controller\Document\Document;
use DecodoMastodonService\Controller\Mastodon\InstanceController;
use DecodoMastodonService\Controller\Request;

$instance = Request::get('instance');

if (!empty(CURRENT_USER) && !empty($instance) && !empty($routingData['action']) && isset(InstanceController::$instances[$instance])) {
    Document::toJson(file_get_contents('https://' . $instance . '/api/v1/statuses/' . $routingData['action']));
}

header('location: ' . HOME_URL);
exit;

