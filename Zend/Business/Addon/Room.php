<?php

class Business_Addon_Room extends Business_Abstract
{
    private $_tablename = 'addon_cate';
    private $_prefix_cache = 'Business_Addon_Room::';
    private static $_instance = null;

    function __construct(){
    }

    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_Room();
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


    

    public function getRoomDetail($id, $group_cate =1){
        $cache = GlobalCache::getCacheInstance('ws');
        // var_dump($cache);
        $cacheKey = md5($this->_prefix_cache . "getRoomDetail-{$id}-{$group_cate}");
        $result = $cache->getCache($cacheKey);
        // var_dump($result);
        if ($result) {
            return $result;
        }
        
        $db = $this->getDbConnection();
        $query = "SELECT * FROM {$this->_tablename} WHERE id = {$id} AND group_cate = {$group_cate}";
        $result = $db->fetchRow($query);
        $cache->setCache($cacheKey,$result);
        return $result;
        
    }
}