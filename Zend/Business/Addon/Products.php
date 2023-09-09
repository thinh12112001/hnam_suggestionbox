<?php

class Business_Addon_Products extends Business_Abstract
{
    private $_tablename = 'addon_products';
    private $_prefix_cache = 'Business_Addon_Products::';
    private static $_instance = null;

    function __construct(){
    }

    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_Products();
        }
        return self::$_instance;
    }

    function getDbConnection() {
        $db = Globals::getDbConnection('maindb', false);
        return $db;
    }

    public function getList($parentId = 0) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getList-{$parentId}");
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }

        $db = $this->getDbConnection();
        $query = "SELECT * FROM {$this->_tablename} WHERE parent_id = {$parentId}";

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
        $query = "SELECT id,title FROM {$this->_tablename} WHERE id in ({$id})";

        $result = $db->fetchAll($query);
        $cache->setCache($cacheKey,$result);
        return $result;
    }

    public function getListForBooking() {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getListForBooking");
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

    public function getDetail($id){
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getDetail-{$id}");
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }

        $db = $this->getDbConnection();
        $query = "SELECT * FROM {$this->_tablename} WHERE id = {$id}";
        $result = $db->fetchRow($query);
        $cache->setCache($cacheKey,$result);
        return $result;
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