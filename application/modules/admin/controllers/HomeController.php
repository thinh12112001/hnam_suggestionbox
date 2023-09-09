<?php
/* TODO: Add code here */
class Admin_HomeController extends Zend_Controller_Action
{
    private $menu = 'menu_news';
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
            if ($this->_request->getParam('action')!="login"){
                $this->_redirect('/admin/home/login');
            }
        }
        $this->_identity = (array) $auth->getIdentity();
        $this->view->menu_active = "products";
    }

    public function indexAction() {

    }


//    products
    public function top5chipAction() {
        $this->view->menu_sub_active = "list_chip";
//        css datatables
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css");
//js datatables
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables/jquery.dataTables.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/jszip/jszip.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/pdfmake.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/vfs_fonts.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.html5.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.print.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.colVis.min.js");
        $this->view->inlineScript()->appendFile("/admin/js/products.js?v=".Globals::getVersion());

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

        foreach ($topChipAndPin as $row) {
            $id = $row['id'];
            $chip = $row['chip'];
            $count = isset($check[$id]) ? $check[$id] : 0;
        
            // echo "ID: $id, Chip: $chip, Pin: $pin, Count: $count <br>";

            if (isset($chipCounts[$chip])) {
                $chipCounts[$chip] += $count;
            }
        
        }
        //Top Chip
        arsort($chipCounts);
        

        $this->view->listCate = $chipCounts;
    }

    public function top5pinAction() {
        $this->view->menu_sub_active = "list_pin";
//        css datatables
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css");
//js datatables
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables/jquery.dataTables.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/jszip/jszip.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/pdfmake.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/vfs_fonts.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.html5.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.print.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.colVis.min.js");
        $this->view->inlineScript()->appendFile("/admin/js/products.js?v=".Globals::getVersion());
        

        $allIds = Business_Addon_Report::getInstance()->getAllItemid();
        $topChipAndPin = Business_Addon_Report::getInstance()->getTopChipPin($allIds);
        #region get full Itemid, chip, pin, count. 
        $check = Business_Addon_Report::getInstance()->getAllUrlLinkFromDetailedProductsAccesing();
        
        $pinCounts = array(
            '<5000' => 0,
            '>5000' => 0
        );
        foreach ($topChipAndPin as $row) {
            $id = $row['id'];
            $pin = $row['pin'];
            $count = isset($check[$id]) ? $check[$id] : 0;
            // Đếm số lượng pin tương ứng
            if (isset($pinCounts[$pin])) {
                $pinCounts[$pin] += $count;
            }
        }

        //Top Chip
        arsort($pinCounts);
        

        $this->view->listCate = $pinCounts;
    }
    

    public function top5priceAction() {
        $this->view->menu_sub_active = "list_price";
//        css datatables
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css");
//js datatables
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables/jquery.dataTables.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/jszip/jszip.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/pdfmake.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/vfs_fonts.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.html5.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.print.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.colVis.min.js");
        $this->view->inlineScript()->appendFile("/admin/js/products.js?v=".Globals::getVersion());
       

        $allIds = Business_Addon_Report::getInstance()->getAllItemid();
        $check = Business_Addon_Report::getInstance()->getAllUrlLinkFromDetailedProductsAccesing();

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
        arsort($priceCounts);

        $this->view->listCate = $priceCounts;
    }

    public function top5brandAction() {
        $this->view->menu_sub_active = "list_brand";
//        css datatables
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css");
//js datatables
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables/jquery.dataTables.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/jszip/jszip.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/pdfmake.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/vfs_fonts.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.html5.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.print.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.colVis.min.js");
        $this->view->inlineScript()->appendFile("/admin/js/products.js?v=".Globals::getVersion());
       

        $topBrand = Business_Addon_Report::getInstance()->getCountingNumberForBrands();
        
        $this->view->listCate = $topBrand;
    }

    public function topblogurlAction() {
        $this->view->menu_sub_active = "list_click";
//        css datatables
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css");
//js datatables
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables/jquery.dataTables.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/jszip/jszip.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/pdfmake.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/vfs_fonts.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.html5.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.print.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.colVis.min.js");
        $this->view->inlineScript()->appendFile("/admin/js/products.js?v=".Globals::getVersion());
        

        $getAllBlogUrl = Business_Addon_Hnproducts::getInstance()->getAllBlogProductClickAndUrl();
            // print_r($getAllBlogUrl);
            // die();

        $this->view->listCate = $getAllBlogUrl;
    }

    public function topitemidperurlAction() {

    }

    public function blogurlperuidAction() {

    }

