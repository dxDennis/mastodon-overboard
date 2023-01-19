<?php

namespace DecodoMastodonService\Model;

use DateInterval;
use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Core\Item\ExtendedCacheItemInterface;
use Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface;
use Phpfastcache\Exceptions\PhpfastcacheDriverCheckException;
use Phpfastcache\Exceptions\PhpfastcacheDriverException;
use Phpfastcache\Exceptions\PhpfastcacheDriverNotFoundException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException;

if (!defined('_CACHE_PATH')) {
    define('_CACHE_PATH', dirname(__DIR__) . '/var/cache/');
}

class CacheModel
{
    const _CACHE_PATH = _CACHE_PATH;
    /**
     * @var null
     */
    public static $InstanceCache = NULL;
    /**
     * @var
     */
    public static $currentKey;
    /**
     * @var ExtendedCacheItemInterface
     */
    public static $cachedString;

    /**
     * @var array
     */
    public static $trace = [];

    /**
     * @param string $timeString
     * @return DateInterval
     */
    public static function toDate(string $timeString): DateInterval
    {
        return DateInterval::createFromDateString($timeString);
    }

    public static function setKey(string $key)
    {
        self::$currentKey = str_replace(['\\', '/', '::', ':', '.', ' '], '_', $key);
    }

    /**
     * @return ExtendedCacheItemPoolInterface|null
     * @throws PhpfastcacheDriverCheckException
     * @throws PhpfastcacheDriverException
     * @throws PhpfastcacheDriverNotFoundException
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheInvalidConfigurationException
     * @throws ReflectionException
     */
    public static function getInstanceCache()
    {
        if (!self::$InstanceCache) {
            self::$trace[] = 'init CacheManager';
            $configOptions = new ConfigurationOption([
                'path' => self::_CACHE_PATH
            ]);
            CacheManager::setDefaultConfig($configOptions);
            self::$InstanceCache = CacheManager::getInstance('files');
        }
        return self::$InstanceCache;
    }

    /**
     * @return ExtendedCacheItemInterface
     */
    private static function getCachedString(): ExtendedCacheItemInterface
    {
        return self::$cachedString;
    }

    /**
     * @return string|void
     */
    public static function getCurrentKey()
    {
        if (empty(self::$currentKey)) {
            die('no Key given');
        }
        return self::$currentKey;
    }

    /**
     * @param string $key
     */
    public function get(string $key): ExtendedCacheItemInterface
    {
        self::setKey($key);

        self::$cachedString = self::getInstanceCache()->getItem(self::getCurrentKey());
        self::$trace[] = 'return cachedString expiring in: ' . self::$cachedString->getExpirationDate()->format('c');
        return self::getCachedString();
    }

    /**
     * @param $data
     * @param $expiresAfter
     * @return ExtendedCacheItemInterface
     */
    public function write($data, $expiresAfter): ExtendedCacheItemInterface
    {
        self::$trace[] = 'writing data to cache-key: ' . self::getCurrentKey();
        self::getCachedString()->set($data)->expiresAfter($expiresAfter);
        self::$InstanceCache->save(self::getCachedString());
        return self::getCachedString();
    }


    public function resetCache(string $class, string $method, $args)
    {
        if (is_string($args)) {
            $args = [$args];
        }

        $cacheKey = $class . '::' . $method . '_' . implode('_', $args);
        $res = $this->get($cacheKey);

        if ($res instanceof Item) {
            $res->setExpirationDate(new \DateTime(date('Y-m-d H:i:s', strtotime('-1 day'))));
        }
    }

    /**
     * @var array
     */
    public array $keyList = [];

    /**
     * @return array|false
     * @throws PhpfastcacheDriverCheckException
     * @throws PhpfastcacheDriverException
     * @throws PhpfastcacheDriverNotFoundException
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheInvalidConfigurationException
     * @throws ReflectionException
     */
    public function clearExpiredCache()
    {
        $keyList = $this->getKeyList();
        if (!is_array($keyList) || empty($keyList)) {
            return false;
        }

        $sumSize = 0;
        $sumDeleted = 0;

        foreach ($keyList as $k => $i) {

            if (!isset($i['expired'])) {
                self::$trace[] = '<pre> skipped Entry: ' . print_r([
                        $k => $i,
                    ], 1) . __FILE__ . ' ' . __LINE__ . '</pre>';
                continue;
            }

            if (!$i['expired']) {
//                self::$trace[] = 'Skip key: ' . $i['key'] . ' expired:' . $i['expireTime'] . ' size:' . $i['filesize'];
                continue;
            }

            if (unlink($i['filename'])) {
                self::$trace[] = 'removed expired key: ' . $i['key'] . ' expired:' . $i['expireTime'] . ' size:' . Helper::formatBytes($i['filesize']);
                $sumDeleted++;
                $sumSize += $i['filesize'];
            } else {
                self::$trace[] = 'remove file ' . $i['filename'] . ' key: ' . $i['key'] . ' failed!';
            }

        }

        self::$trace[] = 'removed ' . $sumDeleted . ' files with combined size: ' . Helper::formatBytes($sumSize);

        return self::$trace;
    }

    /**
     * @return array|void
     * @throws PhpfastcacheDriverCheckException
     * @throws PhpfastcacheDriverException
     * @throws PhpfastcacheDriverNotFoundException
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheInvalidConfigurationException
     * @throws ReflectionException
     */
    public function getKeyList()
    {
        $instanceCache = self::getInstanceCache();
        $cacheStats = $instanceCache->getStats();
        $raw = $cacheStats->getRawData()['tmp'];
        $cacheDirectory = current($raw);

        if (empty($cacheDirectory) || !is_dir($cacheDirectory)) {
            die('cachePath does not exist!');
        }

        $this->keyList = [];
        $this->scanPath($cacheDirectory);

        return $this->keyList;
    }

    /**
     * @param $path
     * @param $level
     * @param $olderThan
     * @return bool
     */
    public function scanPath($path, $level = 0, $olderThan = NULL)
    {
        $level++;
        $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $pathItems = glob($path . '*');

        // return false if folder is empty
        if (!is_array($pathItems) || count($pathItems) === 0) {
            return false;
        }

        /**
         * @var $expireAt \DateTime
         */
        foreach ($pathItems as $pathItem) {

            if (is_dir($pathItem)) {
                if ($this->scanPath($pathItem, $level, $olderThan) === false) {
                    $traceLine = 'empty folder: ' . $pathItem;
                    if (rmdir($pathItem)) {
                        $traceLine .= ' removed!';
                    }
                    self::$trace[] = $traceLine;
                }
                continue;
            }

            $u = unserialize(file_get_contents($pathItem));
            $f = filemtime($pathItem);
            $expireAt = $u['e'];
            $expired = ($expireAt->format('U') < time());

            $fileInfo = [
                'filename' => $pathItem,
                'key' => $u['k'],
                'expireTimeU' => $expireAt->format('U'),
                'expireTime' => $expireAt->format('Y-m-d H:i:s'),
                'filemtime' => date('Y-m-d H:i:s', $f),
                'filesize' => filesize($pathItem),
                'expired' => $expired,
            ];

            $this->keyList[] = $fileInfo;
        }

        return true;
    }
}
