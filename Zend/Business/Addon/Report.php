<?php

class Business_Addon_Report extends Business_Abstract
{
    private $_tablename = 'addon_report';
    private $_prefix_cache = 'Business_Addon_Report::';
    private static $_instance = null;

    function __construct(){
    }

    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_Report();
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

    public function getMostFrequentUrlAccess() {
        $db = $this->getDbConnection();

        $today = date('Y-m-d'); //ngày hiện tại
        
        // $today = "2023-06-29";
        $query = "SELECT urlLink, COUNT(urlLink) AS count
            FROM addon_user_trip
            WHERE DATE_FORMAT(created, '%Y-%m-%d') = '$today'
            GROUP BY urlLink
            ORDER BY count DESC
            LIMIT 3";
        
        $result = $db->fetchAll($query);
        
        return $result;
        // $result = $result[1]['count'];
        // print_r($result);
        // die();
    }

    public function getTopTransaction() {
        $db = $this->getDbConnection();

        $query = "SELECT * FROM `hn_transaction_blog_count` ORDER BY clicksCount DESC";
        $result = $db->fetchAll($query);
        // print_r($result);
        // die();
        return $result;
    }

    public function getTopSuccessPayment() {
        $db = $this->getDbConnection();

        $query = "SELECT uid, COUNT(*) AS count
            FROM `hn_cart`
            GROUP BY uid
            ORDER BY count DESC";
        $result = $db->fetchAll($query);
        return $result;
    }
    
    public function getAccessTimesForToday($url) 
    {
        $db = $this->getDbConnection();

        $today = date('Y-m-d'); // get day hiện tại
        // $today = "2023-06-28";

        $query = "SELECT created FROM addon_user_trip WHERE urlLink LIKE '%{$url}%' AND DATE_FORMAT(created, '%Y-%m-%d') = '{$today}'"; //'
        $result = $db->fetchAll($query);

        $createdValues = array();
        foreach ($result as $row) {
            $createdValues[] = $row['created'];
            
        }
        
        return implode(',', $createdValues);
    }
    public function getAllUrlLink() {
        $db = $this->getDbConnection();
        $query = "SELECT DISTINCT urlLink FROM addon_user_trip";
        $result = $db->fetchAll($query);
        // print_r($result);
        // die();
        return $result;
    }

    public function getAllUrlLinkFromDetailedProductsAccesing() {
        $db = $this->getDbConnection();
        $query = "SELECT urlLink
        FROM addon_user_trip
        WHERE urlLink LIKE '%._____.html%'
        ";
        $result = $db->fetchAll($query);
        
        foreach ($result as $row) {
            $urlLink = $row['urlLink'];
            $matches = array();
        
            if (preg_match('/\d{5}/', $urlLink, $matches)) {
                $id = $matches[0];
                if (isset($listIds[$id])) {
                    $listIds[$id]++;
                } else {
                    $listIds[$id] = 1;
                }
            }
        }
        // print_r($listIds);
        // die();
        // trả về danh sách các ID kèm với số lần nó xuất hiện (count)
        return $listIds;
    }
    public function getAllItemid() {
        $db = $this->getDbConnection();
        $query = "SELECT urlLink FROM `addon_user_trip`";
    
        $result = $db->fetchAll($query);

        // return $result;
        
        $listIds = array();
        foreach ($result as $row) {
            $urlLink = $row['urlLink'];
            $matches = array();
    
            if (preg_match('/\d{5}/', $urlLink, $matches)) {
                $listIds[] = $matches[0];
            }
        }
        // }
        // print_r($listIds);
        // die();
        return $listIds;
    }

    public function getTopPrice($listIds) {
        $db = $this->getDbConnection();
        // Tạo danh sách tham số ràng buộc cho các giá trị id
        $placeholders = implode(',', array_fill(0, count($listIds), '?'));
        $query = "SELECT 
                    hn_products.itemid AS id,
                    (CASE
                        WHEN hn_products.price > 20000000 THEN 'Upper20M'
                        WHEN hn_products.price < 10000000  THEN 'Under10M'
                        WHEN hn_products.price >= 10000000 AND hn_products.price <= 20000000  THEN '10mTo20M'
                    END) AS price
                  FROM hn_products
                  WHERE hn_products.itemid IN ($placeholders)
                  GROUP BY hn_products.itemid
        ";
        $params = $listIds;

        // Thực thi câu truy vấn với tham số ràng buộc
        $result = $db->fetchAll($query, $params);
        return $result;

    }

