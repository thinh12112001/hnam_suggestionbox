<?php

class Business_Addon_Booking extends Business_Abstract
{
    private $_tablename = 'addon_booking';
    private $_prefix_cache = 'Business_Addon_Booking::';
    private static $_instance = null;
    private $_general = null;

    function __construct() {
        $this->_general = Business_Addon_General::getInstance();
    }


    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_Booking();
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

    public function getDetail($id){
        $cache = GlobalCache::getCacheInstance('ws');
        // var_dump($cache);
        $cacheKey = md5($this->_prefix_cache . "getDetail-{$id}");
        $result = $cache->getCache($cacheKey);
        // var_dump($result);
        if ($result) {
            
            return $result;
        }
        
        $db = $this->getDbConnection();
        $query = "SELECT * FROM {$this->_tablename} WHERE id = {$id}";
        $result = $db->fetchRow($query);
        $cache->setCache($cacheKey,$result);
        return $result;
        
    }

    public function getAll() {
        
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getAll-{$id}");
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;

        }

        $db = $this->getDbConnection();
        $query = "SELECT * FROM {$this->_tablename}";

        $result = $db->fetchAll($query);
        $cache->setCache($cacheKey,$result);
        return $result;

    }

    public function getListById($id) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getListById-{$id}");
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }

        $db = $this->getDbConnection();
        $query = "SELECT * FROM {$this->_tablename} WHERE id in ({$id})";

        $result = $db->fetchAll($query);
        $cache->setCache($cacheKey,$result);
        return $result;
    }
    
}
