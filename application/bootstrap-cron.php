<?php
date_default_timezone_set('Asia/Bangkok');
ini_set("soap.wsdl_cache_enabled", "0");
ini_set('magic_quotes_gpc', 'off');
ini_set('magic_quotes_runtime', 'off');
if(!defined('APPLICATION_PATH'))
{
    define('APPLICATION_PATH', dirname(__FILE__));
}
require_once "Zend/Loader.php";

Zend_Loader::registerAutoload();
ProfilerLog::startLog('loadcfg');
$configuration = new Zend_Config_Ini(APPLICATION_PATH . '/config/' . APP_ENV . '.global.ini',APP_ENV);
ProfilerLog::endLog('loadcfg');
Zend_Registry::set('configuration', $configuration);

$frontController = Zend_Controller_Front::getInstance();
$frontController->setParam('env', APP_ENV);
require_once APPLICATION_PATH . '/plugins/BlockManagement/BlockManagementPlugin.php';
$frontController->registerPlugin(new BlockManagementPlugin(array()));