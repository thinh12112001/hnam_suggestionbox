<?php
//define css
//define('CSS_BACKGROUND_BODY', 'linear-gradient(90deg, rgba(68,135,43,1) 12%, rgba(66,171,72,1) 34%, rgba(66,179,86,1) 49%, rgba(18,177,52,1) 75%, rgba(143,222,148,0.9780287114845938) 93%, rgba(20,246,7,0.7847514005602241) 100%)');
//define('CSS_BACKGROUND_IMAGE_BODY', 'https://vectormienphi.com/wp-content/uploads/2020/01/T%E1%BB%95ng-h%E1%BB%A3p-h%C3%ACnh-n%E1%BB%81n-full-HD-1920-x-1080-%C4%91%E1%BA%B9p-nh%E1%BA%A5t-2.jpg');
//define('CSS_BACKGROUND_HEADER', '#000');
//define('CSS_BACKGROUND_MENU', '#fff');
//define('CSS_BACKGROUND_SEARCH', '#fff');
//define('CSS_BACKGROUND_SEARCH_BUTTON', '#F15A25');
//define('CSS_BACKGROUND_RECOMMEND_HEADER', '#F15A25');
//define('CSS_BACKGROUND_PING', '#F15A25');
//define('CSS_BACKGROUND_NAV_ITEM', '#F15A25');
//
//define('CSS_COLOR_MENU', '#000');
//define('CSS_COLOR_SEARCH', '#000');
//define('CSS_COLOR_HEADER', '#ffffff');
//define('CSS_COLOR_RECOMMEND_HEADER', '#ffffff');
//define('CSS_COLOR_DETAIL_A', '#288ad6');
//define('CSS_COLOR_VIEW_ALL', '#295d23');
//define('CSS_LOGO', 'images/logo.svg');
//define('CSS_QUICK_LINK_A', '#288ad6');
//define('CSS_COLOR_NAV_ITEM', '#288ad6');
//
//define('CSS_BODER_PING', '#f15a25');
//define('CSS_BODER_RECOMMEND_HEADER', '#f15a25');

define('SCHEMA_BLOG', 'bloglachuoi');
define('SCHEMA_ORGANIZATION_NAME', 'Hnammobile | Hệ thống bán lẻ điện thoại chính hãng giá rẻ');
define('CSS_BACKGROUND_BODY', '#e1e1e1');
define('CSS_BACKGROUND_IMAGE_BODY', '');
define('CSS_BACKGROUND_HEADER', '#fff');
define('CSS_BACKGROUND_MENU', '#295d23');
define('CSS_BACKGROUND_SEARCH', '#fff');
define('CSS_BACKGROUND_SEARCH_BUTTON', '#295d23');
define('CSS_BACKGROUND_RECOMMEND_HEADER', '#295d23');
define('CSS_BACKGROUND_PING', '#f15a25');
define('CSS_BACKGROUND_NAV_ITEM', '#fff'); //hover
define('CSS_BACKGROUND_CAT_NAV_MOBILE', '#333'); //hover
define('CSS_BACKGROUND_CAT_NAV_MOBILE_DIV', '#fff');// menu mobile hiện ra
define('CSS_BACKGROUND_SEARCH_BOTTOM', '#295d23'); //hover
define('CSS_BACKGROUND_COMMENT_BUTTON', '#295d23'); //hover

define('CSS_COLOR_MENU', '#000');
define('CSS_COLOR_SEARCH', '#000');
define('CSS_COLOR_HEADER', '#333');
define('CSS_COLOR_RECOMMEND_HEADER', '#ffffff');
define('CSS_COLOR_DETAIL_A', '#288ad6');
define('CSS_COLOR_VIEW_ALL', '#295d23');
define('CSS_COLOR_VIEW_MORE', '#295d23');
define('CSS_LOGO', 'images/logo-blog.png');
define('CSS_LOGO_MACTH', 'images/logo-blog-match.png');
define('CSS_QUICK_LINK_A', '#288ad6');
define('CSS_COLOR_HOVER_NAV_ITEM', '#fff');// hover
define('CSS_COLOR_NAV_ITEM', '#fff');// hover
define('CSS_COLOR_COMMENT_BUTTON', '#fff');// hover

define('CSS_BODER_PING', '#f15a25');
define('CSS_BODER_RECOMMEND_HEADER', '#295d23');

define('CSS_BACKGROUND_IMAGE_MATCH_R', '0');
define('CSS_BACKGROUND_IMAGE_MATCH_G', '0');
define('CSS_BACKGROUND_IMAGE_MATCH_B', '0');
define('CSS_COLOR_IMAGE_MATCH_R', '255');
define('CSS_COLOR_IMAGE_MATCH_G', '255');
define('CSS_COLOR_IMAGE_MATCH_B', '255');

//end define css
date_default_timezone_set('Asia/Bangkok');
if(!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', dirname(__FILE__));
}
if(!defined('APP_ENV')) {
    define('APP_ENV', 'development');
}

require_once "Zend/Loader.php";

Zend_Loader::registerAutoload();

ProfilerLog::startLog('loadcfg');

$configuration = new Zend_Config_Ini(APPLICATION_PATH . '/config/' . APP_ENV . '.global.ini',APP_ENV);
ProfilerLog::endLog('loadcfg');

