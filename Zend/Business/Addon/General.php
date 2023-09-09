<?php

class Business_Addon_General extends Business_Abstract
{
    private static $_instance = null;
    private $_prefix_cache = 'Business_Addon_General::';

    function __construct() {

    }



    public function isLayoutV2() {
        if (isset($_REQUEST['old_mode']) && $_REQUEST['old_mode']) {
            return false;
        }
        return true;

    }



    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Business_Addon_General();
        }
        return self::$_instance;
    }


    private function getDbConnection($db = '') {
        $db = $db?$db:'maindb';
        $db = Globals::getDbConnection($db);
        return $db;
    }



    public function quoteDB($str) {
        $db = $this->getDbConnection();
        return $db->quote($str);
    }



    public function excuteCode($query){
        $db     =  $this->getDbConnection();
        $result = $db->query(''.$query.'');
    }

    public function clearCache(){
        $cache = GlobalCache::getCacheInstance('ws');
        $cache->flushAll();
    }

    public function excuteCodev2($query,$cached=''){
        if ($cached) {
            $cache = GlobalCache::getCacheInstance('ws');
            $cacheKey = $cached;
            $result = $cache->getCache($cacheKey);
            if ($result) {
                return $result;
            }
            $db     =  $this->getDbConnection();
            $result = $db->fetchAll($query);
            $cache->setCache($cacheKey,$result);
            return $result;
        }
        else {
            $db     =  $this->getDbConnection();
            $result = $db->fetchAll($query);
            return $result;
        }
    }

    public function getRow($query,$cached=''){
        if ($cached) {
            $cache = GlobalCache::getCacheInstance('ws');
            $cacheKey = $cached;
            $result = $cache->getCache($cacheKey);
            if ($result !== false) {
                return $result;
            }
            $db     =  $this->getDbConnection();
            $result = $db->fetchRow($query);
            if (!$result) {
                $result = '';
            }
            $cache->setCache($cacheKey,$result);
            return $result;
        }
        else {
            $db     =  $this->getDbConnection();
            $result = $db->fetchRow($query);
            return $result;
        }
    }

    public function getRows($table, $where, $db=''){
        $db = $this->getDbConnection($db);
        $query = "select * from $table";
        if($where) {
            $query .= " where (1) and $where";
        }
        $_result = $db->fetchAll($query);
        if($_result) {
            return $_result;
        }
        return 0;
    }

    public function getDataDB($table,$select='',$where='',$orderby='',$limit='',$db='',$groupby='') {
        $db = $this->getDbConnection($db);
        $select = $select?$select:'*';
        $orderby = $orderby?$orderby:'id asc';
        $query = "select $select from $table";
        if($where) {
            $query .= " where (1) and $where";
        }
        if($groupby) {
            $query .= " group by $groupby";
        }
        $query.= " order by $orderby";
        if($limit) {
            $query .= " limit $limit";
        }
        $_result = $db->fetchAll($query);
        if($_result) {
            return $_result;
        }
        return 0;
    }
    // fetchRow
    public function getDataDBRecod($table,$select='', $where='', $db=''){
        $db = $this->getDbConnection($db);
        if(empty($select)){
            $select = "*";
            $query = "select $select from $table";
        }else{
            $query = "select $select from $table";
        }
        if($where) {
            $query .= " where (1) and $where";
        }
        $_result = $db->fetchAll($query);
        if($_result) {
            return $_result;
        }
        return 0;
    }

    public function insertDB($table,$data) {
        
        $db = $this->getDbConnection();
        $result = $db->insert($table,$data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($table);
        }
        return $lastid;
    }

    public function updateDB($table,$data,$query) {
        $db= $this->getDbConnection();
        $result = $db->update($table, $data, $query);
        return $result;
    }

    public function slugString($str,$separator = true) {
        if ($separator) {
            $str = explode('(', $str);
            $str = $str[0];
        }
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = preg_replace('/[^A-Za-z0-9]+/', ' ', $str);
        $str = preg_replace('!\s+!', ' ',  trim($str));
        $str = str_replace(' ', '-', $str);
        $str = strtolower($str);
        return $str;
    }

    public function formatString($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = preg_replace('/[^A-Za-z0-9]+/', ' ', $str);
        $str = preg_replace('!\s+!', ' ',  trim($str));
        return $str;
    }



    function getYoutubeImage($vidID,$thumb='medium'){
        if ($vidID) {
            switch ($thumb) {
                case 'small':
                    $type = 'default.jpg';
                    break;
                case 'large':
                    $type = 'maxresdefault.jpg';
                    break;
                default:
                    $type = 'hqdefault.jpg';
            }
            return $thumb = "https://img.youtube.com/vi/{$vidID}/{$type}";
        }
        else {
            return false;
        }
    }



    public function is_bot($sistema){
        $bots = array(
            'Googlebot'
        , 'Baiduspider'
        , 'ia_archiver'
        , 'R6_FeedFetcher'
        , 'NetcraftSurveyAgent'
        , 'Sogou web spider'
        , 'bingbot'
        , 'Yahoo! Slurp'
        , 'facebookexternalhit'
        , 'PrintfulBot'
        , 'msnbot'
        , 'Twitterbot'
        , 'UnwindFetchor'
        , 'urlresolver'
        , 'Butterfly'
        , 'TweetmemeBot'
        , 'PaperLiBot'
        , 'MJ12bot'
        , 'AhrefsBot'
        , 'Exabot'
        , 'Ezooms'
        , 'YandexBot'
        , 'SearchmetricsBot'
        , 'picsearch'
        , 'TweetedTimes Bot'
        , 'QuerySeekerSpider'
        , 'ShowyouBot'
        , 'woriobot'
        , 'merlinkbot'
        , 'BazQuxBot'
        , 'Kraken'
        , 'SISTRIX Crawler'
        , 'R6_CommentReader'
        , 'magpie-crawler'
        , 'GrapeshotCrawler'
        , 'PercolateCrawler'
        , 'MaxPointCrawler'
        , 'R6_FeedFetcher'
        , 'NetSeer crawler'
        , 'grokkit-crawler'
        , 'SMXCrawler'
        , 'PulseCrawler'
        , 'Y!J-BRW'
        , '80legs.com/webcrawler'
        , 'Mediapartners-Google'
        , 'Spinn3r'
        , 'InAGist'
        , 'Python-urllib'
        , 'NING'
        , 'TencentTraveler'
        , 'Feedfetcher-Google'
        , 'mon.itor.us'
        , 'spbot'
        , 'Feedly'
        , 'bitlybot'
        , 'ADmantX Platform'
        , 'Niki-Bot'
        , 'Pinterest'
        , 'python-requests'
        , 'DotBot'
        , 'HTTP_Request2'
        , 'linkdexbot'
        , 'A6-Indexer'
        , 'Baiduspider'
        , 'TwitterFeed'
        , 'Microsoft Office'
        , 'Pingdom'
        , 'BTWebClient'
        , 'KatBot'
        , 'SiteCheck'
        , 'proximic'
        , 'Sleuth'
        , 'Abonti'
        , '(BOT for JCE)'
        , 'Baidu'
        , 'Tiny Tiny RSS'
        , 'newsblur'
        , 'updown_tester'
        , 'linkdex'
        , 'baidu'
        , 'searchmetrics'
        , 'genieo'
        , 'majestic12'
        , 'spinn3r'
        , 'profound'
        , 'domainappender'
        , 'VegeBot'
        , 'terrykyleseoagency.com'
        , 'CommonCrawler Node'
        , 'AdlesseBot'
        , 'metauri.com'
        , 'libwww-perl'
        , 'rogerbot-crawler'
        , 'MegaIndex.ru'
        , 'ltx71'
        , 'Qwantify'
        , 'Traackr.com'
        , 'Re-Animator Bot'
        , 'Pcore-HTTP'
        , 'BoardReader'
        , 'omgili'
        , 'okhttp'
        , 'CCBot'
        , 'Java/1.8'
        , 'semrush.com'
        , 'feedbot'
        , 'CommonCrawler'
        , 'AdlesseBot'
        , 'MetaURI'
        , 'ibwww-perl'
        , 'rogerbot'
        , 'MegaIndex'
        , 'BLEXBot'
        , 'FlipboardProxy'
        , 'techinfo@ubermetrics-technologies.com'
        , 'trendictionbot'
        , 'Mediatoolkitbot'
        , 'trendiction'
        , 'ubermetrics'
        , 'ScooperBot'
        , 'TrendsmapResolver'
        , 'Nuzzel'
        , 'Go-http-client'
        , 'Applebot'
        , 'LivelapBot'
        , 'GroupHigh'
        , 'SemrushBot'
        , 'ltx71'
        , 'commoncrawl'
        , 'istellabot'
        , 'DomainCrawler'
        , 'cs.daum.net'
        , 'StormCrawler'
        , 'GarlikCrawler'
        , 'The Knowledge AI'
        , 'getstream.io/winds'
        , 'YisouSpider'
        , 'archive.org_bot'
        , 'semantic-visions.com'
        , 'FemtosearchBot'
        , '360Spider'
        , 'linkfluence.com'
        , 'glutenfreepleasure.com'
        , 'Gluten Free Crawler'
        , 'YaK/1.0'
        , 'Cliqzbot'
        , 'app.hypefactors.com'
        , 'axios'
        , 'semantic-visions.com'
        , 'webdatastats.com'
        , 'schmorp.de'
        , 'SEOkicks'
        , 'DuckDuckBot'
        , 'Barkrowler'
        , 'ZoominfoBot'
        , 'Linguee Bot'
        , 'Mail.RU_Bot'
        , 'OnalyticaBot'
        , 'Linguee Bot'
        , 'admantx-adform'
        , 'Buck/2.2'
        , 'Barkrowler'
        , 'Zombiebot'
        , 'Nutch'
        , 'SemanticScholarBot'
        , 'Jetslide'
        , 'scalaj-http'
        , 'XoviBot'
        , 'sysomos.com'
        , 'PocketParser'
        , 'newspaper'
        , 'serpstatbot'
        , 'MetaJobBot'
        , 'SeznamBot/3.2'
        , 'VelenPublicWebCrawler/1.0'
        , 'WordPress.com mShots'
        , 'adscanner'
        , 'BacklinkCrawler'
        , 'netEstate NE Crawler'
        , 'Astute SRM'
        , 'GigablastOpenSource/1.0'
        , 'DomainStatsBot'
        , 'Winds: Open Source RSS & Podcast'
        , 'dlvr.it'
        , 'BehloolBot'
        , '7Siters'
        );
        foreach($bots as $b)
        {
            if( stripos( $sistema, $b ) !== false ) return true;
        }
        return false;
    }


    public function uploadExtension($file) {
        if (!$file['error']) {
            $type  = $file['type'];
            switch ($type) {
                case 'image/jpeg':
                    $ext = 'jpg';
                    break;
                case 'image/png':
                    $ext = 'png';
                    break;
                case 'image/gif':
                    $ext = 'gif';
                    break;
                default :
                    $ext = '';
                    break;
            }
            return $ext;
        }
        return '';
    }



    public function beautifulPrice($price,$nice = true) {
        if ($price > 200000) {
            $temp = (int)($price / 1000);
        }
        else {
            $temp = ceil($price / 1000);
        }
        $temp = (string) $temp;
        if ($price > 200000 && $nice == true) {
            $temp[strlen($temp) - 1] = 9;
        }
        $price = (int) $temp * 1000;
        return $price;
    }

    public function beautifulPricev2($price) {
        $temp = ceil($price / 1000);
        $price = $temp * 1000;
        return $price;
    }

    public function uploadImage($file,$dir,$width='') {
        if (!$file['error']) {
            $tmp_name = $file['tmp_name'];
            $type  = $file['type'];
            $filename  = $file['name'];
            $path = substr($dir,0,strrpos($dir,'/'));
            if (in_array($type, array('image/jpeg','image/png'))) {
                $size = getimagesize($tmp_name);
                if ($width and $size[0] > $width) {
                    $newdir = "mini-game/temp/";
                    if(!is_dir($newdir)){
                        mkdir($newdir, 0755);
                    }
                    $newfile = $newdir.$filename;
                    move_uploaded_file($tmp_name,$newfile);

                    if(!is_dir($path)){
                        mkdir($path, 0755);
                    }
                    Business_Helpers_Image::getInstance()->resizeImage($newfile, $dir, $width, 9999, 0);
                    unlink($newfile);
                }
                else {
                    if(!is_dir($path)){
                        mkdir($path, 0755);
                    }
                    move_uploaded_file($tmp_name,$dir);
                }
                return array('error'=>0, 'link'=>$dir);
            }
            else {
                return array('error'=>1, 'msg'=>'File không phải định dạng hình ảnh');
            }
        }
        return array('error'=>1, 'msg'=>'Không tìm thấy file');
    }









    public function getToken() {
        $_token = new Zend_Session_Namespace('general_token');
        $token = md5(date('Y-m-d H:i:s'));

        $_token->token[$token] = true;
        $tokens = $_token->token;
        $count = count($tokens);
        $limit = 20;
        $newTokens = $tokens;
        if ($count > $limit) {
            foreach ($tokens as $key => $value) {
                if ($count > $limit) {
                    unset($newTokens[$key]);
                } else {
                    break;
                }
                $count--;
            }
        }
        $_token->token = $newTokens;
        return $token;
    }

    public function checkToken($token) {
        $_token = new Zend_Session_Namespace('general_token');
        if ($token == '' || !isset($_token->token) || empty($_token->token)) {
            return false;
        }
        $tokens = array_keys($_token->token);
        if (in_array($token,$tokens)) {
            return true;
        }
        else {
            return false;
        }
    }

    public function deleteToken($token) {
        $_token = new Zend_Session_Namespace('general_token');
        if($this->checkToken($token)) {
            unset($_token->token[$token]);
            return true;
        }
        return false;
    }





    public function numberFormatPrice($price) {
        return number_format($price,0,',','.');
    }

    public function checkEmail($email) {
        return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) ? FALSE : TRUE;
    }




    public function convertImageWebp($params) {
        $file_dir = ROOT_PATH.'/www/'.$params['file'];
        if (!isset($params['file']) || !file_exists($file_dir)) {
            return false;
        }
        $size = getimagesize($file_dir);
        if (!$size || !in_array($size['mime'],array('image/gif','image/png','image/jpeg'))) {
            return false;
        }
        $image = Globals::getStaticUrl().'/'.$params['file'].'?v='.time();
        $dir = $params['new_file'];
        $url = 'http://api.hnammobile.com/webp.php?url='.$image;
        $content = @file_get_contents($url);
        if ($content) {
            file_put_contents($dir, $content);
            return $dir;
        }
    }



    public function randomCode($char = '', $lengCode = 0, $numCode = 0)
    {
        $result = '';
        $size   = strlen($char);
        for ($i = 0; $i < $numCode; $i++) {
            $code = '';
            for ($j = 0; $j < $lengCode; $j++) {
                $code .= $char[rand(0, $size - 1)];
            }
            $result .= $code . ' ';
        }
        $results = substr($result, 0, -1);
        return $results;
    }



    public function getListDefaultAppleXML($variable = 'sitemap_onstock') {
        $_variable = Business_Common_Variables::getInstance();
        $dsID = $_variable->variable_get($variable, '');
        $dsID = str_replace(' ','',$dsID);
        if ($dsID) {
            $newData = array_unique(array_filter(explode(',', $dsID)));
            if (!empty($newData)) {
                $dsID = implode(',',$newData);
            }
            else {
                $dsID = '';
            }
        }
        return $dsID;
    }

    function minify_html($input) {
        if(trim($input) === "") return $input;
        // Remove extra white-space(s) between HTML attribute(s)
        $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function($matches) {
            return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
        }, str_replace("\r", "", $input));
        // Minify inline CSS declaration(s)
        if(strpos($input, ' style=') !== false) {
            $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function($matches) {
                return '<' . $matches[1] . ' style=' . $matches[2] . $this->minify_css($matches[3]) . $matches[2];
            }, $input);
        }
        if(strpos($input, '</style>') !== false) {
            $input = preg_replace_callback('#<style(.*?)>(.*?)</style>#is', function($matches) {
                return '<style' . $matches[1] .'>'. $this->minify_css($matches[2]) . '</style>';
            }, $input);
        }
        if(strpos($input, '</script>') !== false) {
            $input = preg_replace_callback('#<script(.*?)>(.*?)</script>#is', function($matches) {
                return '<script' . $matches[1] .'>'. $this->minify_js($matches[2]) . '</script>';
            }, $input);
        }
        return preg_replace(
            array(
                // t = text
                // o = tag open
                // c = tag close
                // Keep important white-space(s) after self-closing HTML tag(s)
                '#<(img|input)(>| .*?>)#s',
                // Remove a line break and two or more white-space(s) between tag(s)
                '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
                '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
                '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
                '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
                '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
                // Remove HTML comment(s) except IE comment(s)
                '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
            ),
            array(
                '<$1$2</$1>',
                '$1$2$3',
                '$1$2$3',
                '$1$2$3$4$5',
                '$1$2$3$4$5$6$7',
                '$1$2$3',
                '<$1$2',
                '$1 ',
                '$1',
                ""
            ),
            $input);
    }

    function minify_css($css) {
        if(trim($css) === "") return $css;
        $css = preg_replace('/\/\*((?!\*\/).)*\*\//','',$css); // negative look ahead
        $css = preg_replace('/\s{2,}/',' ',$css);
        $css = preg_replace('/\s*([:;{}])\s*/','$1',$css);
        $css = preg_replace('/;}/','}',$css);
        return $css;
    }

    function minify_js($input) {
        if(trim($input) === "") return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                // Remove white-space(s) outside the string and regex
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                // Remove the last semicolon
                '#;+\}#',
                // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
                // --ibid. From `foo['bar']` to `foo.bar`
                '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
            ),
            array(
                '$1',
                '$1$2',
                '}',
                '$1$3',
                '$1.$3'
            ),
            $input);
    }


}