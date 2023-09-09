<?php

class Business_Helpers_News {

    private static $_instance = null;

    // module news to store

    function __construct() {
        
    }

    /**
     * get instance of Business_Helpers_News
     *
     * @return Business_Helpers_News
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Business_Helpers_News();
        }
        return self::$_instance;
    }

    public static function fixLinkNoFollow($content) {
        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        $trust = "hnammobile.com";
        if(preg_match_all("/$regexp/siU", $content, $matches)) {
            $url = $matches[0];
            foreach($url as $_url) {
                if (strpos($_url, $trust)===false) {
                    $new_url = str_replace("href", " rel=\"nofollow\" href", $_url);
                    $content = str_replace($_url, $new_url, $content);
                }
            }
        }
        return $content;
    }

}

?>
