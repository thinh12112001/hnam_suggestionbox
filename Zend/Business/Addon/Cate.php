<?php

class Business_Addon_Cate extends Business_Abstract
{
    private $_tablename = 'addon_cate';
    private $_prefix_cache = 'Business_Addon_Cate::';
    private static $_instance = null;
    private $_general = null;

    function __construct() {
        $this->_general = Business_Addon_General::getInstance();
    }


    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_Cate();
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

    public function getCateByParentId($parentId,$group) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = $this->_prefix_cache . "getCateByParentId-{$parentId}-{$group}";
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }

        $db = $this->getDbConnection();
        $query = "SELECT id,title,slug FROM {$this->_tablename} WHERE group_cate = {$group} and parent_id = $parentId and enabled = 1";
        $result = $db->fetchAll($query);
        $cache->setCache($cacheKey,$result);
        return $result;
    }
    public function getCateNews($parentId,$group) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = $this->_prefix_cache . "getCateNews-{$parentId}-{$group}";
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }

        $db = $this->getDbConnection();
        $query = "SELECT id,title,slug FROM {$this->_tablename} WHERE group_cate = {$group} and parent_id = $parentId and enabled = 1 and id != 4";
        $result = $db->fetchAll($query);
        $cache->setCache($cacheKey,$result);
        return $result;
    }

    public function getListCateGroup($type,$enabled = false) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = $this->_prefix_cache . "getListCateGroup-{$type}";
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }

        $db = $this->getDbConnection();
        $query = "SELECT id,title FROM {$this->_tablename} WHERE group_cate = {$type}";
        if($enabled !== false){
            $query .=  " and enabled = {$enabled}";
        }
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

    public function findAllById($id) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "findAllById-{$id}");
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

    public function getDetail($id){
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = $this->_prefix_cache . "getDetail-{$id}";
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


    public function getSubCategories($id,$enabled='',$link='') {
        $cache = GlobalCache::getCacheInstance('ws');
        $key = md5("{$this->_prefix_cache}-getSubCategories-{$id}-{$enabled}");
        $cacheKey = md5($key);
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }
        $where = "AND parent_id = {$id}";
        if ($enabled!=='') {
            $where .= " AND enabled = {$enabled}";
        }
        $order_by = 'ORDER BY myorder ASC,title ASC';

        $query = "SELECT * FROM {$this->_tablename} WHERE (1) {$where} {$order_by}";
        $result = $this->_general->excuteCodev2($query);
        if ($result) {
            $base_url = Globals::getBaseUrl();
            if (!$link) {
                $newLink = '';
                $parent_id = $result[0]['parent_id'];
                while ( $parent_id != 0) {
                    $query = "SELECT * FROM {$this->_tablename} WHERE id = {$parent_id} ORDER BY myorder ASC, title ASC";
                    if ($enabled!=='') {
                        $query .= " AND enabled = {$enabled}";
                    }
                    $newResult = $this->_general->excuteCodev2($query);
                    $parent_id = $newResult[0]['parent_id'];
                    if ($newLink) {
                        $newLink = "{$newResult[0]['slug']}/{$newLink}";
                    }
                    else {
                        $newLink = "{$newResult[0]['slug']}";
                    }
                }
                if ($newLink) {
                    $base_url.= "/$newLink";
                }
            }
            else {
                $base_url = $link;
            }

            $newResult = array();
            foreach ($result as $key => $item) {
                $item['link'] = "{$base_url}/{$item['slug']}";
                $newResult[$item['slug']] = $item;
            }
            $result = $newResult;
        }
        $cache->setCache($cacheKey, $result);
        return $result;
    }
    public function getAllCategories($enabled='',$group_cate = 1) {
        $cache = GlobalCache::getCacheInstance('ws');
        $key = md5("{$this->_prefix_cache}-getAllCategories-{$enabled}-{$group_cate}");
        $cacheKey = md5($key);
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }
        $where = ' AND parent_id = 0 and group_cate = '.$group_cate;
        if ($enabled!=='') {
            $where .= " AND enabled = {$enabled}";
        }
        $query = "SELECT * FROM {$this->_tablename} WHERE (1) {$where}";
        $result = $this->_general->excuteCodev2($query);
        if ($result) {
            $base_url = Globals::getBaseUrl();
            $newResult = array();
            foreach ($result as $key => $item) {
                $item['level'] = 1;
                $item['link'] = "{$base_url}/{$item['slug']}";
                $item['menu_title'] = $item['title'];
                $item['full_title'] = $item['title'];
                $item['full_raw_title'] = $item['title'];
                $newResult[$item['id']] = $item;
                $sub1 = $this->getSubCategories($item['id'],$enabled,$item['link']);
                if ($sub1) {
                    foreach ($sub1 as $skey1 => $newsub1) {
                        $newsub1['level'] = 2;
                        $newsub1['link'] = "{$item['link']}/{$newsub1['slug']}";
                        $newsub1['menu_title'] = "\_ {$newsub1['title']}";
                        $newsub1['full_title'] = "{$item['full_title']} <i class='fa fa-angle-double-right' aria-hidden='true'></i> {$newsub1['title']}";
                        $newsub1['full_raw_title'] = "{$item['full_raw_title']} > {$newsub1['title']}";
                        $newResult[$newsub1['id']] = $newsub1;
                        $sub2 = $this->getSubCategories($newsub1['id'],$enabled,$newsub1['link']);
                        if ($sub2) {
                            foreach ($sub2 as $skey2 => $newsub2) {
                                $newsub2['level'] = 3;
                                $newsub2['link'] = "{$newsub1['link']}/{$newsub2['slug']}";
                                $newsub2['menu_title'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\_ {$newsub2['title']}";
                                $newsub2['full_title'] = "{$newsub1['full_title']} <i class='fa fa-angle-double-right' aria-hidden='true'></i> {$newsub2['title']}";
                                $newsub2['full_raw_title'] = "{$newsub1['full_raw_title']} > {$newsub2['title']}";
                                $newResult[$newsub2['id']] = $newsub2;
                                $sub3 = $this->getSubCategories($newsub2['id'],$enabled,$newsub2['link']);
                                if ($sub3) {
                                    foreach ($sub3 as $skey3 => $newsub3) {
                                        $newsub3['level'] = 4;
                                        $newsub3['link'] = "{$newsub2['link']}/{$newsub3['slug']}";
                                        $newsub3['menu_title'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\_ {$newsub3['title']}";
                                        $newsub3['full_title'] = "{$newsub2['full_title']} <i class='fa fa-angle-double-right' aria-hidden='true'></i> {$newsub3['title']}";
                                        $newsub3['full_raw_title'] = "{$newsub2['full_raw_title']} > {$newsub3['title']}";
                                        $newResult[$newsub3['id']] = $newsub3;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $result = $newResult;
        }
        $cache->setCache($cacheKey, $result);
        return $result;
    }
    public function getCategoryByID($id,$enabled="",$group_type=1) {
        $cache = GlobalCache::getCacheInstance('ws');
        $key = md5("{$this->_prefix_cache}-getCategoryByID-{$id}-{$group_type}");
        $cacheKey = md5($key);
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }
        $categories = $this->getAllCategories($enabled,$group_type);
        $result = isset($categories[$id])?$categories[$id]:false;
        $cache->setCache($cacheKey, $result);
        return $result;
    }

    public function getAllSubCategories($id,$enabled='',$group_type=1) {
        $cache = GlobalCache::getCacheInstance('ws');
        $key = md5("{$this->_prefix_cache}-getAllSubCategories-{$id}-{$enabled}");
        $cacheKey = md5($key);
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }
        $result = $this->getCategoryByID($id,$enabled,$group_type);
        if ($result) {
            $result = array($result);
            $base_url = Globals::getBaseUrl();
            $newResult = array();
            foreach ($result as $key => $item) {
                $item['link'] = "{$base_url}/{$item['slug']}";
                $item['menu_title'] = $item['title'];
                $newResult[$item['id']] = $item;
                $sub1 = $this->getSubCategories($item['id'],$enabled,$item['link']);
                if ($sub1) {
                    foreach ($sub1 as $skey1 => $newsub1) {
                        $newsub1['link'] = "{$item['link']}/{$newsub1['slug']}";
                        $newsub1['menu_title'] = "\_ {$newsub1['title']}";
                        $newResult[$newsub1['id']] = $newsub1;
                        $sub2 = $this->getSubCategories($newsub1['id'],$enabled,$newsub1['link']);
                        if ($sub2) {
                            foreach ($sub2 as $skey2 => $newsub2) {
                                $newsub2['link'] = "{$newsub1['link']}/{$newsub2['slug']}";
                                $newsub2['menu_title'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\_ {$newsub2['title']}";
                                $newResult[$newsub2['id']] = $newsub2;
                                $sub3 = $this->getSubCategories($newsub2['id'],$enabled,$newsub2['link']);
                                if ($sub3) {
                                    foreach ($sub3 as $skey3 => $newsub3) {
                                        $newsub3['link'] = "{$newsub2['link']}/{$newsub3['slug']}";
                                        $newsub3['menu_title'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\_ {$newsub3['title']}";
                                        $newResult[$newsub3['id']] = $newsub3;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $result = $newResult;
        }
        $cache->setCache($cacheKey, $result);
        return $result;
    }
}