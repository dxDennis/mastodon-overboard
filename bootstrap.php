<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);

const HOME_URL = 'https://www.decodo.de/mastodon/';

const ROOT_DIRECTORY = __DIR__ . '/';
const VAR_DIRECTORY = ROOT_DIRECTORY . 'var/';
const CACHE_DIRECTORY = VAR_DIRECTORY . 'cache/';
const LOG_DIRECTORY = VAR_DIRECTORY . 'log/';
const TEMPLATE_DIRECTORY = ROOT_DIRECTORY . 'Template/';

require_once ROOT_DIRECTORY . 'vendor/autoload.php';
