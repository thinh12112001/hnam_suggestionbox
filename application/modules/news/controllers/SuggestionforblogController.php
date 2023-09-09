<?php
class News_SuggestionforblogController extends Zend_Controller_Action
{
    public function init()
    {
        header("Access-Control-Allow-Credentials: true");
        // header("Access-Control-Allow-Headers: Content-Type");
        header("Access-Control-Allow-Headers: *");

        header("Access-Control-Allow-Origin: *");
        ini_set("display_errors", 1);

        // header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        // header('Access-Control-Max-Age: 1000');
        // header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
    }

    public function indexAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo ".";
        die();
    }

    public function gettopurlanditemidAction() {
        try {
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $getTopClicks = Business_Addon_Hnproducts::getInstance()->getTopClickBlogProduct();
            $getAllBlogUrl = Business_Addon_Hnproducts::getInstance()->getAllBlogUrl();
            // $getTop3Itemid = Business_Addon_Hnproducts::getInstance()->getTopItemidClickPerBlogUrl("https://www.hnammobile.com/tin-tuc/thu-thuat-ung-dung/app-hack-game.26028.html#quick-1-9-app-hack-game-android-2022?utm_source=hnam&utm_medium=blog&utm_campaign=hnam-suggestion");

            echo "<b>URL có nhiều lượt click nhất: </b><u>".  $getTopClicks[0]['originalUrl']."</u>";
            echo "<br>";
            echo "<b>Số lượt click: </b>".  $getTopClicks[0]['frequency'];
            echo "<br>";
            echo "------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------";
            echo "<br>";
            // echo "<b>Blog URL: </b>" . $getTop3Itemid[0]['originalUrl'] . "<br>";
            
            
            foreach ($getAllBlogUrl as $val) {
                $i =1;
                $getTop3Itemid = Business_Addon_Hnproducts::getInstance()->getTopItemidClickPerBlogUrl($val['blogUrl']);
                echo "<b>URL:</b> <u>". $val['blogUrl']. "</u><br>";
                foreach ($getTop3Itemid as $item) {
                    echo "<b>Top " .$i . " itemid: </b>" . $item['itemid'] . "<br>";
                    echo "<b>==>Số lượt click: </b>" . $item['frequency'] . "<br>";
                     // Add an empty line between each record for better readability
                    $i++;
                }
                echo "______________________________________________________________________________________________________________________________|";
                echo "<br>";
            }
            
            echo "<br>";
            
            
            die();

        } 
        catch(Exception $e){
        }
    }
    public function trackingtransactionAction() {
        try {
            $this->_helper->Layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            

            $uid = $this->_request->getParam('uid');
            $currentUrl = $this->_request->getParam('current_url');
            

            if (empty($uid)) {
                $uid = 0;
            }

            if (strpos($currentUrl, "https://www.hnammobile.com/gio-hang") !== false && $uid !== 0) {
                $checkUidExist = Business_Addon_Hnproducts::getInstance()->checkBLogClick($uid);
                if (!empty($checkUidExist)) {
                    Business_Addon_Hnproducts::getInstance()->updateTransactionTable($uid);
                }
            }

            die();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function trackingblogAction()
    {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        try {
            $data_return = array(
                "msg" => "Error",
            );
        $currentURL = $this->_request->getParam('current_url');
        $uid = $this->_request->getParam('uid');
        $itemid = $this->_request->getParam('itemIdValue');

        // if (empty($uid)) {
        //     $uid = 0;
        // }

        $updateUrlCount = Business_Addon_Hnproducts::getInstance()->updateUrlCount($itemid, $currentURL,$uid);

        } catch (Exception $e) {
            // Xử lý lỗi nếu có
        }
    }

    public function trackingcartAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        try {
                    // Thời gian DB tự động cập nhật
                    $data_return = array(
                        "msg"=>"Error",
                    );

                    $uid = $this->_request->getParam('uid');
                    $cartValue =$this->_request->getParam('cartValue');
                    $currentURL = $this->_request->getParam('currentURL');
                    
                    if (strpos($currentURL, "https://www.hnammobile.com/shopping-bag/quick-preview-cart?order_id") !== false && !empty($cartValue)) {
                        $checkUidExist = Business_Addon_Hnproducts::getInstance()->checkBLogClick($uid);
                        if (!empty($checkUidExist)) {
                            $updateCart = Business_Addon_Hnproducts::getInstance()->updateCart($uid, $cartValue);
                        }  
                    }
                    die();
            }
            catch(Exception $e){
                echo "";
                die();
            }
    }

    public function suggestionforblogAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        try {
                    // Thời gian DB tự động cập nhật
                    $data_return = array(
                        "msg"=>"Error",
                    );
                    $currentURL = $this->_request->getParam('current_url');
                    $uid = $this->_request->getParam('uid');

                    
                    if (strpos($currentURL, "https://www.hnammobile.com/tin-tuc") !== false) {
                        $substrings = array("apple", "samsung", "xiaomi", "nokia", "oppo", "galaxy", "iphone", "realme");
                        $found = false;
                        foreach ($substrings as $val) {
                            if (strpos($currentURL, $val) !== false) {
                                $found = true;
                                break;
                            }
                        }
                        if ($found) {
                            $keywords = array("apple", "samsung", "xiaomi", "nokia", "oppo");
                            $foundKeywords = array();
                            // Kiểm tra xem mỗi keyword có tồn tại trong $currentURL hay không
                            foreach ($keywords as $keyword) {
                                if (strpos($currentURL, $keyword) !== false) {
                                    $foundKeywords[] = $keyword;
                                } else if (strpos($currentURL, 'galaxy') !== false) {
                                    $foundKeywords[] = 'samsung';
                                } else if (strpos($currentURL, 'iphone') !== false) {
                                    $foundKeywords[] = 'apple';
                                } else if (strpos($currentURL, 'realme') !== false || strpos($currentURL, 'redmi') !== false) {
                                    $foundKeywords[] = 'xiaomi';
                                }
                            }
                            if (count($foundKeywords) > 1) {
                                $listItemIds = Business_Addon_Hnproducts::getInstance()->getItemTopThuongHieuNew($foundKeywords);
                            } else {
                                $listItemIds = Business_Addon_Hnproducts::getInstance()->getItemTopThuongHieu($foundKeywords[0]);
                            }
                        } 
                        else 
                        {
                            // check time
                            $currentDateTime = date("Y-m-d H:i:s");
                            $currentTime = strtotime($currentDateTime);
                            $targetTime = strtotime(date("Y-m-d") . " 23:00:00");
                            $targetTime2 = strtotime(date("Y-m-d") . " 23:59:00");
                            

                            // check is updated
                            $isUpdated = Business_Addon_Hnproducts::getInstance()->getIsUpdateStatus();
                            $isUpdated = $isUpdated[0]['isUpdated'];
                            

                            if ($currentTime >= $targetTime && $currentTime <= $targetTime2 &&  $isUpdated ==0) {
                            // if ($currentTime <= $targetTime) {
                            #region get Top Brand
                                    $topBrand = Business_Addon_Report::getInstance()->getCountingNumberForBrands();
                                    $topBrandGroup = $topBrand[0]['brand'];
                                    
                                    #region get Top Price
                                    $priceCounts = array(
                                        'Upper20M' => 0,
                                        'Under10M' => 0,
                                        '10mTo20M' => 0
                                    );
                                    $allIds = Business_Addon_Report::getInstance()->getAllItemid();
                                    $topPrice = Business_Addon_Report::getInstance()->getTopPrice($allIds);
                                    foreach ($topPrice as $row) {
                                        $id = $row['id'];
                                        $price = $row['price'];
                                        $count = isset($check[$id]) ? $check[$id] : 0;

                                        if (isset($priceCounts[$price])) {
                                            $priceCounts[$price] += $count;
                                        }
                                    }
                                    // arsort($priceCounts);
                                    // $topPriceGroup = $topPrice[0][]
                                    $rankPrice = 1;
                                    $topPriceGroup = "";
                                    foreach ($priceCounts as $price => $count) {
                                        if ($rankPrice ==1) {
                                            $topPriceGroup = $price;
                                            break;
                                        }
                                    }
                                    $updateTopTraffic = Business_Addon_Hnproducts::getInstance()->updateTopTraffic($topBrandGroup, $topPriceGroup);
                                                                    
                                    if ($updateTopTraffic !== NULL) {
                                        
                                        if (stripos($topPriceGroup,"Upper20M") !== false) {
                                            
                                            $listItemIds = Business_Addon_Report::getInstance()->getItemSuggestUpper20M($topBrandGroup);
                                        } else if (stripos($topPriceGroup,"Under10M") !== false) {
                                            
                                            $listItemIds = Business_Addon_Report::getInstance()->getItemSuggestUnder10M($topBrandGroup);
                                        } else {
                                            
                                            $listItemIds = Business_Addon_Report::getInstance()->getItemSuggestBetween10MAnd20M($topBrandGroup);
                                        }
                                    }
                                } 
                            else if ($currentTime >= $targetTime && $currentTime <= $targetTime2 &&  $isUpdated ==1){
                                // else if ($currentTime <= $targetTime &&  $isUpdated ==1){
                                    $topTraffic = Business_Addon_Hnproducts::getInstance()->getTopTraffic();
                                    $topPriceGroup = $topTraffic[0]['topPriceGroup'];
                                    $topBrandGroup = $topTraffic[0]['topBrandGroup'];

                                    if (stripos($topPriceGroup,"Upper20M") !== false) {
                                        $listItemIds = Business_Addon_Report::getInstance()->getItemSuggestUpper20M($topBrandGroup);
                                    } else if (stripos($topPriceGroup,"Under10M") !== false) {
                                        $listItemIds = Business_Addon_Report::getInstance()->getItemSuggestUnder10M($topBrandGroup);
                                    } else {
                                        $listItemIds = Business_Addon_Report::getInstance()->getItemSuggestBetween10MAnd20M($topBrandGroup);
                                    }
                                }
                            else {
                                
                                    // die();
                                    $topTraffic = Business_Addon_Hnproducts::getInstance()->getTopTraffic();
                                    $topPriceGroup = $topTraffic[0]['topPriceGroup'];
                                    $topBrandGroup = $topTraffic[0]['topBrandGroup'];

                                    
                                    
                                    if (stripos($topPriceGroup,"Upper20M") !== false) {
                                        $listItemIds = Business_Addon_Report::getInstance()->getItemSuggestUpper20M($topBrandGroup);
                                    } else if (stripos($topPriceGroup,"Under10M") !== false) {
                                        $listItemIds = Business_Addon_Report::getInstance()->getItemSuggestUnder10M($topBrandGroup);
                                    } else {
                                        $listItemIds = Business_Addon_Report::getInstance()->getItemSuggestBetween10MAnd20M($topBrandGroup);
                                    }
                                    
                                    // gán isUpdated = 0
                                    if ($isUpdated ==1) {
                                        $result = Business_Addon_Hnproducts::getInstance()->updateTopTrafficToZero();
                                    }

                                }
                        }
                        if (empty($listItemIds)) {
                            $html = "";
                        } else {
                            $numbers = array();
                            foreach ($listItemIds as $item) {
                                $numbers[] = $item['itemid'];
                            }
                            $result = implode(',', $numbers);  
                            $data = Business_Addon_Hnproducts::getInstance()->getItemDetails($result);
                            
                            $html = '<style>
                            div#hnam_internal_id {
                                width: 100%;
                                border-radius: 8px;
                                padding: 20px;
                                background: linear-gradient(to right, #fc5c7d, #6a82fb);
                            }
                            #hnam_internal_id .hnam_internal_row {
                                display: flex;
                                overflow-x: auto;
                                padding: 15px 0;
                            }
                            .hnam_internal_title {
                                font-size: 20px;
                                padding-bottom: 20px;
                                color: #fff;
                                font-weight: 500;
                            }
                            #hnam_internal_id .hnam_internal_product {
                                flex: 0 0 calc(33.33% - 10px);
                                max-width: calc(33.33% - 10px);
                                margin: 0 5px;
                                border: 1px solid #eee;
                                border-radius: 12px;
                                box-shadow: rgba(0,0,0,.15) 0 3px 3px 0;
                                position: relative;
                                width: 100%;
                                padding-right: 15px;
                                padding-left: 15px;
                                background: #ffffff;
                            }
                            #hnam_internal_id .hnam_internal_product_item {
                                padding-top: 1rem;
                                position: relative;
                            }
                            #hnam_internal_id .hnam_internal_product_item .hnam_internal_figure{
                                position: relative;
                            }
                            #hnam_internal_id .hnam_internal_product_item_deal-percent {
                                position: absolute;
                                top: 6px;
                                right: 0;
                                background: #F43636;
                                color: #FFF;
                                font-size: 12px;
                                padding: 2px 8px;
                                border-radius: 24px;
                            }
                            #hnam_internal_id .hnam_internal_product_item_hot-sale {
                                position: absolute;
                                left: 0;
                                bottom: -20px;
                                background: #329AFB;
                                color: #FFF;
                                padding: 2px 8px;
                                font-size: 12px;
                                border-radius: 24px;
                            }
                            .hnam_internal_product_item_price {
                                margin-bottom: 0;
                                font-size: 16px;
                                line-height: 26px;
                            }
                            .hnam_internal_product_item_price strong {
                                color: #F16225;
                            }
                            .hnam_internal_product_item_price strong.internal_vat {
                                color: #2f80ed;
                            }
                            .hnam_internal_product_item_price strong ins {
                                font-size: 11px;
                                color: #636b7b;
                                margin: 0 4px 0 0;
                                text-transform: uppercase;
                                text-decoration: none;
                            }
                            .hnam_internal_product_item_deal-percent-mb {
                                display: none;
                            }
                            .hnam_internal_product_item_price del {
                                font-size: 12px;
                                color: #B3B3B7;
                                margin-left: 0.25rem;
                            }
                            .hnam_internal_product_item_caption h3 {
                                font-size: 14px;
                                line-height: 23px;
                                color: #070707;
                                font-weight: 500;
                            }
                            .hnam_internal_product_item_caption h3 a{
                                color: inherit;
                                display: block;
                            }
                    
                            .hnam_internal_product_item_caption {
                                padding-top: 10px;
                            }
                    
                            img.hnam_internal_product_item_image {
                                margin-top: 8px;
                                transition: .3s ease-out;
                                -webkit-transition: .3s ease-out;
                                height: 180px;
                                width: 100%;
                                text-align: center;
                                object-fit: contain!important;
                            }
                            .hnam_internal_product_item:hover img.hnam_internal_product_item_image{
                                margin-top: 0;
                                margin-bottom: 8px;
                            }
                    
                            /* height */
                            .hnam_internal_row::-webkit-scrollbar {
                                height: 6px;
                            }
                    
                            /* Track */
                            .hnam_internal_row::-webkit-scrollbar-track {
                                background: #f1f1f1;
                            }
                    
                            /* Handle */
                            .hnam_internal_row::-webkit-scrollbar-thumb {
                                background: #888;
                            }
                            /* Handle on hover */
                            .hnam_internal_row::-webkit-scrollbar-thumb:hover {
                                background: #555;
                            }
                            @media only screen and (max-width: 576px) {
                                #hnam_internal_id .hnam_internal_product {
                                    flex: 0 0 calc(60% - 10px);
                                    max-width: 60%;
                                }
                                img.hnam_internal_product_item_image{
                                    height: 130px;
                                }
                            }        
            </style>';
            
            $html .= '<div id="hnam_internal_id">';
            $html .= '<div class="hnam_internal_title">';
            $html .= 'Gợi ý cho bạn';
            $html .= '</div>';
            $html .= '<div class="hnam_internal_row">';
            
            foreach ($data as $item) {
                $title = $item["title"];
                $images = "";
                $itemid = $item["itemid"];
                $links = "";
                // $tag = . "?utm_source=hnam&utm_medium=blog&utm_campaign=hnam-suggestion";
                if ($item["links"] !== "") {
                    $productlink = $item["links"];
                    $encodedProductLink = urlencode($productlink);
                    // if ($uid !== "") {
                        $links = $productlink;
                    // } else {
                    //     $links = "https://int.hnammobile.com/suggestionforblog/trackingblog?itemid=$itemid&productlink=$encodedProductLink";
                    // }
                    
                    // $links = "http://internal_hnammobile.com/suggestionforblog/trackingblog?itemid=$itemid&productlink=$encodedProductLink";
            
                    $currentUrlLink = $currentURL. "&itemid=$itemid" . "&utm_source=hnam&utm_medium=blog&utm_campaign=hnam-suggestion";
                    $encodedUrl = urlencode($currentUrlLink);
                    $links .= "?currentUrl=$encodedUrl";
            
                }
                
            
                $price = 0;
                if ($item["price"] != 0) {
                    $price = $item["price"];
                }
                
                $price = number_format($price, 0,".",".");
                $price_old = 0;
                $salesPercentage = 0;
                if ($item["price_old"] != 0) {
                    $price_old = $item["price_old"];
                    $price_old = number_format($price_old, 0,".",".");
                    $salesPercentage = (($price_old - $price) / $price_old) * 100;
                }
                
                if (isset($item["images"])){
                    $images = $item["images"];
                }
                
            
                $html .= '<div class="hnam_internal_product">';
                $html .= '<div class="hnam_internal_product_item">';
                $html .= '<figure class="hnam_internal_figure">';
                if ($links !== "") {
                    $html .= '<a rel="nofollow" href="'.$links . ' " title="' . $title . '">';
                } else {
                    $html .= '<a rel="nofollow" href="https://www.hnammobile.com/dien-thoai/samsung-galaxy-a73-a736-5g-128gb-ram-8gb-nguyen-seal-bao-hanh-12-thang.24908.html?itemid=24908" title="' . $title . '">';
                }
                
                $html .= '<picture>';
                $html .= '<source media="(max-width:576px)" srcset="https://stcv4.hnammobile.com/uploads/products/webp-home/6/3254501615-samsung-galaxy-a73-a736-128gb-ram-8gb.jpg-23896.webp" type="image/webp">';
                $html .= '<img src="' . $images . '" width="125" height="150" alt="' . $title . '" loading="lazy" class="hnam_internal_product_item_image">';
                $html .= '</picture>';
                // $html .= '<span class="hnam_internal_product_item_hot-sale" style="">Giá Rẻ Lắm</span>';
                if ($salesPercentage != 0) {
                    $html .= '<span class="hnam_internal_product_item_deal-percent">' . floor($salesPercentage) .'%</span>';
                }
            
                $html .= '</a>';
                $html .= '</figure>';
                $html .= '<div class="hnam_internal_product_item_caption">';
                $html .= '<div class="hnam_internal_product_item_price">';
                if ($price != 0) {
                    $html .= '<strong class=""><ins></ins>'. $price .'đ</strong>';
                }else {
                    $html .= '<strong class=""><ins></ins>Tạm thời hết hàng</strong>';
                }
                // $html .= '<strong class=""><ins></ins>'. $price .'đ</strong>';
                $html .= '<span class="hnam_internal_product_item_deal-percent-mb">-29%</span>';
                if ($price_old != 0) {
                    $html .= '<del>'. $price_old .'đ</del>';
                }
            
                $html .= '</div>';
                $html .= '<h3>';
                if ($links !== "") {
                    $html .= '<a class="" href="'.$links .'" title="' . $title . '">';
                } else {
                    $html .= '<a class="" href="https://www.hnammobile.com/dien-thoai/samsung-galaxy-a73-a736-5g-128gb-ram-8gb-nguyen-seal-bao-hanh-12-thang.24908.html?itemid=24908" title="' . $title . '">';
                }
                $html .= $title;
                $html .= '</a>';
                $html .= '</h3>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
            $html .= '</div>';
                        }
                        
        echo $html;
        die();
    }
    }
        catch(Exception $e){
            echo "";
            die();
        }
    }
}
