<?php

class Business_Common_Utils {

    static function redirect($url) {
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: ' . $url);
        exit;
    }
    
    static function fixEmptyTag($str) {
        $str = str_replace("span></span", "span>&nbsp;</span", $str);
        $str = str_replace("\"></span", "\">&nbsp;</span", $str);
        return $str;
    }
    
    static function getCurrentIP() {
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
                return $_SERVER["HTTP_X_FORWARDED_FOR"];

            if (isset($_SERVER["HTTP_CLIENT_IP"]))
                return $_SERVER["HTTP_CLIENT_IP"];

            return $_SERVER["REMOTE_ADDR"];
        }

        if (getenv('HTTP_X_FORWARDED_FOR'))
            return getenv('HTTP_X_FORWARDED_FOR');

        if (getenv('HTTP_CLIENT_IP'))
            return getenv('HTTP_CLIENT_IP');

        return getenv('REMOTE_ADDR');
    }

    static function secondsToDate($seconds) {
        if ($seconds == 0)
            return "(<b class='red'>0</b> ngày)";
        $one_day = 24 * 60 * 60;
        $day = (int) ($seconds / $one_day);
        $hours = (int) ( ($seconds % $one_day) / 3600);
        return "(<b class='red'>" . $day . "</b> ngày <b class='red'>" . $hours . "</b>h)";
    }

    static function parseInput($string, $length = 0) {
        $string = htmlspecialchars($string);
        $string = str_replace('\'', '&#39;', $string);
        $string = str_replace('"', '&quot;', $string);
        if ($length == 0)
            return $string;
        $aString = explode(" ", $string);
        return array_slice($aString, 0, $length);
    }

    static function getCurrentURL() {
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        $protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
        $protocol = 'https';
        return     $protocol ."://" . $host . $uri;
    }

    static function prepareDay() {
        $return = "";
        for ($i = 1; $i <= 31; $i++) {
            if ($i < 10) {
                $return .= "<option value='0" . $i . "'>0" . $i . "</option>";
            } else {
                $return .= "<option value='" . $i . "'>" . $i . "</option>";
            }
        }
        return $return;
    }

    static function prepareMonth() {
        $return = "";
        for ($i = 1; $i <= 12; $i++) {
            if ($i < 10) {
                $return .= "<option value='0" . $i . "'>0" . $i . "</option>";
            } else {
                $return .= "<option value='" . $i . "'>" . $i . "</option>";
            }
        }
        return $return;
    }

    static function prepareYear($range = 100) {
        $year = intval(date('Y'));

        $start = $year - $range;

        $return = "";
        for ($i = $start; $i < $year; $i++) {
            if ($i < 10) {
                $return .= "<option value='0" . $i . "'>0" . $i . "</option>";
            } else {
                $return .= "<option value='" . $i . "'>" . $i . "</option>";
            }
        }
        return $return;
    }

    static function adaptTitleLinkURLSEO($title) {
        return Business_Addon_General::getInstance()->slugString($title);
        /*$title = str_replace("  ", "", $title);
        $special = array(" ", "/", "\\", "?", "&", ",", "\"", "”", "“", "'", "%", "(", ")", ".", "!", "®", ":", "|", "{", "}", "[", "]");
        $title = str_replace($special, "-", $title);
        $title = self::removeTiengViet($title);
        $title = strtolower($title);
        if (is_numeric($title[strlen($title) - 1]))
            $title = $title . "-";
        
        $title = htmlentities($title, null, 'utf-8');
        $title = str_replace("&nbsp;", "-", $title);
        $title = html_entity_decode($title);
        return trim($title);*/
    }
    static function adaptDateToString($datetime) {  
        $ptime = strtotime($datetime);
        $etime = time() - $ptime;

        if ($etime < 1)
        {
            return '0 giây';
        }

        $a = array( 365 * 24 * 60 * 60  =>  'year',
                    30 * 24 * 60 * 60  =>  'month',
                        24 * 60 * 60  =>  'day',
                            60 * 60  =>  'hour',
                                    60  =>  'minute',
                                    1  =>  'second'
                    );
        $a_plural = array( 'year'   => 'năm',
                        'month'  => 'tháng',
                        'day'    => 'ngày',
                        'hour'   => 'giờ',
                        'minute' => 'phút',
                        'second' => 'giây'
                    );
        
        foreach ($a as $secs => $str)
        {
            $d = $etime / $secs;
            if ($d >= 1)
            {
                $r = round($d);
                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $a_plural[$str]) . ' trước';
            }
        }
    }
    
    
    static function adaptTextSEO($title) {
        $title = str_replace("  ", " ", $title);
        $special = array(" ", "/", "\\", "?", "&", ",", "\"", "”", "“", "'", "%", "(", ")", ".", "!", "®");
        $title = str_replace($special, "-", $title);
        $title = self::removeTiengViet($title);
        $title = strtolower($title);
        if (is_numeric($title[strlen($title) - 1]))
            $title = $title . "-";
        echo    $title ;
        return $title;
    }

    static function adaptTitleLinkURL($title) {
        $title = str_replace("-", "", $title);
        $title = str_replace("  ", " ", $title);
        $special = array(" ", "/", "\\", "?", "&", ",", "\"", "”", "“", "'", "(", ")", ".");
        $title = str_replace($special, "-", $title);
        $title = self::removeTiengViet($title);
        $title = strtolower($title) . '.html';
        return $title;
    }

    static function removeTiengViet($content) {
        $trans = array('ẹ'=>'e', 'à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẫ' => 'a', 'ẩ' => 'a', 'ậ' => 'a', 'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'à' => 'a', 'á' => 'a', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i', 'ơ' => 'o', 'ớ' => 'o', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o', 'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u', 'đ' => 'd', 'À' => 'A', 'Á' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'À' => 'A', 'Ẫ' => 'A', 'Ẩ' => 'A', 'Ậ' => 'A', 'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ô' => 'O', 'Ố' => 'O', 'Ồ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O',
            'Ê' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E', 'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O', 'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U', 'Đ' => 'D', 'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e', 'ẵ' => 'a', 'ẳ' => 'a',
            'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a', 'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i', 'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'ô', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o', 'đ' => 'd', 'Đ' => 'D', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y', 'Á' => 'A', 'À' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Ă' => 'A', 'Ắ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A', 'Ặ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A', 'É' => 'E', 'È' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E', 'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ư' => 'U', 'Ứ' => 'U', 'Ừ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U', 'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'Ó' => 'O', 'Ò' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O', 'Ô' => 'O', 'Ố' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O', 'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', 'ặ' => 'a', 'é' => 'e', 'ắ' => 'a', 'ế' => 'e', 'è' => 'e', 'ằ' => 'a', 'É' => 'E', '–' => '')
        ;
        $content = strtr($content, $trans); // chuoi da duoc bo dau
        return $content;
    }

    static function checkName($name) {
        $check=strtolower(trim($name));
        $array=array('100'=>'thắng','101'=>'thủy');  //==== check hinh avatar
        $result=11;
        foreach ($array as $key => $val)
        {  
            if (strpos($check,$val) !== false)
            {   $result=$key;
                break;
            }
        }

            return $result;
    }
    
    
    static function sendRequestByCurl($url, $param = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $output = curl_exec($ch);
        curl_close($ch);
        unset($ch);
        if ($output === false) {
            return "";
        }
        return $output;
    }
    static function sendEmailV4($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached = null, $mail_config, $cc, $bcc) {

        if ($mail_config == "used") {
            $mail_config = "smtp.gmail.com;587;khomaycu@hnammobile.com;bobo@abc@098";
        } else if ($mail_config == "mailgun") {
            $mail_config = "smtp.gmail.com;587;khomaycu@hnammobile.com;bobo@abc@098";
            
	} else {
            $mail_config = "smtp.gmail.com;587;saleonline@hnammobile.com;saleonline552015";
        }
        if ($replyto == "")
            $replyto = $from;

        $arr_config = explode(';', $mail_config);

        //$host = $arr_config[0] . ':' . $arr_config[1];

        $host = $arr_config[0];
        $port = $arr_config[1];

        $username = $arr_config[2];
        $password = $arr_config[3];
        try {
            if ($port == 25) {
                $config = array(
                    //				'ssl' => 'tls',
                    'auth' => 'login',
                    'username' => $username,
                    'password' => $password,
                    'port' => $port
                );
            } else {
                $config = array(
                    'ssl' => 'tls',
                    'auth' => 'login',
                    'username' => $username,
                    'password' => $password,
                    'port' => $port
                );
            }

            $transport = new Zend_Mail_Transport_Smtp($host, $config);

            $mail = new Zend_Mail('utf-8');
            $mail->setType(Zend_Mime::MULTIPART_RELATED);
            $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
            $mail->setReplyTo($replyto);
//			$mail->setBodyText(strip_tags($body_html));

            $mail->setFrom($from, $displayname);
            
            
            if(is_array($to))
            {
                foreach($to as $each_recipient){
                    $mail->addTo($each_recipient);
                }
            }
            else
            $mail->addTo($to);
            
            
            
	    if ($bcc != null) {

                $mail->addBcc($bcc);
	            
            }
            if ($cc != null) {
                $mail->addCc($cc);
            }
            $mail->setSubject($subject);
            $mail->setBodyHtml($body_html);

            //$mail->setBodyHtml($body_html);
            $mail->send($transport);
//		    pre($result);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }

        return "";
    }

    static function sendEmailCron($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached = null, $mail_config, $cc, $bcc) {

        if ($mail_config == "used") {
            $mail_config = "smtp.gmail.com;587;khomaycu@hnammobile.com;bobo@abc@098";
        } else if ($mail_config == "mailgun") {
            $mail_config = "smtp.pepipost.com;25;hnammobilepepi;Voo@voo199@1";
            //$mail_config = "smtp.gmail.com;587;khomaycu@hnammobile.com;bobo@abc@098";

        } else {
            $mail_config = "smtp.gmail.com;587;saleonline@hnammobile.com;saleonline552015";
        }
        if ($replyto == "")
            $replyto = $from;

        $arr_config = explode(';', $mail_config);

        //$host = $arr_config[0] . ':' . $arr_config[1];

        $host = $arr_config[0];
        $port = $arr_config[1];

        $username = $arr_config[2];
        $password = $arr_config[3];
        try {
            if ($port == 25) {
                $config = array(
                    //				'ssl' => 'tls',
                    'auth' => 'login',
                    'username' => $username,
                    'password' => $password,
                    'port' => $port
                );
            } else {
                $config = array(
                    'ssl' => 'tls',
                    'auth' => 'login',
                    'username' => $username,
                    'password' => $password,
                    'port' => $port
                );
            }

            $transport = new Zend_Mail_Transport_Smtp($host, $config);

            $mail = new Zend_Mail('utf-8');
            $mail->setType(Zend_Mime::MULTIPART_RELATED);
            $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
            $mail->setReplyTo($replyto);
//			$mail->setBodyText(strip_tags($body_html));

            $mail->setFrom($from, $displayname);


            if(is_array($to))
            {
                foreach($to as $each_recipient){
                    $mail->addTo($each_recipient);
                }
            }
            else
                $mail->addTo($to);



            if ($bcc != null) {

                $mail->addBcc($bcc);

            }
            if ($cc != null) {
                $mail->addCc($cc);
            }
            $mail->setSubject($subject);
            $mail->setBodyHtml($body_html);

            //$mail->setBodyHtml($body_html);
            $send = $mail->send($transport);
//		    pre($result);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }

        return "";
    }
    
    static function sendEmailV3($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached = null, $mail_config, $cc) {

        if ($mail_config == "used") {
            $mail_config = "smtp.gmail.com;587;khomaycu@hnammobile.com;bobo@abc@098";
        } else {
            $mail_config = "smtp.gmail.com;587;saleonline@hnammobile.com;saleonline552015";
        }
        
        if ($replyto == "")
            $replyto = $from;

        $arr_config = explode(';', $mail_config);

        //$host = $arr_config[0] . ':' . $arr_config[1];

        $host = $arr_config[0];
        $port = $arr_config[1];

        $username = $arr_config[2];
        $password = $arr_config[3];
        try {
            if ($port == 25) {
                $config = array(
                    //				'ssl' => 'tls',
                    'auth' => 'login',
                    'username' => $username,
                    'password' => $password,
                    'port' => $port
                );
            } else {
                $config = array(
                    'ssl' => 'tls',
                    'auth' => 'login',
                    'username' => $username,
                    'password' => $password,
                    'port' => $port
                );
            }

            $transport = new Zend_Mail_Transport_Smtp($host, $config);

            $mail = new Zend_Mail('utf-8');
            $mail->setType(Zend_Mime::MULTIPART_RELATED);
            $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
            $mail->setReplyTo($replyto);
//			$mail->setBodyText(strip_tags($body_html));

            $mail->setFrom($from, $displayname);
            $mail->addTo($to);
            if ($cc != null) {
                $mail->addCc($cc);
            }
            $mail->setSubject($subject);
            $mail->setBodyHtml($body_html);

        
            
            
            //$mail->setBodyHtml($body_html);
            $mail->send($transport);
//		    pre($result);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }

        return "";
    }

    static function sendEmail($from, $displayname, $replyto, $to, $subject, $body_html, $file_attached = null, $mail_config) {
        //nghidv amazon
        if ($mail_config == null)
            $mail_config = "email-smtp.us-east-1.amazonaws.com;587;AKIAJDCE4FESLO52ENHA;ApQc+MZCEyw0crfoN9hOgLOalbq6hn9RnvutGwIKU+Xd";


        if ($replyto == "")
            $replyto = $from;

        $arr_config = explode(';', $mail_config);

        //$host = $arr_config[0] . ':' . $arr_config[1];

        $host = $arr_config[0];
        $port = $arr_config[1];

        $username = $arr_config[2];
        $password = $arr_config[3];

        try {
            if ($port == 25) {
                $config = array(
                    //				'ssl' => 'tls',
                    'auth' => 'login',
                    'username' => $username,
                    'password' => $password,
                    'port' => $port
                );
            } else {
                $config = array(
                    'ssl' => 'tls',
                    'auth' => 'login',
                    'username' => $username,
                    'password' => $password,
                    'port' => $port
                );
            }

            $transport = new Zend_Mail_Transport_Smtp($host, $config);

            $mail = new Zend_Mail('utf-8');
            $mail->setType(Zend_Mime::MULTIPART_RELATED);
            //$mail->addHeader("List-Unsubscribe: <mailto:hotro@easymail.vn>");
            //$mail->addHeader("Return-Path: <mailto:hotro@easymail.vn>");
            $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);

            $mail->setReplyTo($replyto);
            //			$mail->setBodyText(strip_tags($body_html));

            $mail->setFrom($from, $displayname);
            $mail->addTo($to);
            $mail->setSubject($subject);
            $mail->setBodyHtml($body_html);

            //$mail->setBodyHtml($body_html);
            $mail->send($transport);
            //		pre($result);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }

        return "";
    }

    static function uppercase($name) {
        $result = strtoupper($name);
        return $result;
    }

    static function curPageURL($port = 0) {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if($port){
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        elseif ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /*
     * phuoc 16-3-2020 loi https nên copy ra
     */
    static function curPageURLp($port = 0) {
        $pageURL = 'https';
//        if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {
//            $pageURL .= "s";
//        }
        $pageURL .= "://";
        if($port){
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        elseif ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
    /*
     *
     */

    static function generateRandomWord($length = 6) {
        $list = 'ABCDEFGHIJKLMNPQRST123456789';

        $rndWord = "";
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($list) - 1);
            $rndWord .= $list{$index};
        }
        return $rndWord;
    }

    static function generateRandomNumber($length = 6) {
        $list = '0123456789';

        $rndWord = "";
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($list) - 1);
            $rndWord .= $list{$index};
        }
        return $rndWord;
    }   
    
    public static function getContentByCurlSession($url) {
        try {            
            $strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';  
            $curlHandle = curl_init(); // init curl
            curl_setopt($curlHandle, CURLOPT_URL, $url); // set the url to fetch
            curl_setopt($curlHandle, CURLOPT_HEADER, 0);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_TIMEOUT, 300);
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt( $curlHandle, CURLOPT_COOKIE, $strCookie ); 
            $content = curl_exec($curlHandle);
            curl_close($curlHandle);
        } catch (Exception $ex) {
            return "";
        }

        return $content;
    }


    public static function getContentByCurl($url) {
        try {
            $curlHandle = curl_init(); // init curl
            curl_setopt($curlHandle, CURLOPT_URL, $url); // set the url to fetch
            curl_setopt($curlHandle, CURLOPT_HEADER, 0);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_TIMEOUT, 300);
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
            $content = curl_exec($curlHandle);
            $status = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
            curl_close($curlHandle);
        } catch (Exception $ex) {
            return "";
        }

        return $content;
    }

    public static function getContentByCurlv2($url) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $content = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } catch (Exception $ex) {
            return 404;
        }

        return $status;
    }

    public static function getFileContent($path) {
        if ($path != null) {
            try {
                $ret = file_get_contents($path);
                return $ret;
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
        return null;
    }

    public static function getEmailTemplate($templatename) {
        if ($templatename != null) {
            $path = APPLICATION_PATH . "/templates/" . $templatename . ".html";
            return self::getFileContent($path);
        }
    }

    public static function waterMark($image_source) {
        $waterMarkImage = BASE_PATH . Globals::getConfig('watermark');

        if (is_file($image_source)) {
            exec("composite -dissolve 50 -gravity NorthEast $waterMarkImage $image_source $image_source", $result = array());
        }
    }

    static function convertDateTime($date, $time = false) {
        if ($time)
            $_time = "H:i:s"; else
            $_time = '';
        $date = date('d-m-Y ' . $_time, strtotime($date));
        return $date;
    }

    static function shortPrice($price) {
        $limit = 1000000;
        if ($price == 0)
            return 0;
        if ($price < $limit)
            return ($price / 1000) . " ngàn";
        return ($price / 1000000) . " triệu";
    }

    static function shorten($text, $length = 50) {
        if ($text == null)
            return "";
        $text = explode(" ", $text);
        return implode(" ", array_slice($text, 0, $length)) . "...";
    }

    static function isValidEmail($email) {
        if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
            return true;
        }
        return false;
    }

    static function getDateBefore($mySQLDate, $day) {
        return date('Y-m-d', strtotime($date . ' -' . $day . ' day'));
    }

    static function sendMG($subject, $displayName, $fromEmail, $toEmail, $html) {
        $domain = "ezm-system.com";
        $url = "http:/api.mailgun.net/v2/$domain/messages";
        $username = 'api';
        $password = '';
// create a new cURL resource
        $myRequest = curl_init($url);
        $data["from"] = "$displayName <$fromEmail>";
        $data["to"] = $toEmail;
        $data["subject"] = $subject;
        $data["html"] = $html;
        foreach ($data as $k => $v) {
            $arr[] = $k . "=" . urlencode($v);
        }
        $datas = implode("&", $arr);
// do a POST request, using application/x-www-form-urlencoded type
        curl_setopt($myRequest, CURLOPT_POST, TRUE);
// credentials
        curl_setopt($myRequest, CURLOPT_USERPWD, "$username:$password");
// returns the response instead of displaying it
        curl_setopt($myRequest, CURLOPT_RETURNTRANSFER, 1);

//merge data
        curl_setopt($myRequest, CURLOPT_POSTFIELDS, $datas);

// do request, the response text is available in $response
        $response = curl_exec($myRequest);
    }

    public function GetdataCurl($url,$data)
    {

        $ch = curl_init();
//        $data = json_encode($data);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (isset($_REQUEST['f']) && $_REQUEST['f']==10) {
            var_dump($status);
            var_dump($result);
        }
        curl_close($ch);
//        if ($status == 200) {
//            $data = json_decode($result);
//            $result = $data;
//        }
//        else {
//            $result = false;
//        }
        return $result;
    }
    public static function getContentByCurlGoogle($url) {
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch,CURLOPT_TIMEOUT,10);
            $content = curl_exec($ch);
        } catch (Exception $ex) {
            return "";
        }
        return $content;
    }

}



?>