<?php
$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];
define('BASE_PATH', realpath(dirname(__FILE__)));//public folder
define('BASE_PATH_V3', realpath(dirname(__FILE__)));//public folder
define('STATIC_BASE_PATH', BASE_PATH);//public folder
define('ROOT_PATH', realpath(dirname(__FILE__).'/../'));//base folder

if(isset($_SERVER["APP_ENV"]))
{
	define('APP_ENV',$_SERVER["APP_ENV"]);
}
else
{
	define('APP_ENV','production');
}

define("keys", "abc@!123");
define("alias_domain", "vthnam.");
define("shop_news", "shop");
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application/'));
define('ZENDLIB_PATH', ROOT_PATH.'/Zend/');

define('MODULES_PATH', ROOT_PATH.'/application/modules/');
define('ZENDLIBRARY_PATH', ROOT_PATH.'/Zend_lib');
define('BUSINESS_PATH', ROOT_PATH.'/Business/');
set_include_path(MODULES_PATH . PATH_SEPARATOR . get_include_path());
set_include_path(ZENDLIB_PATH . PATH_SEPARATOR . get_include_path());
set_include_path(ZENDLIBRARY_PATH . PATH_SEPARATOR . get_include_path());
set_include_path(BUSINESS_PATH . PATH_SEPARATOR . get_include_path());
define("site_key", "6LczPUAUAAAAAKXQYXc6Q1qr2WEVTCTZYj595Yjz");
define("secret_key", "6LczPUAUAAAAANwVEF50dx_SJ1fZjLM9oH8BNR91");
define("aks", "f979efaf44b5f200a06530ce77fbf53d");
//    ini_set('display_errors', '0');
//ini_set('display_errors', '0');

error_reporting(E_ALL);
ini_set("display_errors", 1);

try
{
	require '../application/bootstrap.php';
}
catch (Exception $exception)
{
	echo '<html><body><center>'
		. 'An exception occured while bootstrapping the application.';
	if (defined('APP_ENV')
		&& APP_ENV != 'production'
	) {
		echo '<br /><br />' . $exception->getMessage() . '<br />'
			. '<div align="left">Stack Trace:'
			. '<pre>' . $exception->getTraceAsString() . '</pre></div>';
	}
	echo '</center></body></html>';
	exit(1);
}

// vardum
	
Zend_Controller_Front::getInstance()->dispatch();
	
