<?php

class Business_Addon_Cate_product extends Business_Abstract
{
    private $_tablename = 'addon_cate_product';
    private $_prefix_cache = 'Business_Addon_Cate_Product::';
    private static $_instance = null;
    private $_general = null;

    function __construct() {
        $this->_general = Business_Addon_General::getInstance();
    }


    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_Cate_Product();
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

    public function getList($parentId = 0,$limit=0,$enabled=false) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getList-{$parentId}-{$limit}-{$enabled}");
        $result = $cache->getCache($cacheKey);
        if ($result) {
            
            return $result;
        }
        //var_dump($result);
        $db = $this->getDbConnection();
        $query = "SELECT * FROM {$this->_tablename} WHERE parent_id in ({$parentId})  ";
        if ($enabled){
            $query .= " and enabled = ".$enabled;
        }

        if ($limit){
            $query .= " LIMIT 0,".$limit;
        }
        $result = $db->fetchAll($query);
        $cache->setCache($cacheKey,$result);
        return $result;
    }

    public function getListCateProductGroup($type,$enabled = false) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = $this->_prefix_cache . "getListCateProductGroup-{$type}";
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }

        $db = $this->getDbConnection();
        $query = "SELECT id,title FROM {$this->_tablename} WHERE group_cate_product = {$type}";
        if($enabled !== false){
            $query .=  " and enabled = {$enabled}";
        }
        $result = $db->fetchAll($query);
        $cache->setCache($cacheKey,$result);
        return $result;
    }
    
}
