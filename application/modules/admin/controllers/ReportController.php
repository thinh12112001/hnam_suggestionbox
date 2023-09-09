<?php
/* TODO: Add code here */
class Admin_ReportController extends Zend_Controller_Action
{
    private $menu = 'menu_booking';
    private $_identity;

    public function init()
    {
        ini_set('display_errors', '1');
        BlockManager::setLayout('hnamtemplatecontent');
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        if(!is_null($identity) and count($identity) != 0) {
            $fullname = $identity->fullname?$identity->fullname:$identity->username;
            $this->view->fullname = $fullname;
        }else{
            $this->_redirect('/admin/home/login');
        }
        $this->_identity = (array) $auth->getIdentity();
        $this->view->menu_active = "report";
    }

    public function indexAction() {
        // $data_pageshow= $this->_bookingList;
        // $this->view->data_pageshow=$data_pageshow;
    }

    public function reportAction() // thống kê thgian khoảng bao nhiêu lâu thì URL này đc user truy cập
    {
        $result = Business_Addon_Report::getInstance()->getMostFrequentUrlAccess();
        
        echo "<u>Top 3 URL có nhiều lượt truy cập trong ngày nhất</u>: <br>";
        for ($i =0; $i <3;$i++) {
            echo  $result[$i]['urlLink']. ": ";
                echo  $result[$i]['count'] . " lượt truy cập";
                echo "<br>";
        }
        echo "<br>";
            $array = Business_Addon_Report::getInstance()->getAllUrlLink();
            
            $Oppo = array();
            $Apple = array();
            $Xiaomi = array();
            $Nokia = array();
            $Samsung = array();

            foreach ($array as $item) {
                $url = $item['urlLink'];

                if (strpos($url, 'https://www.hnammobile.com/dien-thoai/oppo') === 0) {
                    $Oppo[] = $url;
                }
                else if (strpos($url, 'https://www.hnammobile.com/dien-thoai/apple') === 0) {
                    $Apple[] = $url;
                }
                else if (strpos($url, 'https://www.hnammobile.com/dien-thoai/samsung') === 0) {
                    $Samsung[] = $url;
                }
                else if (strpos($url, 'https://www.hnammobile.com/dien-thoai/nokia') === 0) {
                    $Nokia[] = $url;
                }
                else {
                    $Xiaomi[] = $url;
                
                }
            }
            $OppoTimeStamp = Business_Addon_Report::getInstance()->getTimeStampAvereageAndNumberAccess('https://www.hnammobile.com/dien-thoai/oppo'); 
                echo "<u>Thống kê tổng lượng truy cập và thời gian trung bình mà User truy cập vào từng thương hiệu </u><br>";
                echo "OPPO: ";
                echo $averageMinutesOppo = $OppoTimeStamp['averageMinutes'] . " phút ";
                echo $averageSecondsOppo = $OppoTimeStamp['averageSeconds'] . " giây /";
                echo $numberOfAccessOppo = $OppoTimeStamp['numberOfAccess'] . " tổng lượt truy cập";
                echo "<br>";

            $AppleTimeStamp = Business_Addon_Report::getInstance()->getTimeStampAvereageAndNumberAccess('https://www.hnammobile.com/dien-thoai/apple'); 
            echo "APPLE: ";
                echo  $averageMinutesApple = $AppleTimeStamp['averageMinutes'] . " phút ";
                echo $averageSecondsApple = $AppleTimeStamp['averageSeconds'] . " giây /";
                echo $numberOfAccessApple = $AppleTimeStamp['numberOfAccess'] . " tổng lượt truy cập";
                echo "<br>";

            $SamsungTimeStamp = Business_Addon_Report::getInstance()->getTimeStampAvereageAndNumberAccess('https://www.hnammobile.com/dien-thoai/samsung'); 
            echo "Samsung: ";
                echo $averageMinutesSamsung = $SamsungTimeStamp['averageMinutes'] . " phút ";
                echo $averageSecondsSamsung = $SamsungTimeStamp['averageSeconds'] . " giây /";
                echo $numberOfAccessSamsung = $SamsungTimeStamp['numberOfAccess'] . " tổng lượt truy cập";
                echo "<br>";

            $XiaomiTimeStamp = Business_Addon_Report::getInstance()->getTimeStampAvereageAndNumberAccess('https://www.hnammobile.com/dien-thoai/xiaomi');
            echo "Xiaomi: ";
                echo $averageMinutesXiaomi = $XiaomiTimeStamp['averageMinutes'] . " phút ";
                echo $averageSecondsXiaomi = $XiaomiTimeStamp['averageSeconds'] . " giây /";
                echo $numberOfAccessXiaomi = $XiaomiTimeStamp['numberOfAccess'] . " tổng lượt truy cập";
                echo "<br>"; 

            $NokiaTimeStamp = Business_Addon_Report::getInstance()->getTimeStampAvereageAndNumberAccess('https://www.hnammobile.com/dien-thoai/nokia'); 
            echo "Nokia: ";
                echo $averageMinutesNokia = $NokiaTimeStamp['averageMinutes'] . " phút ";
                echo $averageSecondsNokia = $NokiaTimeStamp['averageSeconds'] . " giây /";
                echo $numberOfAccessNokia = $NokiaTimeStamp['numberOfAccess'] . " tổng lượt truy cập";
                
                echo "<br>";
                echo "<br>";
                echo "--------------------------------------------------------------------------------------------------------------------------------------------------------";
                echo "<br>";
                echo "<br>";
                
                $uid = 	30733281;
            
                echo "<u>Thống kê hành vi của user có UID</u>: ". $uid. "<br>";
                
                $listIds = Business_Addon_Report::getInstance()->getListItemByUid($uid); // chi tiết
                // print_r($listIds);
                // die();
                $priceArray = Business_Addon_Report::getInstance()->getMostFrequentPriceGroup($listIds);

                $groupPriceUpper10m = 0;
                $groupPriceUnder10m = 0;
                if ($priceArray !== "") {
                    foreach($priceArray as $value) {
                        if  ($value['price'] > 10000000) {
                            $groupPriceUpper10m +=1;
                        } else {
                            $groupPriceUnder10m +=1;
                        }
                        // print_r("<u> Giá</u>: " . $value['price']."đ");
                        // echo "<br>";
                    }
                    $groupPrice = ($groupPriceUpper10m > $groupPriceUnder10m) ? $groupPriceUpper10m : $groupPriceUnder10m;
                    echo "Số sản phẩm chi tiết user đã xem nằm trong nhóm giá lớn hơn 10M là: " . $groupPriceUpper10m . "<br>"; 
                    echo "Số sản phẩm chi tiết user đã xem nằm trong nhóm giá nhỏ hơn 10M là: " . $groupPriceUnder10m. "<br>"; 
                    
                    echo "<br>";
                }
                
                
        
                $test = Business_Addon_Report::getInstance()->getMostFrequentThuongHieu($uid); // thương hiệu
        
                if (!empty($listIds) && !empty($listIds[0])) { // nếu không rỗng => xem trang chi tiết
                    $count = count($listIds); // đếm bao nhiêu lần vào trang chi tiết
                    echo "<u>Số lần vào xem trang chi tiết</u>: ". $count. "<br>";
                    foreach ($test as $value) {
                        echo "Brand: ". $value['brand'] ;
                        echo " ". $value['count'] . "<br>";
                    }
                    // $result = Business_Addon_Hnproducts::getInstance()->callGetSuggestionObjectsByThuongHieuChipPin($listIds);
                    // $result = $result[0]['randomObjects'];                 
                }
                // die();
            die();
    }