    public function getTopChipPin($listIds) {
        $db = $this->getDbConnection();
        // Tạo danh sách tham số ràng buộc cho các giá trị id
        $placeholders = implode(',', array_fill(0, count($listIds), '?'));
        
        // Chuẩn bị câu truy vấn với tham số ràng buộc
        $query = "SELECT 
                    hn_features.product_itemid AS id,
                    MAX(CASE
                        WHEN hn_features.fid = 181 THEN
                            CASE
                                WHEN hn_features.fvalue LIKE '%Snapdragon%' THEN 'Snapdragon'
                                WHEN hn_features.fvalue LIKE '%Apple%' THEN 'Apple'
                                WHEN hn_features.fvalue LIKE '%MediaTek%' THEN 'MediaTek'
                                WHEN hn_features.fvalue LIKE '%Helio%' THEN 'Helio'
                                WHEN hn_features.fvalue LIKE '%Exynos%' THEN 'Exynos'
                                ELSE ''
                            END
                        ELSE ''
                    END) AS chip,
                    MAX(CASE
                        WHEN hn_features.fid = 186 THEN
                            CASE
                                WHEN hn_features.fvalue LIKE '%mAh%' THEN
                                    CASE
                                        WHEN CAST(SUBSTRING_INDEX(hn_features.fvalue, 'mAh', 1) AS UNSIGNED) >= 5000 THEN '>5000'
                                        ELSE '<5000'
                                    END
                                ELSE ''
                            END
                        ELSE ''
                    END) AS pin
                FROM hn_features
                WHERE hn_features.product_itemid IN ($placeholders)
                GROUP BY hn_features.product_itemid";

        // Chuẩn bị các giá trị id cho tham số ràng buộc
        $params = $listIds;

        // Thực thi câu truy vấn với tham số ràng buộc
        $result = $db->fetchAll($query, $params);
        // print_r($result);
        // die();
        return $result;
    }

    public function getCountingNumberForBrands() {
        $db = $this->getDbConnection();
        $query = "SELECT *
        FROM (
            SELECT 
                'apple' AS brand,
                SUM(CASE WHEN urlLink LIKE '%apple%' THEN 1 ELSE 0 END) AS count
            FROM addon_user_trip
            UNION ALL
            SELECT 
                'oppo' AS brand,
                SUM(CASE WHEN urlLink LIKE '%oppo%' THEN 1 ELSE 0 END) AS count
            FROM addon_user_trip
            UNION ALL
            SELECT 
                'xiaomi' AS brand,
                SUM(CASE WHEN urlLink LIKE '%xiaomi%' THEN 1 ELSE 0 END) AS count
            FROM addon_user_trip
            UNION ALL
            SELECT 
                'nokia' AS brand,
                SUM(CASE WHEN urlLink LIKE '%nokia%' THEN 1 ELSE 0 END) AS count
            FROM addon_user_trip
            UNION ALL
            SELECT 
                'samsung' AS brand,
                SUM(CASE WHEN urlLink LIKE '%samsung%' THEN 1 ELSE 0 END) AS count
            FROM addon_user_trip
        ) AS subquery
        ORDER BY count DESC
        ";
        $result = $db->fetchAll($query);
        return $result;
    }
    
    public function getListItemByUid($uid) {
        $db = $this->getDbConnection();
        $query = "SELECT urlLink FROM `addon_user_trip` where uid = {$uid}";
    
        $result = $db->fetchAll($query);

        // return $result;
        
        $listIds = array();
        foreach ($result as $row) {
            $urlLink = $row['urlLink'];
            $matches = array();
    
            if (preg_match('/\d{5}/', $urlLink, $matches)) {
                $listIds[] = $matches[0];
            }
        }
        return $listIds;
    }

