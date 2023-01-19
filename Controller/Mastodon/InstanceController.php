<?php

namespace DecodoMastodonService\Controller\Mastodon;

use DecodoMastodonService\Controller\Log\LogController;
use DecodoMastodonService\Controller\Mastodon\Api\ApiController;
use DecodoMastodonService\Model\CacheModel;

class InstanceController
{
    public static $instances = [
        'connectit.social' => [
            'name' => 'connectit.social',
            'uri' => 'https://connectit.social',
        ],
        'mastodon.social' => [
            'name' => 'mastodon.social',
            'uri' => 'https://mastodon.social',
        ],
        'social.bund.de' => [
            'name' => 'social.bund.de',
            'uri' => 'https://social.bund.de',
        ],'chaos.social' => [
            'name' => 'chaos.social',
            'uri' => 'https://chaos.social',
        ],
    ];

    public function __construct()
    {
    }

    /****************************************************** NEW STATS ****************************************/
    /**
     * @param bool $reset
     * @return array
     */
    public function getInstanceStatsCollection(bool $reset = false): array
    {
        foreach (self::$instances as $instanceName => $instance) {

            $results = $this->getCachedSingleInstanceStats($instance, $reset);
            if ($results) {
                self::$instances[$instanceName] = array_merge(self::$instances[$instanceName], $results);
            }
        }
        return self::$instances;
    }

    /**
     * @param $instance
     * @param bool $reset
     * @param string $expirationTime
     * @return array|false
     */
    public function getCachedSingleInstanceStats($instance, bool $reset = false, string $expirationTime = '1 Day'): array
    {

        $cacheModel = new CacheModel();
        $cacheKey = __METHOD__ . '_' . $instance['name'];
        $cacheItem = $cacheModel->get($cacheKey);

        LogController::getLogger()->info('IP:' . $_SERVER['REMOTE_ADDR'] .
            ' isHit: '.$cacheItem->isHit().
            ' reset: '.$reset.
            ' expirationTime: '.$expirationTime.
            ' CacheKey:' . $cacheKey, [__FILE__ . ' ' . __LINE__]);


        if (!$cacheItem->isHit() || $reset !== false) {
            $results = $this->getSingleInstanceStats($instance);

            if ($results !== false) {
                $cacheItem = $cacheModel->write($results, CacheModel::toDate($expirationTime));
            }
        } else {

            LogController::getLogger()->info('IP:' . $_SERVER['REMOTE_ADDR'] .
                ' From Cache: '.$cacheItem->getExpirationDate()->format('Y-m-d H:i:s'), [__FILE__ . ' ' . __LINE__]);

        }

        $results['expires_at'] = $cacheItem->getExpirationDate()->format('Y-m-d H:i:s');
        $results['expiration_time'] = $expirationTime;
        $results['cache_key'] = $cacheKey;
        $results['data'] = $cacheItem->get();
        $results['count'] = (isset($results['data']) ? count($results['data']) : 0);

        return $results;
    }

    public function getSingleInstanceStats($instance)
    {
        $apiController = new ApiController();
        $cacheCallResponse = $apiController->call($instance['uri'] . '/api/v2/instance');

        if (!$cacheCallResponse) {
            return false;
        }
        return json_decode($cacheCallResponse, 1);
    }

    /****************************************************** NEW TRENDS ****************************************/

    /**
     * @param $reset
     * @return \string[][]
     */
    public function getInstanceTrendsCollection($reset = false): array
    {
        foreach (self::$instances as $instanceName => $instance) {
            self::$instances[$instanceName] = array_merge(self::$instances[$instanceName],
                $this->getCachedSingleInstanceTrends($instance, $reset));
        }
        return self::$instances;
    }

    /**
     * @param $instance
     * @param bool $reset
     * @param string $expirationTime
     * @return false|mixed
     */
    public function getCachedSingleInstanceTrends($instance, bool $reset = false, string $expirationTime = '15 minutes')
    {

        $cacheModel = new CacheModel();
        $cacheKey = __METHOD__ . '_' . $instance['name'];
        $cacheItem = $cacheModel->get($cacheKey);

        LogController::getLogger()->info('IP:' . $_SERVER['REMOTE_ADDR'] .
            ' isHit: '.$cacheItem->isHit().
            ' reset: '.$reset.
            ' expirationTime: '.$expirationTime.
            ' CacheKey:' . $cacheKey, [__FILE__ . ' ' . __LINE__]);

        if (!$cacheItem->isHit() || $reset !== false) {
            $results = $this->getSingleInstanceTrends($instance);
            if (!$results === false) {
                $cacheItem = $cacheModel->write($results, CacheModel::toDate($expirationTime));
            }
        } else {
            LogController::getLogger()->info('IP:' . $_SERVER['REMOTE_ADDR'] .
                ' From Cache: '.$cacheItem->getExpirationDate()->format('Y-m-d H:i:s'), [__FILE__ . ' ' . __LINE__]);
        }

        $results['expires_at'] = $cacheItem->getExpirationDate()->format('Y-m-d H:i:s');
        $results['expiration_time'] = $expirationTime;
        $results['cache_key'] = $cacheKey;
        $results['data'] = $cacheItem->get();
        $results['count'] = (isset($results['data']) && is_array($results['data']) ?  count($results['data']):0);
        return $results;
    }

    public function getSingleInstanceTrends($instance)
    {
        $apiController = new ApiController();
        $cacheCallResponse = $apiController->call($instance['uri'] . '/api/v1/trends/statuses');
        if (!$cacheCallResponse) {
            return false;
        }
        return json_decode($cacheCallResponse, 1);
    }

}