//    ajax paging list product
    public function ajaxListProductsAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $_general = Business_Addon_General::getInstance();
        $draw = 0;
        $row = 0;
        if (isset($_POST['start'])){
            $row = $_POST['start'];
        }
        if (isset($_POST['draw'])){
            $draw = $_POST['draw'];
        }

        if (isset($_POST['length'])){
            $rowperpage = $_POST['length']; // Rows display per page
        }
        $columnIndex = 0;
        $columnName = "";
        if (isset($_POST['order'][0]['column'])){
            $columnIndex = $_POST['order'][0]['column']; // Column index
            $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
        }
        if (isset($_POST['order'][0]['dir'])){
            $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
        }
        if (isset($_POST['search']['value'])){
            $searchValue = addslashes($_POST['search']['value']); // Search value
        }





        $search = trim($this->_request->getParam("search",""));
        $status = (int)trim($this->_request->getParam("status",-1));
        $parent_id= (int)trim($this->_request->getParam("parent_id",0));




        $searchQuery = " ";
//        if($searchValue != ''){
//            $searchQuery .= " and (emp_name like '%".$searchValue."%' or email like '%".$searchValue."%' or city like'%".$searchValue."%' ) ";
//        }

        $where_search = " 1=1 ";
        if ($search){
            $where_search .= " AND title like '%$search%'";
        }
        if ($parent_id){
            $where_search .= " AND parent_id = '{$parent_id}'";
        }
        if ($status != -1){
            $where_search .= " AND enabled = '{$status}'";
        }



        $sql_count = "SELECT count(*) FROM addon_products where {$where_search}";
        $list_count = $_general->excuteCodev2($sql_count);
        $totalRecordwithFilter = $totalRecords = (int)$list_count[0]['count(*)'];

        $page = trim($this->_request->getParam("page",""));
        if ($page=="all"){
            $limit = "";
        }else{
            $limit = " LIMIT {$row},{$rowperpage}";
        }

        $columnName = str_replace("p_","",$columnName);
        if ($columnIndex==0){
            $order = "order by id DESC";
        }else{
            $order = " order by ".$columnName." ".$columnSortOrder;
        }

        $sql_list = "SELECT * FROM addon_products where {$where_search} {$order} {$limit} ";

        $list = $_general->excuteCodev2($sql_list);

        $data = array();

        if ($list){
            $listParentId = implode(",",array_column($list,'parent_id'));
            $listParent = Business_Addon_Cate::getInstance()->getListById($listParentId);
            $titleParentId = array();
            if ($listParent){
                foreach ($listParent as $key=>$val){
                    $titleParentId[$val['id']] = $val['title'];
                }
            }
            $stt=$row+1;
            foreach ($list as $val){
                $image = "";
                if ($val['images']){
                    $image = '<img src="'.Globals::getBaseUrl().$val['images'].'" width="80" height="80" style="height:auto">';
                }
                $title = '<a href="/admin/home/edit?id='.$val['id'].'" title="'.$val['title'].'">'.$val['title'].'</a>';
                if ($page=="all"){
                    if ($val['enabled']==1){
                        $status = "Hiển thị";
                    }else{
                        $status = "Tắt";
                    }
                    $title = $val['title'];
                }else{
                    if ($val['enabled']==1){

                        $status = '<input onchange="changeStatus('.$val["id"].',\''.md5("NewCenruryAbcdqwerProducts".$val['id']).'\',0)" class="status-changes" data-token="'.md5("LoyaltyAdminHNamAbcdqwerProducts".$val['id']).'" data-id="'.$val['id'].'" type="checkbox" id="status'.$val['id'].'" name="status'.$val['id'].'" value="1" checked data-bootstrap-switch>';
                    }else{
                        $status='<input onchange="changeStatus('.$val["id"].',\''.md5("NewCenruryAbcdqwerProducts".$val['id']).'\',1)" class="status-changes" type="checkbox" data-token="'.md5("LoyaltyAdminHNamAbcdqwerProducts".$val['id']).'" data-id="'.$val['id'].'" id="status'.$val['id'].'" name="status'.$val['id'].'" value="1" data-bootstrap-switch>';
                    }
                }
                $date_created = "";
                if ($val['created']){
                    $date_created = date("H:i:s d-m-Y",strtotime($val['created']));
                }
                $name_cate = "";
                if (isset($titleParentId[$val['parent_id']])){
                    $name_cate = $titleParentId[$val['parent_id']];
                }
                $data[] = array(
                    "stt"=>$stt,
                    "p_title"=>$title,
                    "p_parent_id"=>$name_cate,
                    "p_price"=>number_format($val['price'])."đ",
                    "p_created"=>$date_created,
                    "p_enabled"=>$status,
                );
                $stt++;
            }
        }


        if ($page=="all"){
            $response = array(
                "data"=>$data
            );
        }else{
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "aaData" => $data
            );
        }
        echo json_encode($response);
    }

    public function changeStatusProductsAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        if(!is_null($identity) and count($identity) != 0) {
            $id  = $this->_request->getParam('id');
            $type  = (int)$this->_request->getParam('type');
            $token  = $this->_request->getParam('token');
            $__ztoken = md5("NewCenruryAbcdqwerProducts".$id);
            if ($token != $__ztoken){
                echo json_encode(array('msg' => "Dữ liệu không đúng. Vui lòng thử lại",'reloads' => true));
                die();
            }

            $detail_products = Business_Addon_Products::getInstance()->getDetail($id);
            if (!$detail_products){
                echo json_encode(array('msg' => "Dữ liệu không đúng. Vui lòng thử lại",'reloads' => true));
                die();
            }

            $data_update = array(
                "userid_update"=> $identity->userid,
                "enabled"=> $type,
            );

            try{
                Business_Addon_Products::getInstance()->updateDB('addon_products',$data_update,'id='.$id);
                echo json_encode(array('msg' => "Cập nhật thành công"));
                die();
            }catch (Exception $e){
                echo json_encode(array('msg' => "Có lỗi xảy ra. Vui lòng thử lại",'reloads' => true));
                die();
            }

        }else{
            echo json_encode(array('msg' => "Vui lòng đăng nhập",'redirect' => '/admin/home/logout'));
            die();
        }
    }

    public function editCateProductAction(){
        $this->view->menu_sub_active = "cate_products";
        $this->view->inlineScript()->appendFile("/admin/js/products.js?v=".Globals::getVersion());
        $__cate  = Business_Addon_Cate::getInstance();
        $list_cate = $__cate->getListCateGroup(1,false);

        $id  = (int)$this->_request->getParam('id');
        $detail = array();
        if($id){
            $detail = $__cate->getDetail($id);
        }
        $this->view->detail = $detail;
        $this->view->listCate = $list_cate;
        // echo "<pre>";
        // var_dump($list_cate);
        // die();
        $this->view->token = Business_Addon_General::getInstance()->getToken();
    }
    public function top3urlAction(){
        $this->view->menu_sub_active = "cate_products";
//        css datatables
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css");
//js datatables
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables/jquery.dataTables.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/jszip/jszip.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/pdfmake.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/vfs_fonts.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.html5.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.print.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.colVis.min.js");
        $this->view->inlineScript()->appendFile("/admin/js/products.js?v=".Globals::getVersion());

        //report
        $result = Business_Addon_Report::getInstance()->getMostFrequentUrlAccess();
        
        $this->view->listCate = $result;

    }

    public function toptransactionAction() {
        $this->view->menu_sub_active = "transactions";
//        css datatables
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css");
//js datatables
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables/jquery.dataTables.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/jszip/jszip.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/pdfmake.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/vfs_fonts.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.html5.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.print.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.colVis.min.js");
        $this->view->inlineScript()->appendFile("/admin/js/products.js?v=".Globals::getVersion());

        // get top transaction
        $result = Business_Addon_Report::getInstance()->getTopTransaction();

        $this->view->listCate = $result;
        
    }

    public function topsuccesspaymentAction() {
        $this->view->menu_sub_active = "success_payment";
//        css datatables
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css");
        $this->view->headLink()->appendStylesheet("/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css");
//js datatables
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables/jquery.dataTables.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/jszip/jszip.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/pdfmake.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/pdfmake/vfs_fonts.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.html5.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.print.min.js");
        $this->view->inlineScript()->appendFile("/admin/plugins/datatables-buttons/js/buttons.colVis.min.js");
        $this->view->inlineScript()->appendFile("/admin/js/products.js?v=".Globals::getVersion());

        //getTopSuccessPayment
        $result = Business_Addon_Report::getInstance()->getTopSuccessPayment();

        $this->view->listCate = $result;

    }

    public function cartdetailbyuidAction() {
        
    }
    
    public function changeStatusCateAction(){
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        if(!is_null($identity) and count($identity) != 0) {
            $id  = $this->_request->getParam('id');
            $type  = (int)$this->_request->getParam('type');
            $token  = $this->_request->getParam('token');
            $__ztoken = md5("NewCenruryAbcdqwerCate".$id);
            if ($token != $__ztoken){
                echo json_encode(array('msg' => "Dữ liệu không đúng. Vui lòng thử lại",'reloads' => true));
                die();
            }

            $detail_products = Business_Addon_Cate::getInstance()->getDetail($id);
            if (!$detail_products){
                echo json_encode(array('msg' => "Dữ liệu không đúng. Vui lòng thử lại",'reloads' => true));
                die();
            }

            $data_update = array(
                "userid_update"=> $identity->userid,
                "enabled"=> $type,
            );

            try{
                Business_Addon_Products::getInstance()->updateDB('addon_cate',$data_update,'id='.$id);
                echo json_encode(array('msg' => "Cập nhật thành công"));
                die();
            }catch (Exception $e){
                echo json_encode(array('msg' => "Có lỗi xảy ra. Vui lòng thử lại",'reloads' => true));
                die();
            }

        }else{
            echo json_encode(array('msg' => "Vui lòng đăng nhập",'redirect' => '/admin/home/logout'));
            die();
        }
    }
    public function editAction(){
        $this->view->menu_sub_active = "list_products";
        $this->view->headLink()->appendStylesheet("/admin/plugins/summernote/summernote-bs4.min.css");
        $this->view->inlineScript()->appendFile("/admin/plugins/summernote/summernote-bs4.min.js?v=".Globals::getVersion());
        $this->view->inlineScript()->appendFile("/admin/js/products.js?v=".Globals::getVersion());
        $id = (int)$this->_request->getParam("id");
        $detail = array();
        if ($id){
            $__products = Business_Addon_Products::getInstance();
            $detail = $__products->getDetail($id);
        }
        $__cate = Business_Addon_Cate::getInstance();
        $list_cate = $__cate->getListCateGroup(1,false);
        $this->view->listCate = $list_cate;
        $this->view->detail = $detail;
        $this->view->token = Business_Addon_General::getInstance()->getToken();

    }

