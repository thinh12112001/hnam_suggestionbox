<?php

class Business_Addon_RegisterEmail extends Business_Abstract
{
    private $_tablename = 'addon_register_email';
    private $_prefix_cache = 'Business_Addon_RegisterEmail::';
    private static $_instance = null;

    function __construct(){
    }

    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_RegisterEmail();
        }
        return self::$_instance;
    }

    function getDbConnection() {
        $db = Globals::getDbConnection('maindb', false);
        return $db;
    }

    public function insert($data) {
        $db = $this->getDbConnection();
        $result = $db->insert($this->_tablename,$data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($this->_tablename);
        }
        return $lastid;
    }

    public function checkRegisted($email,$type) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = $this->_prefix_cache . "checkRegisted-{$email}-{$type}";
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }

        $db = $this->getDbConnection();
        $query = "SELECT id FROM {$this->_tablename} WHERE email = '{$email}' AND type = {$type}";
        $result = $db->fetchRow($query);
        $cache->setCache($cacheKey,$result);
        return $result;
    }
}