<?php

class Business_Addon_UserTrip extends Business_Abstract
{
    private $_tablename = 'addon_user_trip';
    private $_prefix_cache = 'Business_Addon_UserTrip::';
    private static $_instance = null;
    private $_general = null;

    function __construct() {
        $this->_general = Business_Addon_General::getInstance();
    }


    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_UserTrip();
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

    public function updateData($id, $browser, $ip, $urlLink){
        $db = $this->getDbConnection();
        $query = "UPDATE {$this->_tablename} SET browser = '{$browser}', ip = '{$ip}', urlLink = '{$urlLink}' WHERE id = {$id}";
        $result = $db->query($query);

        if ($result !== false && $result->rowCount() > 0) {
            // Truy vấn UPDATE thành công
            return true;
        } else {
            // Truy vấn UPDATE thất bại
            return false;
        }

        
    }

    public function insertDb($id, $browser, $ip, $urlLink){
        $db = $this->getDbConnection();
        $query = "INSERT INTO {$this->_tablename} (id, browser, ip, urlLink) VALUES ({$id}, '{$browser}', '{$ip}', '{$urlLink}')";
        $result = $db->query($query);


        if ($result !== false && $result->rowCount() > 0) {
            // Truy vấn UPDATE thành công
            return true;
        } else {
            // Truy vấn UPDATE thất bại
            return false;
        }

        
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
