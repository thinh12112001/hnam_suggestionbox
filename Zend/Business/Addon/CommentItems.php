<?php

class Business_Addon_CommentItems extends Business_Abstract
{

    private $_tablename = 'addon_comment_items';
    private $_prefix_cache = 'Business_Addon_CommentItems::';

    private static $_instance = null;

    function __construct()
    {}
    
    // public static function
    /**
     * get instance of Business_Addon_CommentItems
     *
     * @return Business_Addon_CommentItems
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Get DB Connection
     *
     * @return Zend_Db_Adapter_Abstract
     */
    private function getDbConnection()
    {
        $db = Globals::getDbConnection('maindb');
        return $db;
    }

    private function getCacheInstance()
    {
        $cache = GlobalCache::getCacheInstance('ws');
        return $cache;
    }
    public function getListPagingByItemidTime1($commentid = 2, $itemid = '', $offset = '', $records = '', $published = '', $parentid = '',$start='',$end='')
    {
        $cache = $this->getCacheInstance();
        $__key = md5($commentid . $itemid . $offset . $records . $published . $parentid.$start.$start . "listtime1");
    
        if($start!='' and $end=='')
        {
            $where = " and datetime>'$start'";
        }else
        {
    
            $where = " and datetime>'$start'   and  datetime<'$end' ";
    
        }
        $result = $cache->getCache($__key);
      //  $result = false;
        if ($result === false) {
            $db = $this->getDbConnection();
            if ($itemid !== '') {
                $_itemid = " AND itemid = " . (int) $itemid;
            }
            if ($parentid !== '') {
                       $_parentid = " AND parentid in ( $parentid )";
            }
            if ($parentid === 'sub') { // get all sub comments
                $_parentid = " AND parentid != 0 ";
            }
            if ($published === '')
                $query = "SELECT count(itemid) as total FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid $where ORDER BY datetime desc";
            else
                $query = "SELECT count(itemid) as total FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid AND display = $published ORDER BY datetime desc";
    
            if ($offset !== '' && $records != '') {
                $query .= " LIMIT $offset, $records";
            }

            $data = array(
                $itemid
            );
           // echo $query; exit;
            $result = $db->fetchAll($query, $data);
            $cache->setCache($__key, $result[0]["total"], 120);
        }
        return $result[0]["total"];
    }
    
    
    public function getListPagingByItemidTime($commentid = 2, $itemid = '', $offset = '', $records = '', $published = '', $parentid = '',$start='',$end='')
    {
        $cache = $this->getCacheInstance();
        $__key = md5($commentid . $itemid . $offset . $records . $published . $parentid.$start.$start . "listtime");
        
            if($start!='' and $end=='')
        {
            $where = " and datetime>'$start'";
            $where .= " or lastupdate>'$start'";
        }else 
        {
            
            $where = " and ( datetime>'$start'   and  datetime<'$end' ) ";

            $where .= "  or ( lastupdate>'$start'   and  lastupdate<'$end' ) ";

            
        }
        $result = $cache->getCache($__key);
        if ($result === false) {
            $db = $this->getDbConnection();
            if ($itemid !== '') {
                $_itemid = " AND itemid = " . (int) $itemid;
            }
            if ($parentid !== '') {
                       $_parentid = " AND parentid in ( $parentid )";
            }
            if ($parentid === 'sub') { // get all sub comments
                $_parentid = " AND parentid != 0 ";
            }
            if ($published === '')
                $query = "SELECT * FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid $where ORDER BY lastupdate desc ,datetime desc";
            else
                $query = "SELECT * FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid AND display = $published ORDER BY lastupdate desc , datetime desc";
    
            if ($offset !== '' && $records != '') {
                $query .= " LIMIT $offset, $records";
            }
            if ($parentid === '') 
                   $query .= " LIMIT 0, 100";
            $data = array(
                $itemid
            );
            $result = $db->fetchAll($query, $data);
            $cache->setCache($__key, $result, 120);
        }
        return $result;
    }
    public function getListPagingByItemidNo1($commentid = 2, $itemid = '', $offset = '', $records = '', $published = '', $parentid = '')
    {
        $cache = $this->getCacheInstance();
        $__key = md5($commentid . $itemid . $offset . $records . $published . $parentid . "listNo1");
        $result = $cache->getCache($__key);
       // $result = false;
        if ($result === false) {
            $db = $this->getDbConnection();
            if ($itemid !== '') {
                $_itemid = " AND itemid = " . (int) $itemid;
            }
            if ($parentid !== '') {
                       $_parentid = " AND parentid in ( $parentid )";
            }
            if ($parentid === 'sub') { // get all sub comments
                $_parentid = " AND parentid != 0 ";
            }
            if ($published === '')
                $query = "SELECT count(itemid) as total  FROM " . $this->_tablename . " WHERE id NOT IN ( SELECT parentid FROM " . $this->_tablename ."  WHERE commentid=$commentid $_itemid AND parentid != 0  ) AND commentid=$commentid $_itemid $_parentid  OR (display = 0  AND  commentid=$commentid   AND parentid = 0  ) ORDER BY datetime desc";
            else
                $query = "SELECT count(itemid) as total  FROM " . $this->_tablename . " as c WHERE commentid=$commentid $_itemid $_parentid AND display = $published ORDER BY datetime desc";
    
            if ($offset !== '' && $records != '') {
                $query .= " LIMIT $offset, $records";
            }
            if ($parentid === '') 
                   $query .= " LIMIT 0, 100";
            $data = array(
                $itemid
            );
    
            $result = $db->fetchAll($query, $data);
            $cache->setCache($__key,  $result[0]["total"], 120);
        }
        return  $result[0]["total"];
    }
    