    public function reportByUidAction() {
        // die();
        $uid = 290638401;
        
        echo "<u>UID</u>: ". $uid. "<br>";
        
        $listIds = Business_Addon_Report::getInstance()->getListItemByUid($uid); // chi tiết
        // print_r($listIds);
        // die();
        $priceArray = Business_Addon_Report::getInstance()->getMostFrequentPriceGroup($listIds);
        $groupPriceUpper10m = 0;
        $groupPriceUnder10m = 0;
        foreach($priceArray as $value) {
            if  ($value['price'] > 10000000) {
                $groupPriceUpper10m +=1;
            } else {
                $groupPriceUnder10m +=1;
            }
            print_r("<u> Giá</u>: " . $value['price']."đ");
            echo "<br>";
        }
        echo "<br>";
        $groupPrice = ($groupPriceUpper10m > $groupPriceUnder10m) ? $groupPriceUpper10m : $groupPriceUnder10m;
        echo "Số sản phẩm chi tiết user đã xem nằm trong nhóm giá lớn hơn 10m là: " . $groupPriceUpper10m . "<br>"; 
        echo "Số sản phẩm chi tiết user đã xem nằm trong nhóm giá nhỏ hơn 10m là: " . $groupPriceUnder10m. "<br>"; 
        
        echo "<br>";
        
 
        $test = Business_Addon_Report::getInstance()->getMostFrequentThuongHieu($uid); // thương hiệu

        if (!empty($listIds) && !empty($listIds[0])) { // nếu không rỗng => xem trang chi tiết
            $count = count($listIds); // đếm bao nhiêu lần vào trang chi tiết
            echo "Số lần vào xem trang chi tiết: ". $count. "<br>";
            foreach ($test as $value) {
                echo "Brand: ". $value['brand'] ;
                echo " ". $value['count'] . "<br>";
            }
            // $result = Business_Addon_Hnproducts::getInstance()->callGetSuggestionObjectsByThuongHieuChipPin($listIds);
            // $result = $result[0]['randomObjects'];                 
        }
        die();
    
    }


}