// end products



    private function checkExt($file) {
        $file = explode(".", $file);
        $last = strtolower($file[count($file) - 1]);
        if (in_array($last, array('jpg', 'bmp', 'gif', 'png')))
            return 1;
        return 0;
    }

    private function getFolderContent($dir) {
        $files = array();
        if (!is_dir($dir))
            return null;
        $dh = opendir($dir);
        while (($file = readdir($dh)) !== false) {
            if ($file != "." && $file != "..")
                $files[] = $file;
        }
        closedir($dh);
        return $files;
    }

    

    public function loginAction() {
        $this->_helper->Layout()->disableLayout();
        $request = $this->getRequest();
        if($request->isPost()) {
            $registry = Zend_Registry::getInstance();
            $auth = Zend_Auth::getInstance();
            $db = Globals::getDbConnection('maindb');
            $authAdapter = new Zend_Auth_Adapter_DbTable($db);
            $authAdapter->setTableName('zfw_users')
                ->setIdentityColumn('username')
                ->setCredentialColumn('password');
            $uname = $request->getParam('username');
            $paswd = $request->getParam('password');
            $authAdapter->setIdentity($uname);
            $authAdapter->setCredential(md5($paswd));
            $result = $auth->authenticate($authAdapter);
            if($result->isValid()) {
                $data = $authAdapter->getResultRowObject(null, 'password');
                $auth->getStorage()->write($data);
                $identity = $auth->getIdentity();

                //set timeout login
                $authns = new Zend_Session_Namespace('Zend_Auth');
                $seconds=60 * 60 * 8 ;
                Zend_Session::rememberMe($seconds);
                Zend_Session::setOptions(array(
                    'use_only_cookies' => 'on',
                    'remember_me_seconds' => $seconds
                ));
                $authns->setExpirationSeconds($seconds);  //set 8 tieng login

                setcookie('uname', urlencode($identity->username), time() + 1 * 60 * 60, '/', 'https://int.hnammobile.com/');
                setcookie('token', urlencode(md5($identity->username."ASDQWEZXCHNAM!@#")), time() + 1 * 60 * 60, '/', 'https://int.hnammobile.com/');

                if(in_array($identity->idregency,array(51)) || $identity->content_manager){
                    $this->_redirect('/admin/home');
                }
                else {
                    $this->_redirect('/admin/home/logout');
                }
            }
            else {
                $this->_redirect('/admin/home/login');
            }
        }
    }

    public function logoutAction() {
        $this->_helper->Layout()->disableLayout();
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_redirect('/admin/home/login');
        die();
    }



    private function buildPrefixImg($news_module='')
    {
        $prefix_img = '';
        return sprintf($prefix_img, $news_module) . rand(0, 10000000000);
    }

    private function createThumbnail($image_name, $new_width, $new_height, $uploadDir, $moveToDir)
    {
        $path = $uploadDir . '/' . $image_name;

        $mime = getimagesize($path);

        if ($mime['mime'] == 'image/png') {$src_img = imagecreatefrompng($path);}
        if ($mime['mime'] == 'image/jpg') {$src_img = imagecreatefromjpeg($path);}
        if ($mime['mime'] == 'image/jpeg') {$src_img = imagecreatefromjpeg($path);}
        if ($mime['mime'] == 'image/pjpeg') {$src_img = imagecreatefromjpeg($path);}
        if ($mime['mime'] == 'image/gif') {$src_img = imagecreatefromgif($path);}

        $old_x = imageSX($src_img);
        $old_y = imageSY($src_img);

        if ($old_x > $old_y) {
            $thumb_w = $new_width;
            $thumb_h = $old_y * ($new_height / $old_x);
        }

        if ($old_x < $old_y) {
            $thumb_w = $old_x * ($new_width / $old_y);
            $thumb_h = $new_height;
        }

        if ($old_x == $old_y) {
            $thumb_w = $new_width;
            $thumb_h = $new_height;
        }

        $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);

        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
        $backgroundColor = imagecolorallocate($dst_img, 255, 255, 255);
        imagefill($dst_img, 0, 0, $backgroundColor);

        // New save location
        $new_thumb_loc = $moveToDir . $image_name;

        if ($mime['mime'] == 'image/png') {$result = imagepng($dst_img, $new_thumb_loc, 7);}
        if ($mime['mime'] == 'image/jpg') {$result = imagejpeg($dst_img, $new_thumb_loc, 70);}
        if ($mime['mime'] == 'image/jpeg') {$result = imagejpeg($dst_img, $new_thumb_loc, 70);}
        if ($mime['mime'] == 'image/pjpeg') {$result = imagejpeg($dst_img, $new_thumb_loc, 70);}
        if ($mime['mime'] == 'image/gif') {$result = imagegif($dst_img, $new_thumb_loc, 70);}
        imagedestroy($dst_img);
        imagedestroy($src_img);

        return $result;
    }
}