    public function getListPagingByItemidNo($commentid = 2, $itemid = '', $offset = '', $records = '', $published = '', $parentid = '')
    {
        $cache = $this->getCacheInstance();
        $__key = md5($commentid . $itemid . $offset . $records . $published . $parentid . "listNo");
        $result = $cache->getCache($__key);
        $result = false;
        if ($result === false) {
            $db = $this->getDbConnection();
            if ($itemid !== '') {
                $_itemid = " AND itemid = " . (int) $itemid;
            }
            if ($parentid !== '') {
                       $_parentid = " AND parentid in ( $parentid )";
            }
            if ($parentid === 'sub') { // get all sub comments
                $_parentid = " AND parentid != 0 ";
            }
            if ($published === '')
                $query = "SELECT *  FROM " . $this->_tablename . " WHERE id NOT IN ( SELECT parentid FROM " . $this->_tablename ."  WHERE commentid=$commentid $_itemid AND parentid != 0  ) AND commentid=$commentid $_itemid $_parentid  AND is_review = 0 OR (display = 0  AND  commentid=$commentid   AND parentid = 0  ) ORDER BY datetime desc";
            else
                $query = "SELECT *  FROM " . $this->_tablename . " as c WHERE commentid=$commentid $_itemid $_parentid AND display = $published AND is_review = 0 ORDER BY datetime desc";
    
            if ($offset !== '' && $records != '') {
                $query .= " LIMIT $offset, $records";
            }
            if ($parentid === '') 
                   $query .= " LIMIT 0, 100";
            $data = array(
                $itemid
            );

            $result = $db->fetchAll($query, $data);
            $cache->setCache($__key,  $result, 120);
        }
        return  $result;
    }
    
    public function getListPagingByItemid1($commentid = 2, $itemid = '', $offset = '', $records = '', $published = '', $parentid = '')
    {
        $cache = $this->getCacheInstance();
        $__key = md5($commentid . $itemid . $offset . $records . $published . $parentid . "list1");
        $result = $cache->getCache($__key);

        $result = false;
        if ($result === false) {
            $db = $this->getDbConnection();
            if ($itemid !== '') {
                $_itemid = " AND itemid = " . (int) $itemid;
            }
            if ($parentid !== '') {
                       $_parentid = " AND parentid in ( $parentid )";
            }
            if ($parentid === 'sub') { // get all sub comments
                $_parentid = " AND parentid != 0 ";
            }
            if ($published === '')
                $query = "SELECT  count(itemid) as total FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid AND is_review = 0 ORDER BY datetime desc";
            else
                $query = "SELECT  count(itemid) as total FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid AND display = $published AND is_review = 0 ORDER BY datetime desc";
    
            if ($offset !== '' && $records != '') {
                $query .= " LIMIT $offset, $records";
            }
           if ($parentid === '') 
                   $query .= " LIMIT 0, 100";
            $data = array(
                $itemid
            );

            $result = $db->fetchAll($query, $data);
            $cache->setCache($__key, $result[0]["total"], 120);
        }
        return  $result[0]["total"];
    }
    
    
    
    public function getListPagingByItemid($commentid = 2, $itemid = '', $offset = '', $records = '', $published = '', $parentid = '')
    {
        $cache = $this->getCacheInstance();
        $__key = md5($commentid . $itemid . $offset . $records . $published . $parentid . "list");
        $result = $cache->getCache($__key);
        $result = false;
        if ($result === false) {
            $db = $this->getDbConnection();
            if ($itemid !== '') {
                $_itemid = " AND itemid = " . (int) $itemid;
            }
            if ($parentid !== '') {
                $_parentid = " AND parentid in ( $parentid )";
            }
            if ($parentid === 'sub') { // get all sub comments
                $_parentid = " AND parentid != 0 ";
            }
            if ($published === '')
                $query = "SELECT * FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid AND is_review = 0 ORDER BY lastupdate desc,  datetime desc";
            else
                $query = "SELECT * FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid AND display = $published AND is_review = 0 ORDER BY lastupdate desc, datetime desc";
            
            if ($offset !== '' && $records != '') {
                $query .= " LIMIT $offset, $records";
            }
            if ($parentid === '') 
                  $query .= " LIMIT 0, 100";
            $data = array(
                $itemid
            );
            $result = $db->fetchAll($query, $data);
            $cache->setCache($__key, $result, 120);
        }
        return $result;
    }


