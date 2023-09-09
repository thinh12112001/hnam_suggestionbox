<?php

class Business_Addon_Hnproducts extends Business_Abstract
{
    private $_tablename = 'hn_products';
    private $_prefix_cache = 'Business_Addon_Hnproducts::';
    private static $_instance = null;

    function __construct(){
    }

    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_Hnproducts();
        }
        return self::$_instance;
    }

    function getDbConnection() {
        $db = Globals::getDbConnection('maindb', false);
        return $db;
    }

    public function getList($itemid) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getList-{$itemid}");
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }

        $db = $this->getDbConnection();
        $query = "SELECT * FROM {$this->_tablename} WHERE itemid = {$itemid}";

        $result = $db->fetchAll($query);
        $cache->setCache($cacheKey,$result);
        return $result;
    }

    public function getMostFrequentPriceGroup($itemIds, $checkPrice) { // trả về mảng chứa các price
        $db = $this->getDbConnection();
    
        $itemPrices = array(); // Mảng để lưu giá trị price
        $countUpper10M = 0;
        $countUnder10M = 0;
        
        foreach ($itemIds as $itemId) {
            $query = "SELECT price FROM hn_products WHERE itemid = '$itemId'";
            $result = $db->fetchAll($query);
            
            if (!empty($result)) {
                $itemPrice = $result[0]['price']; // Lấy giá trị price đầu tiên
                $itemPrices[] = $itemPrice; // Thêm giá trị price vào mảng
            } 
            // else {

            //     return "error";
            // }
        }
        if (empty($itemPrices)) {
            return "error";
        }

        foreach ($itemPrices as $price) {
            if ($price > $checkPrice) {
                $countUpper10M +=1;
            } else {
                $countUnder10M +=1;
            }
        }
        if ($countUpper10M >= $countUnder10M) {
            
            return "upper10M";
        }
        return "under10M";
    }
    public function getRandomObjectsByBrandAndPriceUpper10M($topBrand,$itemIds) {
        $db = $this->getDbConnection();
        if (count($itemIds) == 1) {
            $itemIdsString = reset($itemIds); // Lấy phần tử đầu tiên trong mảng
            $query = "SELECT * FROM
            (
                SELECT hn_group_suggestion.itemid, hn_products.price
                FROM hn_group_suggestion
                INNER JOIN hn_products ON hn_group_suggestion.itemid = hn_products.itemid
                WHERE hn_group_suggestion.itemid NOT IN ('{$itemIdsString}')
                AND hn_group_suggestion.ThuongHieu = '{$topBrand}'
                AND hn_products.price > 10000000
                ORDER BY RAND()
                LIMIT 5
            ) AS T1
            ORDER BY T1.price ASC";

            $result = $db->fetchAll($query);
            // $result = $result[0]['ThuongHieu'];
            return $result;
        }

        $query = "SELECT * FROM
        (
            SELECT hn_group_suggestion.itemid, hn_products.price
            FROM hn_group_suggestion
            INNER JOIN hn_products ON hn_group_suggestion.itemid = hn_products.itemid
            WHERE hn_group_suggestion.itemid NOT IN (" . implode(',', $itemIds) . ")
            AND hn_group_suggestion.ThuongHieu = '{$topBrand}'
            AND hn_products.price > 10000000
            ORDER BY RAND()
            LIMIT 5
        ) AS T1
        ORDER BY T1.price ASC";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getRandomObjectsByBrandAndPriceUnder10M($topBrand,$itemIds) {
        $db = $this->getDbConnection();
        
        if (count($itemIds) == 1) {
            $itemIdsString = reset($itemIds); // Lấy phần tử đầu tiên trong mảng
            $query = "SELECT * FROM
            (
                SELECT hn_group_suggestion.itemid, hn_products.price
                FROM hn_group_suggestion
                INNER JOIN hn_products ON hn_group_suggestion.itemid = hn_products.itemid
                WHERE hn_group_suggestion.itemid NOT IN ('{$itemIdsString}')
                AND hn_group_suggestion.ThuongHieu = '{$topBrand}'
                AND hn_products.price <= 10000000
                ORDER BY RAND()
                LIMIT 5
            ) AS T1
            ORDER BY T1.price ASC";

            $result = $db->fetchAll($query);
            // $result = $result[0]['itemid'];

            return $result;
        }
        $query = "SELECT * FROM
        (
            SELECT hn_group_suggestion.itemid, hn_products.price
            FROM hn_group_suggestion
            INNER JOIN hn_products ON hn_group_suggestion.itemid = hn_products.itemid
            WHERE hn_group_suggestion.itemid NOT IN (" . implode(',', $itemIds) . ")
            AND hn_group_suggestion.ThuongHieu = '{$topBrand}'
            AND hn_products.price <= 10000000 AND hn_products.price > 0
            ORDER BY RAND()
            LIMIT 5
        ) AS T1
        ORDER BY T1.price ASC";
        $result = $db->fetchAll($query);
        // print_r($result);
        // die();
        return $result;
    }

    public function getTopBrandForListIds($itemIds){
        $db = $this->getDbConnection();

        if (count($itemIds) == 1) {
                $itemIdsString = reset($itemIds); // Lấy phần tử đầu tiên trong mảng
                $query = "SELECT ThuongHieu
                FROM hn_group_suggestion
                WHERE itemid = '{$itemIdsString}'";

            $result = $db->fetchAll($query);
            $result = $result[0]['ThuongHieu'];
            return $result;
        }
        $query = "SELECT ThuongHieu, COUNT(*) AS count
        FROM hn_group_suggestion
        WHERE itemid IN (" . implode(',', $itemIds) . ")
        GROUP BY ThuongHieu
        ORDER BY count DESC
        LIMIT 1";

        // Thực thi câu truy vấn
        $result = $db->fetchAll($query);

        // Kiểm tra và trả về kết quả
        if (!empty($result)) {
        $topBrand = $result[0]['ThuongHieu'];
        $totalCount = $result[0]['count'];
        
        return $topBrand;
        } else {
            return null; // Không tìm thấy bản ghi nào
        }
    }

    public function getAllBlogUrl() {
        $db = $this->getDbConnection();
        $query = "SELECT DISTINCT SUBSTRING_INDEX(blogUrl, '.html', 1) AS blogUrl
        FROM hn_blog_products_url_counting;";
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getAllBlogProductClickAndUrl() {
        $db = $this->getDbConnection();
        $query = "SELECT
            SUBSTRING_INDEX(blogUrl, '.html', 1) AS originalUrl,
            COUNT(*) AS frequency
            FROM
                hn_blog_products_url_counting
            GROUP BY
                originalUrl
            ORDER BY
                frequency DESC";
            
        $result = $db->fetchAll($query);
        return $result;

    }
    public function getTopClickBlogProduct() {
        // lấy itemid được xem nhiều nhất trên tất cả trang blog
        // $query = "SELECT itemid, COUNT(itemid) AS frequency
        // FROM hn_blog_products_url_counting
        // GROUP BY itemid
        // ORDER BY frequency DESC
        // LIMIT 1";

        // lấy bài blog được xem nhiều nhất có cụm tin-tuc
        // $query = "SELECT
        //     SUBSTRING(blogUrl, LOCATE('tin-tuc', blogUrl) + LENGTH('tin-tuc') + 1) AS commonPart,
        //     COUNT(*) AS frequency
        // FROM
        //     hn_blog_products_url_counting
        // GROUP BY
        //     commonPart
        // ORDER BY
        //     frequency DESC
        // LIMIT 1";
        
        // lấy blog được xem nhiều nhất
        $db = $this->getDbConnection();
        $query = "SELECT
            SUBSTRING_INDEX(blogUrl, '.html', 1) AS originalUrl,
            COUNT(*) AS frequency
            FROM
                hn_blog_products_url_counting
            GROUP BY
                originalUrl
            ORDER BY
                frequency DESC
            LIMIT 1;";
            
        $result = $db->fetchAll($query);
        return $result;

    }

    public function getTopItemidClickPerBlogUrl($blogUrl) {
        $pos = stristr($blogUrl, '.html', true);

        // Use the part of the string before ".html" as the trimmed blogUrl
        $trimmedBlogUrl = $pos !== false ? $pos : $blogUrl;
        
        $db = $this->getDbConnection();
        $query = "SELECT itemid, COUNT(itemid) AS frequency, SUBSTRING_INDEX(blogUrl, '.html', 1) AS originalUrl
        FROM hn_blog_products_url_counting
        WHERE blogUrl LIKE '$trimmedBlogUrl%'
        GROUP BY itemid
        ORDER BY frequency DESC
        LIMIT 3";
            
        $result = $db->fetchAll($query);
        return $result;
    }

    public function getBlogUrlByUid($uid) {
        $db = $this->getDbConnection();
        $query = "SELECT itemid, blogUrl 
        from hn_blog_products_url_counting
        where uid = {$uid}";

        $result = $db->fetchAll($query);
        return $result;
    }

    public function getCartValueByUid($uid) {
        $db = $this->getDbConnection();
        $query = "SELECT cartValue
            from hn_cart
            where uid = {$uid}";

        $result = $db->fetchAll($query);
        return $result;
    }

    public function updateCart($uid, $cartValue) {
        $db = $this->getDbConnection();
        $query = "INSERT INTO hn_cart (uid, cartValue)
        VALUES ($uid, '$cartValue')";

        $result = $db->fetchAll($query);
        return $result;
    }

    public function updateUrlCount($itemid, $currentUserUrl, $uid) {
        $db = $this->getDbConnection();
        $query = "INSERT INTO hn_blog_products_url_counting (itemid, blogUrl, uid)
                    VALUES ($itemid, '$currentUserUrl', $uid)";
          
        $result = $db->query($query);
        return $result->rowCount();
    }

    public function updateTransactionTable($uid) {
        $db = $this->getDbConnection();
        $query = "INSERT INTO hn_transaction_blog_count (uid, clicksCount) 
                    VALUES ($uid, 1) 
                    ON DUPLICATE KEY UPDATE clicksCount = clicksCount + 1";
                    
        $result = $db->query($query);
        return $result->rowCount();            
    }

    public function checkBLogClick($uid) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM hn_blog_products_url_counting
        WHERE uid = $uid";

        $result = $db->fetchAll($query);
        return $result;
    }
    
    public function updateIdCount($itemid) {
        $db = $this->getDbConnection();
        $query = "INSERT INTO hn_products_clickcounts (itemid, clicksCount)
          VALUES ($itemid, 1)
          ON DUPLICATE KEY UPDATE clicksCount = clicksCount + 1";
        
        $result = $db->query($query);
        return $result->rowCount();
            
    }
    public function getItemTopThuongHieuNew($keywordsArray) {
        // $cache = GlobalCache::getCacheInstance('ws');
        // $cacheKey = md5($this->_prefix_cache . "getItemTopThuongHieuNew-{$keywordsArray}");
        // $result = $cache->getCache($cacheKey);
        // if ($result) {
        //     return $result;
        // }

        $db = $this->getDbConnection();

        $keywords = "'" . implode("','", $keywordsArray) . "'";
        
        $query = "SELECT T1.itemid, T1.price 
                    FROM (
                        SELECT hn_group_suggestion.itemid, hn_products.price 
                        FROM hn_group_suggestion 
                        JOIN hn_products ON hn_group_suggestion.itemid = hn_products.itemid
                        WHERE hn_group_suggestion.ThuongHieu in ($keywords) AND hn_products.price > 0
                        ORDER BY RAND()
                        LIMIT 5
                    ) AS T1
                    ORDER BY T1.price ASC";

        $result = $db->fetchAll($query);
        // $cache->setCache($cacheKey,$result);
        
        return $result;
    }
    
    public function getItemTopThuongHieu($keyword) {
        $cache = GlobalCache::getCacheInstance('ws');
        $cacheKey = md5($this->_prefix_cache . "getItemTopThuongHieu-{$keyword}");
        $result = $cache->getCache($cacheKey);
        if ($result) {
            return $result;
        }

        $db = $this->getDbConnection();
        $query = "SELECT T1.itemid, T1.price 
          FROM (
              SELECT hn_group_suggestion.itemid, hn_products.price 
              FROM hn_group_suggestion 
              JOIN hn_products ON hn_group_suggestion.itemid = hn_products.itemid
              WHERE hn_group_suggestion.ThuongHieu = '$keyword' AND hn_products.price > 0
              ORDER BY hn_products.price DESC
              LIMIT 5
          ) AS T1
          ORDER BY T1.price ASC";

        $result = $db->fetchAll($query);
        $cache->setCache($cacheKey,$result);
        
        return $result;
    }
    public function getItemByThuongHieu($keyword) {
        $db = $this->getDbConnection();
        $query = "SELECT T1.itemid, T1.price 
                FROM (
                    SELECT hn_group_suggestion.itemid, hn_products.price 
                    FROM hn_group_suggestion 
                    JOIN hn_products ON hn_group_suggestion.itemid = hn_products.itemid
                    WHERE hn_group_suggestion.ThuongHieu = '$keyword' AND hn_products.price > 0
                    ORDER BY RAND()
                    LIMIT 5
                ) AS T1
                ORDER BY T1.price ASC";

        $result = $db->fetchAll($query);
        return $result;
    }

    public function getRandomItems() {
        
        $db = $this->getDbConnection();
        $query = "SELECT *
        FROM (
            SELECT itemid, price
            FROM hn_products
            WHERE price > 0
            ORDER BY RAND()
            
            LIMIT 5
        ) AS T1
        ORDER BY price ASC";
        
        $result = $db->fetchAll($query);
        
        return $result;
        
    }

    public function getLastItemIdsMissing($itemIds, $lastItemId, $limitNum)
    {
        $db = $this->getDbConnection();
        $subquery = "SELECT DISTINCT brand FROM hn_features where product_itemid = {$lastItemId}";
        $subresult = $db->fetchAll($subquery);
        $brand = $subresult[0]['brand'];
        
        $subquery2 ="SELECT itemid from hn_group_suggestion
        WHERE hn_group_suggestion.ThuongHieu = '{$brand}' 
        AND FIND_IN_SET(itemid, '{$itemIds}') = 0
        ORDER BY RAND()
        LIMIT {$limitNum}";
        $subresult2 = $db->fetchAll($subquery2);
        
        $count = count($subresult2);
        $missingItemids = "";
        foreach ($subresult2 as $index => $val) {
            $missingItemids .= $val['itemid'];
            if ($index < $count - 1) {
                $missingItemids .= ",";
            }
        }
        $itemIds .=  ",".$missingItemids;

        $query = "SELECT hn_group_suggestion.itemid, hn_products.price
            FROM hn_products
            JOIN hn_group_suggestion ON hn_products.itemid = hn_group_suggestion.itemid
            WHERE FIND_IN_SET(hn_group_suggestion.itemid, '{$itemIds}')
            ORDER BY hn_products.price ASC
            ";

        $result = $db->fetchAll($query);
        return $result;
    }

    public function updateTopTraffic($topBrandGroup, $topPriceGroup) {
        $db = $this->getDbConnection();
        
        $query = "UPDATE hn_top_traffics
                  SET topBrandGroup = '{$topBrandGroup}',
                      topPriceGroup = '{$topPriceGroup}',
                      isUpdated = 1,
                      dateUpdate = NOW()
                  WHERE id = 1";
    
        $result = $db->query($query);
        return $result->rowCount();
    }
    public function updateTopTrafficToZero() {
        $db = $this->getDbConnection();
        
        $query = "UPDATE hn_top_traffics
                  SET isUpdated = 0,
                  dateUpdate = NOW()
                  WHERE id = 1";
    
        $result = $db->query($query);
        return $result->rowCount();
    }
    public function getIsUpdateStatus() {
        $db = $this->getDbConnection();
        
        $query = "SELECT isUpdated from hn_top_traffics where id =1";
        $result = $db->fetchAll($query);
        return $result;
    }
    public function getTopTraffic() {
        $db = $this->getDbConnection();
        
        $query = "SELECT * from hn_top_traffics where id =1";
        $result = $db->fetchAll($query);
        // print_R($result);
        // die();
        return $result;
    }
    
    // public function getRandomItemsWithLastIDUnder10M($itemIds, $priceLine,$limitNum) {
        
    //     print_r($itemIds);
    //     die();
    //     $db = $this->getDbConnection();
    //     $subquery = "SELECT price from hn_products where itemid = {$itemIds}";
    //     $subqueryResult = $db->fetchAll($subquery);
    //     $lastidPrice =  $subqueryResult[0]['price'];

    //     // on progress
    //     // ==> select top brand from $itemIds
    //     $query = "SELECT *
    //     FROM (
    //         SELECT hn_group_suggestion.itemid, hn_products.price
    //         FROM hn_products JOIN hn_group_suggestion ON hn_products.itemid = hn_group_suggestion.itemid
    //         WHERE price < {$priceLine} AND price > {$lastidPrice}
    //         ORDER BY RAND()
            
    //         LIMIT {$limitNum}
    //     ) AS T1
    //     ORDER BY T1.price ASC";
        
    //     $result = $db->fetchAll($query);
    //     return $result;
        
    // }

    // public function getRandomItemsWithLastIDUpper10M($itemIds, $priceLine,$limitNum) {
        
    //     $db = $this->getDbConnection();
    //     $subquery = "SELECT price from hn_products where itemid = {$itemIds}";
    //     $subqueryResult = $db->fetchAll($subquery);
    //     $lastidPrice =  $subqueryResult[0]['price'];


    //     $query = "SELECT *
    //     FROM (
    //         SELECT itemid, price
    //         FROM hn_products
    //         WHERE price > {$priceLine} AND price > {$lastidPrice}
    //         ORDER BY RAND()
            
    //         LIMIT {$limitNum}
    //     ) AS T1
    //     ORDER BY price ASC";
        
    //     $result = $db->fetchAll($query);
    //     return $result;
        
    // }

    function getItemDetails($listIds) {
        $db = $this->getDbConnection();
        $query = "SELECT * FROM hn_products WHERE itemid IN ({$listIds}) ORDER BY FIELD(itemid, {$listIds})";
        $result = $db->fetchAll($query);
        return $result;
    }
    
    
    public function getListItemByUid($uid) {
        $db = $this->getDbConnection();
        $query = "SELECT urlLink FROM `addon_user_trip` where uid = {$uid} AND urlLink LIKE '%.%.html%' ";
    
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
    public function getListUrlByUid($uid) {
        $db = $this->getDbConnection();
        $query = "SELECT urlLink FROM `addon_user_trip` WHERE uid = {$uid}
        AND (urlLink LIKE '%nokia%' 
        OR urlLink LIKE '%samsung%' 
        OR urlLink LIKE '%oppo%' 
        OR urlLink LIKE '%xiaomi%' 
        OR urlLink LIKE '%apple%')
        ";

    
        $result = $db->fetchAll($query);
        // print_r($result);
        // die();
        // $listIds = array();
        // foreach ($result as $row) {
        //     $urlLink = $row['urlLink'];
    
        //     // Tách chuỗi sau dấu /
        //     $afterSlash = explode('/', $urlLink);
        //     $lastSegment = end($afterSlash);
    
        //     // Tìm vị trí dấu -
        //     $dashPosition = strpos($lastSegment, '-');
    
        //     if ($dashPosition !== false) {
        //         // Lấy cụm từ từ vị trí 0 đến vị trí dấu -
        //         $substring = substr($lastSegment, 0, $dashPosition);
        //         $listIds[] = $substring;
        //     }
        // }
        // print_r($result);
        // die();
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

}