    public function getMostFrequentPriceGroup($itemIds) {
        // $db = $this->getDbConnection();
        // $itemIdsString = implode(',', $itemIds);
        // print_r($itemIdsString);
        // $query = "SELECT price from hn_products where itemid in ('$itemIdsString')";
        // $result = $db->fetchAll($query);
        
        // print_r($result);
        // die();
        // return($result);
        $db = $this->getDbConnection();
        if (empty($itemIds)) {
            return "";
        }
        $itemIdsString = implode(',', array_map(function($itemId) {
            return "'" . trim($itemId) . "'";
        }, $itemIds));
        
        $query = "SELECT price FROM hn_products WHERE itemid IN ($itemIdsString)";
        $result = $db->fetchAll($query);

        return $result;
    }

    public function getMostFrequentThuongHieu($uid) {
        
        $db = $this->getDbConnection();

        $query = "SELECT 
        CASE
            WHEN urlLink LIKE '%apple%' THEN 'apple'
            WHEN urlLink LIKE '%oppo%' THEN 'oppo'
            WHEN urlLink LIKE '%nokia%' THEN 'nokia'
            WHEN urlLink LIKE '%xiaomi%' THEN 'xiaomi'
            WHEN urlLink LIKE '%samsung%' THEN 'samsung'
        END AS brand,
        COUNT(*) AS count
    FROM 
        addon_user_trip
    WHERE 
        (urlLink LIKE '%apple%' OR
         urlLink LIKE '%oppo%' OR
         urlLink LIKE '%nokia%' OR
         urlLink LIKE '%xiaomi%' OR
         urlLink LIKE '%samsung%') AND
        uid = {$uid}
    GROUP BY brand
    ORDER BY count DESC
    ";
        $result = $db->fetchAll($query);
        // print_r($result);
        // die();
        return $result;
    }
    public function getItemSuggestUpper20M($topBrandGroup) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getItemSuggestUpper20M-{$topBrandGroup}");
        $result = $cache->getCache($cacheKey);
        if ($result) {
            
            return $result;
        }

        $db = $this->getDbConnection();
        $query = 
            "SELECT T1.itemid, T1.price FROM(
            SELECT hn_products.itemid, hn_products.price FROM hn_features JOIN hn_products
            ON hn_features.product_itemid = hn_products.itemid
            WHERE hn_products.price > 20000000 AND hn_features.brand = '{$topBrandGroup}'
            ORDER by RAND()
            LIMIT 5) AS T1
            ORDER BY T1.price";

        if ($query) {
            $result = $db->fetchAll($query);
            $cache->setCache($cacheKey,$result);
            // print_r($result);
            // die();
            return $result;
        }
        return "";

    }

    public function getItemSuggestUnder10M($topBrandGroup) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getItemSuggestUnder10M-{$topBrandGroup}");
        $result = $cache->getCache($cacheKey);
        if ($result) {
            
            return $result;
        }

        $db = $this->getDbConnection();
        $query = 
            "SELECT * FROM(
            SELECT hn_products.itemid, hn_products.price FROM hn_features JOIN hn_products
            ON hn_features.product_itemid = hn_products.itemid
            WHERE hn_products.price < 10000000 AND hn_features.brand = '{$topBrandGroup}'
            ORDER by RAND()
            LIMIT 5) AS T1
            ORDER BY T1.price";
        if ($query) {
            $result = $db->fetchAll($query);
            $cache->setCache($cacheKey,$result);
            // print_r($result);
            // die();
            return $result;
        }
        return "";
    }
    public function getItemSuggestBetween10MAnd20M($topBrandGroup) {
        
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getItemSuggestBetween10MAnd20M-{$topBrandGroup}");
        $result = $cache->getCache($cacheKey);
        if ($result) {
            
            return $result;
        }
        
        $db = $this->getDbConnection();
        $query = 
            "SELECT *
            FROM (
                SELECT hn_products.itemid, hn_products.price
                FROM hn_features
                JOIN hn_products ON hn_features.product_itemid = hn_products.itemid
                WHERE hn_products.price >= 10000000
                    AND hn_products.price < 20000000
                    AND hn_features.brand = '{$topBrandGroup}'
                ORDER BY RAND()
                LIMIT 5
            ) AS T1
            ORDER BY T1.price";
        if ($query) {
            $result = $db->fetchAll($query);
            $cache->setCache($cacheKey,$result);
            // print_r($result);
            // die();
            return $result;
        }
        return "";
    }
    // by Top: Brand and Price 
        // If not enough ==> Pin ==> Chip
    public function getItemForNewMembers($topBrandGroup) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getItemForNewMembers-{$topBrandGroup}");
        $result = $cache->getCache($cacheKey);
        if ($result) {
            
            return $result;
        }
