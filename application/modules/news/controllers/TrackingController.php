<?php
class News_TrackingController extends Zend_Controller_Action
{
    public function init()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
    }

    public function indexAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo ".";
        die();
    }

    public function trackingAction(){


//        No render layout
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
//
        $uid = $this->_request->getParam('uid');
        $ip = $_SERVER['REMOTE_ADDR'];
        $currentURL = $this->_request->getParam('url_tracking');
        $currentBrowser =  $this->_request->getParam('currentBrowser');        
        $fullContent = $this->_request->getParam('fullContent');
        // Thời gian DB tự động cập nhật
        $data_return = array(
            "msg"=>"Error",
        );
        
        if (strpos($currentURL,"https://www.hnammobile.com/dien-thoai")!==false) {
        // if (strpos($currentURL, "https://www.hnammobile.com/dien-thoai") !== false && preg_match('/([\d\s\w\-]+)/', $currentURL)) {
            $data = array(
                'uid' => (int)$uid,
                'browser'=> $currentBrowser,
                'ip' => $ip,
                'urlLink' => $currentURL,
                'created' => date("Y-m-d H:i:s"),
                // 'fullContent' => $fullContent,
//                'timestamp' => $timeStamp
            );
            try{
                Business_Addon_General::getInstance()->insertDB("addon_user_trip", $data);

                $data_return = array(
                    "msg"=>"Success",
                );
            }catch (Exception $e) {
                $data_return = array(
                    "msg" => "Lỗi: " . $e->getMessage(),
                );
            }

        }
//         else if (strpos($fullContent, 'snapdragon') !== false) {
//             $data = array(
//                 'uid' => (int)$uid,
//                 'browser'=> $currentBrowser,
//                 'ip' => $ip,
//                 'urlLink' => $currentURL,
//                 'created' => date("Y-m-d H:i:s"),
//                 'fullContent' => $fullContent,
// //                'timestamp' => $timeStamp
//             );
//             try{
//                 Business_Addon_General::getInstance()->insertDB("addon_user_trip", $data);

//                 $data_return = array(
//                     "msg"=>"Success",
//                 );
//             }catch (Exception $e) {
//                 $data_return = array(
//                     "msg" => "Lỗi: " . $e->getMessage(),
//                 );
//             }
//         }
        echo json_encode($data_return);
        die();
    }
}