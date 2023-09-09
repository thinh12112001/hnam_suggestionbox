<?php

class Business_Addon_News extends Business_Abstract
{
    private $_tablename = 'addon_news';
    private $_prefix_cache = 'Business_Addon_News::';
    private static $_instance = null;

    function __construct(){
    }

    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_News();
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
    public function getListNew($selects="*",$parentId = 0,$limit=0,$enabled=false) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getListNew-{$parentId}-{$limit}-{$enabled}");
        $result = $cache->getCache($cacheKey);

        
        // if ($result) {
            
        //     return $result;

        // }

        $db = $this->getDbConnection();
        $query = "SELECT {$selects} FROM {$this->_tablename} WHERE parent_id in ({$parentId})  ";
        if ($enabled){
            $query .= " and enabled = ".$enabled;
        }
        $query .= " ORDER BY id DESC";
        if ($limit){
            $query .= " LIMIT 0,".$limit;
        }
        $result = $db->fetchAll($query);
        $cache->setCache($cacheKey,$result);
        return $result;
    }

    public function insertData($id, $browser, $ip, $urlLink){
        $db = $this->getDbConnection();
        $query = "INSERT INTO 'addon_user_trip' (id, browser, ip, urlLink) VALUES ({$id}, '{$browser}', '{$ip}', '{$urlLink}')";
        $result = $db->query($query);


        if ($result !== false && $result->rowCount() > 0) {
            // Truy vấn UPDATE thành công
            return true;
        } else {
            // Truy vấn UPDATE thất bại
            return false;
        }

        
    }

    public function getListNewByTitle($selects="*",$parentId = 0,$limit=0,$enabled=false,$searchInput) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getListNewByTitle-{$parentId}-{$limit}-{$enabled}-{$searchInput}");
        $result = $cache->getCache($cacheKey);

        $db = $this->getDbConnection();
        $query = "SELECT {$selects} FROM {$this->_tablename} WHERE parent_id IN ({$parentId}) AND title LIKE '%{$searchInput}%';";


        if ($enabled){
            $query .= " and enabled = ".$enabled;
        }
        $query .= " ORDER BY id DESC";
        if ($limit){
            $query .= " LIMIT 0,".$limit;
        }
        $result = $db->fetchAll($query);
        $cache->setCache($cacheKey,$result);
        return $result;
    }

    public function getListRelated($selects="*",$parentId = 0,$id=0,$limit=0,$enabled=false) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getListRelated-{$parentId}-{$limit}-{$enabled}-{$id}");
        $result = $cache->getCache($cacheKey);
        // var_dump($result);
        if ($result) {
            return $result;
        }

        $db = $this->getDbConnection();
        $query = "SELECT {$selects} FROM {$this->_tablename} WHERE parent_id in ({$parentId}) and id != $id ";
        if ($enabled){
            $query .= " and enabled = ".$enabled;
        }
        $query .= " ORDER BY id DESC";
        if ($limit){
            $query .= " LIMIT 0,".$limit;
        }
        $result = $db->fetchAll($query);
        $cache->setCache($cacheKey,$result);
        return $result;
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
}