 public function apiComment($commentid = 2, $start,$end)
    {
        $db = $this->getDbConnection();
        if($start){
            $andDate =" and datetime > '$start' and datetime <= '$end'";
        }  
        $query = " SELECT * FROM " . $this->_tablename . " where commentid=$commentid and parentid=0     $andDate order by id asc  ";
        $result = $db->fetchAll($query);
        return $result;
    }
public function apiCommentSub($commentid = 2, $parentid)
    {
        $db = $this->getDbConnection();
        $query = " SELECT * FROM " . $this->_tablename . " where commentid=$commentid   and parentid=$parentid  ";
        $result = $db->fetchAll($query);
        return $result;
    }



    public function getTotalBySearch($commentid = 2, $search = '', $date = '', $published = '',$offset = '', $records = '')
    {
        $db = $this->getDbConnection();
        if($date)
        $_date = " AND datetime > '$date 00:00:00' AND datetime < '$date 23:59:59'";
;
        if ($search[key($search)] !== ''  ) {
            if (is_numeric($search['id']))
                $_itemid = " AND id = " . (int) $search['id'];
            if (key($search)=='email')
                $_filter = "AND " . key($search) . " LIKE '" . $search[key($search)] . "' ";
            else
                $_filter = "AND " . key($search) . " LIKE '%" . $search[key($search)] . "%' ";
        }

        if ($published === '') {
            if ($search['id']!=0) {
                $query = "SELECT * FROM " . $this->_tablename . " WHERE  commentid=$commentid $_itemid AND is_review = 0 ORDER BY datetime desc";
            } else
                $query = "SELECT * FROM " . $this->_tablename . " WHERE  commentid=$commentid  $_date  $_filter AND is_review = 0 ORDER BY datetime desc";
        } 
        else
            $query = "SELECT * FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_filter AND display = $published $_date AND is_review = 0 ORDER BY datetime desc";
        if ($offset !== '' && $records != '') {
            $query .= " LIMIT $offset, $records";
        }
      if ($parentid === '') 
           $query .= " LIMIT 0, 100";

       //var_dump($query);die();
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getTotalByItemid($commentid = 2, $itemid = '', $offset = '', $records = '', $published = '', $parentid = 0)
    {
        $cache = $this->getCacheInstance();
        $__key = md5($commentid . $itemid . $offset . $records . $published . $parentid);
        
        $result = $cache->getCache($__key);
      //  $result = false;
        if ($result === false) {
            $db = $this->getDbConnection();
            if ($itemid !== '') {
                $_itemid = " AND itemid = " . (int) $itemid;
            }
                   $_parentid = " AND parentid in ( $parentid )";
            
            if ($published === '')
                $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid ORDER BY datetime desc";
            else
                $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE commentid=$commentid $_itemid $_parentid AND display = $published ORDER BY datetime desc";
            
            if ($offset !== '' && $records != '') {
                $query .= " LIMIT $offset, $records";
            }
            if ($parentid === '') 
               $query .= " LIMIT 0, 100";
            
            $data = array(
                $itemid
            );
            $result = $db->fetchAll($query, $data);
            $cache->setCache($__key, $result[0]["total"], 120);
            return $result[0]["total"];
        }
        return $result;
    }

    public function getTotalListByCommentTime($commentid = 2, $published = '',$start='',$end='')
    {
        $db = $this->getDbConnection();
        if($start!='' and $end=='')
        {
            $where = " and datetime>'$start' ";
        }else 
        {
            
            $where = " and datetime>'$start'   and  datetime<'$end' ";
            
        }
        if ($published === '')
            $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE commentid=$commentid $where   AND parentid = 0   ORDER BY datetime desc";
        else
            $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE commentid=$commentid AND published = $published   AND parentid = 0    ORDER BY datetime desc";
        // $data = array($itemid);
        $result = $db->fetchAll($query);
        return $result[0]["total"];
    }
    
    
    public function getTotalListByCommentNo($commentid = 2, $published = '')
    {
        $db = $this->getDbConnection();
        if ($published === '')
            $query = "SELECT count(id) as total FROM " . $this->_tablename . " WHERE id NOT IN ( SELECT parentid  FROM " . $this->_tablename . "  WHERE commentid=$commentid   AND parentid != 0 ) AND  commentid=$commentid   AND parentid = 0  OR (display = 0  AND  commentid=$commentid   AND parentid = 0  )  ORDER BY datetime desc";
        else
            $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE commentid=$commentid AND published = $published   AND parentid = 0   ORDER BY datetime desc";
        // $data = array($itemid);

        $result = $db->fetchAll($query);
        return $result[0]["total"];
    }
    
    
    public function getTotalListByComment($commentid = 2, $published = '')
    {
        $db = $this->getDbConnection();
        if ($published === '')
            $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE commentid=$commentid   AND parentid = 0   ORDER BY datetime desc";
        else
            $query = "SELECT count(*) as total FROM " . $this->_tablename . " WHERE commentid=$commentid AND published = $published   AND parentid = 0   ORDER BY datetime desc";
            // $data = array($itemid);
        $result = $db->fetchAll($query);
        return $result[0]["total"];
    }

    public function getDetail($id)
    {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE id=? ";
        $data = array(
            $id
        );
        $result = $db->fetchAll($query, $data);
        return $result[0];
    }

    public function getListByCate($cateid)
    {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM " . $this->_tablename . " WHERE cateid = '" . parent::adaptSQL($cateid) . "'";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getLastestComments()
    {
        $cache = $this->getCacheInstance();
        $key = "comment.top";
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " ORDER BY datetime DESC LIMIT 0, 10";
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result, 5 * 60);
        }
        return $result;
    }

