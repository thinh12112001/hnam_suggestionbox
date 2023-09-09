<?php

class Business_Addon_Banner extends Business_Abstract
{
    private $_tablename = 'addon_banner';
    private $_prefix_cache = 'Business_Addon_Banner::';
    private static $_instance = null;

    function __construct(){
    }

    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_Banner();
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

    public function getList($type,$enabled = false) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getList-{$type}");
        $result = $cache->getCache($cacheKey);
        
        if ($result) {
            return $result;
        }

        $db = $this->getDbConnection();
        $query = "SELECT * FROM {$this->_tablename} WHERE group_type = {$type}";
        if($enabled !== false){
            $query .=  " and enabled = {$enabled}";
        }
        $query .= " order by stt ASC, id DESC";
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
}