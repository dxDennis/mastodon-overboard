<?php

namespace DecodoMastodonService\Controller;

class Request
{
    protected $arguments;
    protected $uri;

    protected bool $parsedUri = false;
    protected bool $parsedArguments = false;
    public $baseUrl = '';

    /**
     * @param $var
     * @param $default
     * @return mixed|null
     */
    public static function getPostVar($var, $default = NULL)
    {
        return self::get($var, $default, 'POST');
    }

    /**
     * @param $in
     * @return bool
     */
    public static function toBoolean($in): bool
    {
        return ($in !== 'false' && $in !== '0');
    }

    /**
     * @param $var
     * @param $default
     * @param $type
     * @return mixed|null
     */
    public static function get($var, $default = NULL, $type = NULL)
    {

        $type = isset($type) ? strtoupper($type) : 'REQUEST';
        switch ($type) {
            default:
            case 'VAR':
                $var = $var ?? $default;
                break;
            case 'GET':
                $var = $_GET[$var] ?? $default;

                break;
            case 'REQUEST':
                $var = $_REQUEST[$var] ?? $default;
                break;
            case 'POST':
                $var = $_POST[$var] ?? $default;
                break;
        }

        return $var;
    }

    /**
     * @return array
     */
    public static function getPostVars(): array
    {
        return $_POST;
    }

    /**
     * @return bool
     */
    public function isParsed(): bool
    {
        return $this->parsedUri === true && $this->parsedArguments === true;
    }

    /**
     * @return void
     */
    public function parse()
    {
        $this->getUri();
        $this->getArguments();
    }

    /**
     * @return mixed
     */
    public function getUri()
    {

        if ($this->uri === NULL) {
            $requestUri = ($_SERVER['REQUEST_URI'] ?? false);
            if (!$requestUri) {
                throw new \RuntimeException(sprintf('can´t resolve "%s"requested uri', $requestUri));
            }
            $this->setUri($this->cleanUri($requestUri));
            $this->parsedUri = true;
        }
        return $this->uri;
    }

    /**
     * @param $requestUri
     * @return $this
     */
    public function setUri($requestUri): Request
    {
        $this->uri = $requestUri;
        return $this;
    }

    /**
     * @param $requestUri
     * @return mixed
     */
    public function cleanUri($requestUri)
    {
        if (!empty($this->getBaseUrl())) {
            $requestUri = str_replace([rtrim($this->getBaseUrl(), '/') . '/', 'index.php'], '', trim($requestUri));
        }
        $requestUriParts = explode('?', trim($requestUri, '/'));
        return $requestUriParts[0];
    }

    /**
     * @return mixed
     */
    public function getArguments()
    {
        if ($this->arguments === NULL) {
            $queryString = (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : false);
            if (!$queryString) {
                throw new \RuntimeException(sprintf('can´t resolve "%s"requested uri', $queryString));
            }
            $this->setArguments($queryString);
            $this->parsedArguments = true;
        }
        return $this->arguments;
    }

    /**
     * @param $arguments
     * @return $this
     */
    public function setArguments($arguments): Request
    {
        parse_str($arguments, $this->arguments);
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        if (empty($this->baseUrl)) {
            global $router;
            $this->baseUrl = $router::getBaseUrl();
        }
        return $this->baseUrl;
    }
}
