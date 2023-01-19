<?php

namespace DecodoMastodonService\Controller\Mastodon\Api;

use DecodoMastodonService\Controller\Log\LogController;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiController
{
    public function call($uri)
    {
        LogController::getLogger()->info('IP:' . $_SERVER['REMOTE_ADDR'] . ' CALL URI:' . $uri, [__FILE__ . ' ' . __LINE__]);

        $client = new Client();
        try {
            $results = $client->request('GET', $uri);
        } catch (GuzzleException $e) {
            LogController::getLogger()->error('IP:' . $_SERVER['REMOTE_ADDR'] . ' FAIL:' . $uri, [$e]);
            return false;
        }

        $contents = $results->getBody()->getContents();
        LogController::getLogger()->info('IP:' . $_SERVER['REMOTE_ADDR'] . ' LENGTH:' . strlen($contents), [__FILE__ . ' ' . __LINE__]);

        return $contents;
    }
}
