<?php
class News_SuggestionController extends Zend_Controller_Action
{
    public function init()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        ini_set("display_errors", 1);
    }

    public function indexAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        echo ".";
        die();
    }

    

    public function suggestionAction(){
        try {
//        No render layout
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
//
        $uid = $this->_request->getParam('uid');
        // $currentURL = $this->_request->getParam('url_tracking'); 
        $ip = $_SERVER['REMOTE_ADDR'];
        // Thời gian DB tự động cập nhật
        $data_return = array(
            "msg"=>"Error",
        );
        $checkExistsPrice = false;

            // $uid = 280637225;
            // Mốc giá để phân nhóm
            $checkPrice = 10000000;
            $listIds = Business_Addon_Hnproducts::getInstance()->getListItemByUid($uid);
            
            $countingAllUrl = Business_Addon_Hnproducts::getInstance()->getListUrlByUid($uid);
            
            if (!empty($listIds) && !empty($listIds[0] && count($listIds) >= (count($countingAllUrl) /2)) && count($listIds) < 40) { //xem trang chi tiết nhiều hơn thương hiệu  
                
                $getTopBrand = Business_Addon_Hnproducts::getInstance()->getTopBrandForListIds($listIds);
                $priceArray = Business_Addon_Hnproducts::getInstance()->getMostFrequentPriceGroup($listIds,$checkPrice);
                    // thuộc nhóm trên 10 triệu
                    
                    if ($priceArray  === "upper10M") {
                        $subresult = Business_Addon_Hnproducts::getInstance()->getRandomObjectsByBrandAndPriceUpper10M($getTopBrand, $listIds);
                        $itemIds = [];
                        foreach ($subresult as $row) {
                            $itemIds[] = $row['itemid'];
                        }
                        $result = implode(',', $itemIds);
        
                        $checkExistsPrice = true;
                    } 
                    // thuộc nhóm dưới 10 triệu
                    else if ($priceArray  === "under10M"){   
                        $subresult = Business_Addon_Hnproducts::getInstance()->getRandomObjectsByBrandAndPriceUnder10M($getTopBrand, $listIds);
                        $itemIds = [];
                        foreach ($subresult as $row) {
                            $itemIds[] = $row['itemid'];
                        }
                        $result = implode(',', $itemIds);
                        
                        $checkExistsPrice = true;
                    } else if ($priceArray  === "error"){
                        // die();
                    }
            } 

            if ($checkExistsPrice == false) { // nếu rỗng là chỉ xem danh mục hoặc chưa xem gì
    
                    $listIds = Business_Addon_Hnproducts::getInstance()->getListUrlByUid($uid);
                    $apple = 0;
                    $samsung = 0;
                    $oppo = 0;
                    $nokia = 0;
                    $xiaomi = 0;
                    foreach ($listIds as $id) {
                        if (stripos($id['urlLink'],"apple")) {
                            $apple+=1;
                        }
                        else if (stripos($id['urlLink'],"samsung")) {
                            $samsung+=1;
                        }
                        else if (stripos($id['urlLink'],"oppo")) {
                            $oppo+=1;
                        }
                        else if (stripos($id['urlLink'],"nokia")) {
                            $nokia+=1;
                        }
                        else if (stripos($id['urlLink'],"xiaomi")) {
                            $xiaomi+=1;
                        }
                    }
                    $thuongHieu = array(
                        'apple' => $apple,
                        'samsung' => $samsung,
                        'oppo' => $oppo,
                        'nokia' => $nokia,
                        'xiaomi' => $xiaomi
                    );
                    $mostFrequentString = "error";
                    
                    $maxValue = max($thuongHieu); // Tìm giá trị lớn nhất trong mảng
                    // Tìm tên thương hiệu tương ứng với giá trị lớn nhất
                    if ($maxValue != 0) {
                        $mostFrequentString = array_search($maxValue, $thuongHieu);
                    }

                        switch ($mostFrequentString) {
                            case (strpos($mostFrequentString,"apple")) :
                                $listItemIds = Business_Addon_Hnproducts::getInstance()->getItemByThuongHieu('Apple');
                                break;
                            case (strpos($mostFrequentString,"iphone")) :
                                $listItemIds = Business_Addon_Hnproducts::getInstance()->getItemByThuongHieu('Apple');
                                break;   
                            case (strpos($mostFrequentString,"samsung")) :
                                $listItemIds = Business_Addon_Hnproducts::getInstance()->getItemByThuongHieu('Samsung');
                                break;
                            case (strpos($mostFrequentString,"galaxy")) :
                                $listItemIds = Business_Addon_Hnproducts::getInstance()->getItemByThuongHieu('Samsung');
                                break;
                            case (strpos($mostFrequentString,"xiaomi")) :
                                $listItemIds = Business_Addon_Hnproducts::getInstance()->getItemByThuongHieu('Xiaomi');
                                break;
                            case (strpos($mostFrequentString,"oppo")) :
                                $listItemIds = Business_Addon_Hnproducts::getInstance()->getItemByThuongHieu('Oppo');
                                break;
                            case (strpos($mostFrequentString,"nokia")) :
                                $listItemIds = Business_Addon_Hnproducts::getInstance()->getItemByThuongHieu('Nokia');

                                // if(count($listItemIds) < 5) {
                                //     //chuyển từ array sang string
                                //     $arr = [];
                                //     foreach ($listItemIds as $row) {
                                //         $arr[] = $row['itemid'];
                                //     }

                                //     $stringNokiaIds = implode(',', $arr);

                                //     $limitNum = 5 - count($listItemIds);
                                //     $lastItemId = end($listItemIds);
                                //     $lastItemId = $lastItemId['itemid'];
                                //     // print_r($lastItemId);
                                //     // die();
                                //         // tạo hàm lấy N giá trị ngẫu nhiên theo nhóm giá và itemid cuối 
                                //         $subresult = Business_Addon_Hnproducts::getInstance()->getLastItemIdsMissing($stringNokiaIds,$lastItemId,$limitNum);
                                //         print_r($subresult);
                                //         die();

                                //         $itemIds = [];
                                //         foreach ($subresult as $item) {
                                //             $itemIds[] = $item["itemid"];
                                //         }
                                //         $subresult = implode(",", $itemIds);
                                        
                                // }

                                // die();
                                break;
                            default:
                            
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
                                $rankPrice = 1;
                                $topPriceGroup = "";
                                foreach ($priceCounts as $price => $count) {
                                    if ($rankPrice ==1) {
                                        $topPriceGroup = $price;
                                        break;
                                    }
                                }
                                
                                if (stripos($topPriceGroup,"Upper20M") !== false) {
                                    $listItemIds = Business_Addon_Report::getInstance()->getItemSuggestUpper20M($topBrandGroup);
                                } else if (stripos($topPriceGroup,"Under10M") !== false) {
                                    $listItemIds = Business_Addon_Report::getInstance()->getItemSuggestUnder10M($topBrandGroup);
                                } else {
                                    $listItemIds = Business_Addon_Report::getInstance()->getItemSuggestBetween10MAnd20M($topBrandGroup);
                                }
                                break;
                        }                    
                     
                    // ghép thành chuỗi 5 itemid
                    $numbers = array();
                        foreach ($listItemIds as $item) {
                            $numbers[] = $item['itemid'];
                        }
                        $result = implode(',', $numbers);  
                
                     
            }

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
    $links =$item["links"] . "?utm_source=hnam&utm_medium=blog&utm_campaign=hnam-suggestion";
    
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
        $html .= '<a rel="nofollow" href="https://www.hnammobile.com/dien-thoai/samsung-galaxy-a73-a736-5g-128gb-ram-8gb-nguyen-seal-bao-hanh-12-thang.24908.html" title="' . $title . '">';
    }
    // $html .= '<a rel="nofollow" href="https://www.hnammobile.com/dien-thoai/samsung-galaxy-a73-a736-5g-128gb-ram-8gb-nguyen-seal-bao-hanh-12-thang.24908.html" title="' . $title . '">';
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
    $html .= '<a class="" href="https://www.hnammobile.com/dien-thoai/samsung-galaxy-a73-a736-5g-128gb-ram-8gb-nguyen-seal-bao-hanh-12-thang.24908.html" title="' . $title . '">';
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
        echo $html;
        die();
            }
            catch (Exception $e){
                echo "";
                die();
            }        
    }
}