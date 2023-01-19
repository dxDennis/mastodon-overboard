<?php

namespace DecodoMastodonService\Controller\Log;

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LogController
{
    public static $logger;

    /**
     * @return mixed
     */
    public static function getLogger()
    {
        if (!self::$logger) {
            self::$logger = new Logger($_ENV['APP_INSTANCE']);
            self::$logger->pushHandler(new StreamHandler(LOG_DIRECTORY . 'application-' . date('Ymd') . '.log',
                Logger::DEBUG));
            self::$logger->pushHandler(new FirePHPHandler());
        }
        return self::$logger;
    }

}
