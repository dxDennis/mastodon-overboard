<?php

namespace DecodoMastodonService\Controller;

class Router
{
    const ROUTE_BASE_DIR = ROOT_DIRECTORY;
    const DEFAULT_VIEWPORT = 'main';

    private static $currentUri = '';
    private static string $baseUrl = '/';
    private static string $query = '';
    private static string $viewport = 'main';
    private static string $method = 'GET';
    private static string $options = '';
    private static string $action = '';


    public static function route($baseUrl = '/')
    {
        self::$baseUrl = $baseUrl;
        self::$method = $_SERVER['REQUEST_METHOD'];

        $currentUri = ltrim(self::getCurrentUri(), '/');
        if (!empty(self::$baseUrl)) {
            $currentUri = trim(str_replace(trim(self::$baseUrl, '/'), '', trim($currentUri)), '/');
        }

        $parts = explode('/', $currentUri);

        self::$query = $_SERVER['QUERY_STRING'];

        if (count($parts) > 0 && $parts[0] !== '') {
            self:: $viewport = array_shift($parts);

            if (count($parts) > 0) {
                self::$action = array_shift($parts);
                if (count($parts) > 0) {
                    self::$options = array_shift($parts);
                }
            }
        }

        return [
            'viewport' => self::getViewport(),
            'action' => self::getAction(),
            'options' => self::getOptions(),
            'method' => self::getMethod(),
            'query' => self::getQuery(),
            'currentUri()' => self::getCurrentUri(),
            'currentUri' => $currentUri,
            'self' => self:: $viewport,
            'baseUrl' => self::$baseUrl,

        ];
    }

    /**
     * @return string
     */
    public static function getCurrentUri(): string
    {
        if (empty(self::$currentUri)) {
            self::$currentUri = str_replace([self::ROUTE_BASE_DIR, self::getQuery(), '?'], '', trim($_SERVER['REQUEST_URI'], '/'));
        }
        return self::$currentUri;
    }


    /**
     * @return string
     */
    public static function getQuery()
    {
        if (!self::$query) {
            self::$query = $_SERVER['QUERY_STRING'];
        }
        return self::$query;
    }


    /**
     * @return string
     */
    public static function getOptions()
    {
        return self::$options;
    }


    /**
     * @return string
     */
    public static function getViewport()
    {
        return (!empty(self::$viewport) ? self::$viewport : self::DEFAULT_VIEWPORT);
    }

    /**
     * @return string
     */
    public static function getAction()
    {
        return self::$action;
    }

    /**
     * @return string
     */
    public static function getMethod(): string
    {
        return self::$method;
    }

    /**
     * @return string
     */
    public static function getBaseUrl(): string
    {
        return self::$baseUrl;
    }


}
