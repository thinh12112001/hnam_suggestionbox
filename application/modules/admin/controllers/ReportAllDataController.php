<?php
/* TODO: Add code here */
class Admin_ReportAllDataController extends Zend_Controller_Action
{
    private $menu = 'reportAllData';
    private $_identity;
    // private $_bookingList = array(
    //     "1"=> "Phòng 1",
    //     "2"=> "Phòng 2",
    // );

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
        $this->view->menu_active = "booking";
    }

    public function indexAction() {
        // $data_pageshow= $this->_bookingList;
        // $this->view->data_pageshow=$data_pageshow;
    }

    public function reportAction() // thống kê thgian khoảng bao nhiêu lâu thì URL này đc user truy cập
    {
        // $uid = 290638401;
        // echo "<u>UID</u>: ". $uid. "<br>";
        
        $allIds = Business_Addon_Report::getInstance()->getAllItemid();
        

        $topChipAndPin = Business_Addon_Report::getInstance()->getTopChipPin($allIds);
        

        #region get full Itemid, chip, pin, count. 
        $check = Business_Addon_Report::getInstance()->getAllUrlLinkFromDetailedProductsAccesing();
        $chipCounts = array(
            'Apple' => 0,
            'Snapdragon' => 0,
            'MediaTek' => 0,
            'Helio' => 0,
            'Exynos' => 0
        );
        $pinCounts = array(
            '<5000' => 0,
            '>5000' => 0
        );
        foreach ($topChipAndPin as $row) {
            $id = $row['id'];
            $pin = $row['pin'];
            $chip = $row['chip'];
            $count = isset($check[$id]) ? $check[$id] : 0;
        
            // echo "ID: $id, Chip: $chip, Pin: $pin, Count: $count <br>";

            if (isset($chipCounts[$chip])) {
                $chipCounts[$chip] += $count;
            }
        
            // Đếm số lượng pin tương ứng
            if (isset($pinCounts[$pin])) {
                $pinCounts[$pin] += $count;
            }
        }
        //Top Chip
        arsort($chipCounts);
        
        echo "<br>";
        echo "<strong><u>Top Chip</u></strong>: <br>";
        $topChipGroup = "";
        $rankChip = 1;
        foreach ($chipCounts as $chip => $count) {
            if ($rankChip ==1) {
                $topChipGroup = $chip;
            }
            echo "Top $rankChip: $chip, Count: $count <br>";
            $rankChip++;
        }
        echo "<br>";
        arsort($pinCounts);
        $topPinGroup = "";
        // Top Pin
        echo "<strong><u>Top Pin</u></strong>: <br>";
        $rankPin = 1;
        foreach ($pinCounts as $pin => $count) {
            if ($rankPin ==1) {
                $topPinGroup = $pin;
            }
            echo "Top $rankPin: $pin, Count: $count <br>";
            $rankPin++;
        }
        #endregion 

        #region get Top Price
        $priceCounts = array(
            'Upper20M' => 0,
            'Under10M' => 0,
            '10mTo20M' => 0
        );
        $topPrice = Business_Addon_Report::getInstance()->getTopPrice($allIds);
        foreach ($topPrice as $row) {
            $id = $row['id'];
            $price = $row['price'];
            $count = isset($check[$id]) ? $check[$id] : 0;

            if (isset($priceCounts[$price])) {
                $priceCounts[$price] += $count;
            }
        }
        echo "<br>";
        arsort($priceCounts);
        // Top Price
        echo "<strong><u>Top Price</u></strong>: <br>";
        $rankPrice = 1;
        $topPriceGroup = "";
        foreach ($priceCounts as $price => $count) {
            if ($rankPrice ==1) {
                $topPriceGroup = $price;
            }
            echo "Top $rankPrice: $price, Count: $count <br>";
            $rankPrice++;
        }
        #end region

        

        $check = Business_Addon_Report::getInstance()->getAllUrlLinkFromDetailedProductsAccesing();
        $topBrand = Business_Addon_Report::getInstance()->getCountingNumberForBrands();
        
        echo "<br>";
        $topRankBrand = 1;
        $topBrandGroup = "";

        echo "<strong><u>Top 5 brand</u></strong>: <br>";
 
        foreach ($topBrand as $row) {
            if ($topRankBrand ==1) {
                $topBrandGroup = $row['brand'];
            }
            echo "Top $topRankBrand: ".$row['brand']. "-" . $row['count'] . "<br>";
            $topRankBrand++;
        }
        echo "<br>";
        echo "<br>";
        echo "Top 1 chip: ". $topChipGroup;
        echo "<br>";
        echo "Top 1 pin: ". $topPinGroup;
        echo "<br>";
        echo "Top 1 price: ". $topPriceGroup;
        echo "<br>";
        echo "Top 1 brand: ". $topBrandGroup;
        echo "<br>";
        echo "<br>";
        Business_Addon_Report::getInstance()->getItemForNewMembers($topPriceGroup,$topBrandGroup);
        die();

    }


}