    public function getComment($itemid,$commentid,$offset='',$limit='') {
        $_general = Business_Addon_General::getInstance();
        $cache = GlobalCache::getCacheInstance('ws');
        $key = "{$this->_prefix_cache}getComment-{$itemid}-{$commentid}-{$offset}-{$limit}";
        $cacheKey = md5($key);
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }
        $query = "SELECT * FROM addon_comment_items WHERE commentid = {$commentid} AND itemid = {$itemid} AND display = 1 AND parentid = 0 AND is_review = 0 ORDER BY id DESC";
        if ($limit !== '' && $offset !== '') {
            $query .= " LIMIT $offset,$limit";
        }
        $result = $_general->excuteCodev2($query);
        if ($result) {
            $newResult = array();
            foreach ($result as $item) {
                $query = "SELECT * FROM addon_comment_items WHERE commentid = {$commentid} AND itemid = {$itemid} AND display = 1 AND parentid = {$item['id']} AND is_review = 0 ORDER BY id ASC";
                $result = $_general->excuteCodev2($query);
                if ($result) {
                    $newSubResult = array();
                    foreach ($result as $sItem) {
                        $sItem["datetime_text"] = Business_Common_Utils::adaptDateToString($sItem['datetime']);
                        $newSubResult[] = $sItem;
                    }
                    $item['answer'] = $newSubResult;
                }
                $item["datetime_text"] = Business_Common_Utils::adaptDateToString($item['datetime']);
                $newResult[] = $item;
            }
            $result = $newResult;
        }
        $cache->setCache($cacheKey, $result);
        return $result;
    }

    public function getCountComment($itemid,$commentid) {
        $_general = Business_Addon_General::getInstance();
        $cache = GlobalCache::getCacheInstance('ws');
        $key = "{$this->_prefix_cache}getCountComment-{$itemid}-{$commentid}";
        $cacheKey = md5($key);
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }
        $query = "SELECT count(*) as count FROM addon_comment_items WHERE commentid = {$commentid} AND itemid = {$itemid} AND display = 1 AND parentid = 0 AND is_review = 0 ORDER BY id DESC";
        $result = $_general->excuteCodev2($query);
        if ($result) {
            $result = $result[0]['count'];
        }
        $cache->setCache($cacheKey, $result);
        return $result;
    }

    public function getLastestCommentsByCate($cateid, $limit = 2)
    {
        $cache = $this->getCacheInstance();
        $key = "comment.cate." . $cateid;
        $result = $cache->getCache($key);
        if ($result === false) {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE cateid = $cateid ORDER BY datetime DESC LIMIT 0, " . $limit;
            $result = $db->fetchAll($query);
            $cache->setCache($key, $result, 5 * 60);
        }
        return $result;
    }

    public function insert($data)
    {
        $cache = $this->getCacheInstance();
        $db = $this->getDbConnection();
        $db->insert($this->_tablename, $data);
        $cache->deleteCache("news.top");
        $cache->deleteCache("comment.cate." . $data["cateid"]);
        return $db->lastInsertId();
    }
    

    public function update($data)
    {
        $id = $data["id"];
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->update($this->_tablename, $data, $where);
        return $result;
    }

    public function delete($id)
    {
        var_dump($id);
        exit();
        $where = array();
        $where[] = "id='" . parent::adaptSQL($id) . "'";
        $db = $this->getDbConnection();
        $result = $db->delete($this->_tablename, $where);
        return $result;
    }
}
?>