Zend_Registry::set('configuration', $configuration);

if (APP_ENV=='production')
{
	define('IMAGE_URL', '/images/');
	define('UPLOAD_URL', '/uploads/');
	define('BASE_URL', $configuration->baseurl);
}
elseif (APP_ENV=='staging')
{
	define('IMAGE_URL', '/images/');
	define('UPLOAD_URL', '/uploads/');
	define('BASE_URL', $configuration->baseurl);
}
elseif (APP_ENV=='development')
{
	define('IMAGE_URL', '/images/');
	define('UPLOAD_URL', '/uploads/');
	define('BASE_URL', $configuration->baseurl);
}

$lang = Language_Language::getInstance()->getLang();
require_once APPLICATION_PATH . '/languages/language-' . $lang .'.php' ;

Zend_Registry::set('lang', $__language);

$frontController = Zend_Controller_Front::getInstance();
$frontController->setParam('env', APP_ENV);




$frontController->addControllerDirectory(APPLICATION_PATH . '/modules/admin/controllers', 'admin');
$frontController->addControllerDirectory(APPLICATION_PATH . '/modules/news/controllers', 'news');
$frontController->addControllerDirectory(APPLICATION_PATH . '/modules/tts/controllers', 'tts');


/*News*/

$addon_ajax_route = new Zend_Controller_Router_Route(
    '/ajax/:action/*',
    array('controller' => 'ajax', 'action' => 'index', 'module' => 'news')
);

// /*News*/

// $addon_ajax_route_new = new Zend_Controller_Router_Route(
//     '/ajax/:action/*',
//     array('controller' => 'ajax', 'action' => 'index', 'module' => 'news')
// );

$router = $frontController->getRouter();

// phan fontend


$indexHome = new Zend_Controller_Router_Route_Regex(
    '',
    array(
        'controller' => 'index',
        'action' => 'index',
        'module' => 'news'
    )
);
$router->addRoute('indexHome',$indexHome);



$tracking = new Zend_Controller_Router_Route_Regex(
    'tracking',
    array(
        'controller' => 'tracking',
        'action' => 'tracking',
        'module' => 'news'
    )
);
$router->addRoute('tracking',$tracking);

$suggestion = new Zend_Controller_Router_Route_Regex(
    'suggestion',
    array(
        'controller' => 'suggestion',
        'action' => 'suggestion',
        'module' => 'news'
    )
);
$router->addRoute('suggestion',$suggestion);

$suggestionforblog = new Zend_Controller_Router_Route_Regex(
    'suggestionforblog',
    array(
        'controller' => 'suggestionforblog',
        'action' => 'suggestionforblog',
        'module' => 'news'
    )
);
$router->addRoute('suggestionforblog',$suggestionforblog);

$trackingblog = new Zend_Controller_Router_Route_Regex(
    'suggestionforblog/trackingblog',
    array(
        'controller' => 'suggestionforblog',
        'action' => 'trackingblog',
        'module' => 'news'
    )
);
$router->addRoute('trackingblog',$trackingblog);

//trackingcart
$trackingcart = new Zend_Controller_Router_Route_Regex(
    'suggestionforblog/trackingcart',
    array(
        'controller' => 'suggestionforblog',
        'action' => 'trackingcart',
        'module' => 'news'
    )
);
$router->addRoute('trackingcart',$trackingcart);

//trackingtransaction
$trackingtransaction = new Zend_Controller_Router_Route_Regex(
    'suggestionforblog/trackingtransaction',
    array(
        'controller' => 'suggestionforblog',
        'action' => 'trackingtransaction',
        'module' => 'news'
    )
);
$router->addRoute('trackingtransaction',$trackingtransaction);


$gettopurlanditemid = new Zend_Controller_Router_Route_Regex(
    'suggestionforblog/gettopurlanditemid',
    array(
        'controller' => 'suggestionforblog',
        'action' => 'gettopurlanditemid',
        'module' => 'news'
    )
);
$router->addRoute('gettopurlanditemid',$gettopurlanditemid);
//getTopUrlAndItemid
// end phan fontend

//Route admin

$admin_route = new Zend_Controller_Router_Route('admin/:controller/:action/*',
    array('controller' => 'home', 'action' => 'index', 'module' => 'admin'));

$router->addRoute('admin',$admin_route);

$router->addRoute('ajax',$addon_ajax_route);

// end route admin




$frontController->setRouter($router);

// check var
// check vardumb($a);
$frontController->throwExceptions(false);
$frontController->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
    'module'     => 'news',
    'controller' => 'error',
    'action'     => 'index'
)));

require_once APPLICATION_PATH . '/plugins/BlockManagement/BlockManagementPlugin.php';
$frontController->registerPlugin(new BlockManagementPlugin(array()));
require_once APPLICATION_PATH . '/plugins/SEOPlugin.php';
$frontController->registerPlugin(new SEOPlugin());
require_once APPLICATION_PATH . '/plugins/ProfilerPlugin.php';
$frontController->registerPlugin(new ProfilerPlugin());
unset($frontController, $config, $global_config, $dbAdapter, $logger, $router);