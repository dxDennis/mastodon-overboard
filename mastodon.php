<?php
/**
 * instances:
 *
 *
 */

use DecodoMastodonService\Component\Main\MainHelper;
use DecodoMastodonService\Controller\Router;
use Dotenv\Dotenv;

require_once __DIR__ . '/bootstrap.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

define('CURRENT_USER', ($_COOKIE['isAdmin'] ?? ''));

$cookiePath = "/";
setcookie("cookieName", "", time() - 3600, $cookiePath);
unset ($_COOKIE['cookieName']);

$router = new Router();
$routingData = $router::route('/mastodon');
if($routingData['method'] === 'GET'){
    if(file_exists(__DIR__.'/Viewport/'.$routingData['viewport'].'.php')){
        include_once __DIR__.'/Viewport/'.$routingData['viewport'].'.php';
        exit;
    }
}
include_once __DIR__.'/Viewport/error.php';
exit;




//$requestAction = (isset($_GET['action']) && !empty($_GET['action']) ? trim($_GET['action']) : '');
//$requestId = (isset($_GET['id']) && (int)$_GET['id'] > 0 ? (int)$_GET['id'] : 0);
//$requestInstance = (isset($_GET['instance']) && !empty($_GET['instance']) ? trim($_GET['instance']) : '');
//
//if (isset($_GET['logout']) && !empty($_GET['logout'])) {
//    $requestAction = 'logout';
//}
//
//if ($requestId > 0 && !empty($requestInstance)) {
//    $requestAction = 'getInstance';
//}
//
//switch ($requestAction) {
//    default:
//        include_once __DIR__ . '/Viewport/mainList.php';
//        break;
//
//    case 'renewInstance':
//        $helper = new MainHelper();
//
//        if (isset($helper->instanceController::$instances[$requestInstance])) {
//            $helper->instanceController->getCachedSingleInstanceTrends($helper->instanceController::$instances[$requestInstance], true);
//            $helper->instanceController->getCachedSingleInstanceStats($helper->instanceController::$instances[$requestInstance], true);
//            header('location: ' . HOME_URL . '#renewInstance-' . $requestInstance);
//            exit;
//        }
//
//        header('location: ' . HOME_URL . '#renewInstance-' . $requestInstance . '-fail');
//        exit;
//        break;
//
//    case 'logout':
//        setcookie("isAdmin", "", time() - 3600, $cookiePath);
//        unset ($_COOKIE['isAdmin']);
//        header('location: /mastodon/#logout');
//        exit;
//        break;
//
//    case 'login':
//        $username = $_GET['user'] ?? '';
//        $password = $_GET['password'] ?? '';
//        if ($username === $_ENV['ADMIN'] && $password === $_ENV['PASSWORD']) {
//            $cookieExpire = time() + (60 * 60 * 24);
//            setcookie("isAdmin", $_GET['user'], $cookieExpire, $cookiePath);
//            header('location: ' . HOME_URL . '#logged-in');
//            exit;
//        }
//        header('location: ' . HOME_URL . '#login-failed');
//        exit;
//
//    case 'stats':
//        include_once __DIR__ . '/Viewport/getInstanceStats.php';
//        break;
//
//    case 'getInstance':
//        include_once __DIR__ . '/Viewport/getInstance.php';
//        break;
//
//    case 'test':
//
//        echo '<pre>' . print_r([
//                '' => $_ENV,
//            ], 1) . __FILE__ . ' ' . __LINE__ . '</pre>';
//        exit;
//        break;
//
//    case 'reset':
//        $helper = new MainHelper();
//        $helper->getTrendCollection(true);
//        header('location: ' . HOME_URL . '#reset');
//        exit;
//}


