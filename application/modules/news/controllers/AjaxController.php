<?php
class News_AjaxController extends Zend_Controller_Action
{
    private $_prefix_cache = 'News_AjaxController::';
    public function init()
    {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->noIndex = true;
    }



    function wrap($fontSize, $angle, $fontFace, $string, $width){

        $ret = "";

        $arr = explode(' ', $string);

        foreach ( $arr as $word ){
            $teststring = $ret.' '.$word;
            $testbox = imagettfbbox($fontSize, $angle, $fontFace, $teststring);
            if ( $testbox[2] > $width ){
                $ret.=($ret==""?"":"\n").$word;
            } else {
                $ret.=($ret==""?"":' ').$word;
            }
        }

        return $ret;
    }

    private function properText($text){

        // Convert UTF-8 string to HTML entities
        $text = mb_convert_encoding($text, 'HTML-ENTITIES',"UTF-8");
        // Convert HTML entities into ISO-8859-1
        $text = html_entity_decode($text,ENT_NOQUOTES, "ISO-8859-1");
        // Convert characters > 127 into their hexidecimal equivalents
        $out = "";
        for($i = 0; $i < strlen($text); $i++) {
            $letter = $text[$i];
            $num = ord($letter);
            if($num>127) {
                $out .= "&#$num;";
            } else {
                $out .=  $letter;
            }
        }

        return $out;

    }

    public function trackingAction() {
        $cookieValue = $_POST['cookieValue']; // Nhận giá trị cookieValue từ request
        $cookieObject = json_decode($cookieValue, true);

        $uid = $cookieObject['uid'];
        $currentBrowser = $cookieObject['currentBrowser'];
        $ipAddress = $cookieObject['ip'];
        $currentURL = $cookieObject['currentURL'];


        $currentTime = microtime(true);
        $formattedTime = date('Y-m-d H:i:s', $currentTime);
        $timeStamp = $formattedTime;
        
        $data = array(
                        'id' => $uid,
                        'browser' => $currentBrowser,
                        'ip' => $ipAddress,
                        'urlLink' => $currentURL,
                        'timestamp' => $timeStamp,
        );
    
        Business_Addon_General::getInstance()->insertDB("addon_user_trip",$data);
        $data_res;
        if ($uid != null && $currentBrowser != null && $ipAddress != null && $currentURL != null && $timeStamp != null ) {
            $data_res = array(
                "msg" => "Thành công",
                "uid" => $uid
            );
        } else {
            $data_res = array(
                "msg" => "Thất bại",
                "reloads"=>true
            );
        }
        
        
        echo json_encode($data_res);
        // $cookieValue = $this->_request->getParam('cookieValue');
        // $cookieData = json_decode($cookieValue);
        
        // $uid = $cookieData->uid;
        // $currentBrowser = $cookieData->currentBrowser;
        // $ip =  $cookieData->ip;
        // $currentURL = $cookieData->currentURL;
        // $currentTime = microtime(true);
        // $formattedTime = date('Y-m-d H:i:s', $currentTime);
        // $timeStamp = $formattedTime;

        
        // $data = array(
        //             'id' => $uid,
        //             'browser' => $currentBrowser,
        //             'ip' => $ip,
        //             'urlLink' => $currentURL,
        //             'timestamp' => $timeStamp,
                    
        //         );
        // Business_Addon_General::getInstance()->insertDB("addon_user_trip",$data);

        // echo "test";

    }

}