// die();
        $db = $this->getDbConnection();
        // if ($topPriceGroup == "Upper20M") {
        //     $query = 
        //     "SELECT * FROM(
        //     SELECT hn_products.itemid, hn_products.price FROM hn_features JOIN hn_products
        //     ON hn_features.product_itemid = hn_products.itemid
        //     WHERE hn_products.price > 20000000 AND hn_features.brand = '{$topBrandGroup}'
        //     ORDER by RAND()
        //     LIMIT 5) AS T1
        //     ORDER BY T1.price";
        // } else if ($topPriceGroup == "Under10M") {
        //     $query = 
        //     "SELECT * FROM(
        //     SELECT hn_products.itemid, hn_products.price FROM hn_features JOIN hn_products
        //     ON hn_features.product_itemid = hn_products.itemid
        //     WHERE hn_products.price < 10000000 AND hn_features.brand = '{$topBrandGroup}'
        //     ORDER by RAND()
        //     LIMIT 5) AS T1
        //     ORDER BY T1.price";
        // } else {
        //     // die();
        //     $query = 
        //     "SELECT *
        //     FROM (
        //         SELECT hn_products.itemid, hn_products.price
        //         FROM hn_features
        //         JOIN hn_products ON hn_features.product_itemid = hn_products.itemid
        //         WHERE hn_products.price >= 10000000
        //             AND hn_products.price < 20000000
        //             AND hn_features.brand = '{$topBrandGroup}'
        //         ORDER BY RAND()
        //         LIMIT 5
        //     ) AS T1
        //     ORDER BY T1.price
        //     ";
        // }
        $query = "SELECT hn_products.itemid,hn_products.price from hn_products
        JOIN hn_group_suggestion ON hn_products.itemid = hn_group_suggestion.itemid
        WHERE hn_group_suggestion.ThuongHieu = '{$topBrandGroup}' AND hn_products.price > 0
        ORDER BY hn_products.price ASC
        LIMIT 5";

        if ($query) {
            $result = $db->fetchAll($query);
            $cache->setCache($cacheKey,$result);
            // print_r($result);
            // die();
            return $result;
        }
        return "";

        
    }

    public function getTimeStampAvereageAndNumberAccess($url) {
         // lấy thgian truy cập trung bình theo 1 url
        //  $url = "https://www.hnammobile.com/dien-thoai/apple-iphone";
         $TodayTimeStampAccess = Business_Addon_Report::getInstance()->getAccessTimesForToday($url); 
         
         $timeArray = explode(",", $TodayTimeStampAccess);
         $numberOfAccess = count($timeArray);
        if ($numberOfAccess > 1) {
            $timestamps = array_map('strtotime', $timeArray);
            $accessTimes = [];

            for ($i = 1; $i < count($timestamps); $i++) {
                $timeDiff = $timestamps[$i] - $timestamps[$i - 1];
                $accessTimes[] = $timeDiff;
            }

            $averageAccessTime = array_sum($accessTimes) / count($accessTimes);
            $averageMinutes = floor($averageAccessTime / 60);
            $averageSeconds = $averageAccessTime % 60;

            return array(
                'averageMinutes' => $averageMinutes,
                'averageSeconds' => $averageSeconds,
                'numberOfAccess' => $numberOfAccess
            );
        } else {
            echo "Không có dữ liệu truy cập cho URL đã cho.<br>";
        }
    }
}