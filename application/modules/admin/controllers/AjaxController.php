<?php
class Admin_AjaxController extends Zend_Controller_Action
{
    private $_identity;
    public function init()
    {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $auth = Zend_Auth::getInstance();
        $this->_identity = (array) $auth->getIdentity();
    }

    public function submitCateAction()
    {
        $id  = (int)$this->_request->getParam('id');
        $group_cate = (int)$this->_request->getParam('group_cate');
        $title  = addslashes($this->_request->getParam('title'));
        $token  = $this->_request->getParam('token');
        $parentId  = (int)$this->_request->getParam('parentId',0);
        $description  = addslashes($this->_request->getParam('description'));
        $myOrder  = (int)$this->_request->getParam('myOrder');
        $status  = (int)$this->_request->getParam('status');
        $metaTilte  = addslashes($this->_request->getParam('metaTilte'));
        $metaDescription  = addslashes($this->_request->getParam('metaDescription'));
        $_ztoken = Business_Addon_General::getInstance()->checkToken($token);
        try{
            if ($_ztoken){
                $userid = $this->_identity['userid'];
                if (!$title) {
                    echo json_encode(array('msg' => 'Vui lòng nhập tiêu đề', 'field' => 'inputName'));
                    die();
                }
                $slug = Business_Addon_General::getInstance()->slugString($title);
                $data = array(
                    'title' => $title,
                    'parent_id' => $parentId,
                    'group_cate' => $group_cate,
                    'meta_title' => $metaTilte,
                    'meta_description' => $metaDescription,
                    'link' => "",
                    'myorder' => $myOrder,
                    'description' => $description,
                    'userid' => $userid,

                    'enabled' => $status,
                );
                $img_name    = 'images';
                $image = null;
                $_general = Business_Addon_General::getInstance();

                $filename = rand(100000,999999).$_general->slugString($title).time();
                if (isset($_FILES[$img_name]) && $_FILES[$img_name]['size'] > 0) {
                    $uploads_dir = BASE_PATH . '/v2/images/uploads/cate/';
                    $tmp_name = $_FILES[$img_name]['tmp_name'];

                    $ext  = Business_Common_Images::get_image_extension($tmp_name);
                    $name = "social-{$filename}.{$ext}";
                    if (!is_dir($uploads_dir)) {
                        mkdir($uploads_dir, 0777, true);
                    }
                    Business_Helpers_Image::getInstance()->resizeImage($tmp_name, "$uploads_dir/$name", 180, 180, 1);

                    $image = 'v2/images/uploads/cate/'.$name;
                }
                if(isset($image)) {
                    $data['images'] = $image;
                }

                $img_name    = 'imagesSocal';
                $socialimage = null;
                $_general = Business_Addon_General::getInstance();

                $filename = rand(100000,999999).$_general->slugString($title).time();
                if (isset($_FILES[$img_name]) && $_FILES[$img_name]['size'] > 0) {
                    $uploads_dir = BASE_PATH . '/v2/images/uploads/cate/';
                    $tmp_name = $_FILES[$img_name]['tmp_name'];

                    $ext  = Business_Common_Images::get_image_extension($tmp_name);
                    $name = "social-{$filename}.{$ext}";
                    if (!is_dir($uploads_dir)) {
                        mkdir($uploads_dir, 0777, true);
                    }
                    Business_Helpers_Image::getInstance()->resizeImage($tmp_name, "$uploads_dir/$name", 180, 180, 1);

                    $socialimage = 'v2/images/uploads/cate/'.$name;
                }
                if(isset($socialimage)) {
                    $data['imagesSocial'] = $socialimage;
                }

                if ($id){
                    Business_Addon_General::getInstance()->updateDB("addon_cate",$data,"id = ".$id);
                    $data_res = array(
                        "msg" => "Sửa danh mục thành công",
                        "reloads"=>true
                    );
                }else{
                    $data['slug'] = $slug;
                    $lastID = Business_Addon_General::getInstance()->insertDB("addon_cate",$data);
                    $data_res = array(
                        "msg" => "Thêm bài danh mục thành công",
                        "url"=>"/admin/home/edit-cate-product?id=".$lastID
                    );
                    if($group_cate == 2){
                        $data_res = array(
                            "msg" => "Thêm bài danh mục thành công",
                            "url"=>"/admin/news/edit-cate?id=".$lastID
                        ); 
                    }
                }
                $cache = GlobalCache::getCacheInstance('ws');
                $cache->flushAll();

                echo json_encode($data_res);
                die();
            }else{
                $data_res = array(
                    "msg" => "Có lỗi xảy ra. Vui lòng thử lại.",
                    "reloads"=>true
                );
                echo json_encode($data_res);
                die();
            }
        }catch (Exception $e){
            $data_res = array(
                "msg" => "Có lỗi xảy ra. Vui lòng thử lại",
                "reloads"=>true
            );
            echo json_encode($data_res);
            die();
        }



    }
    public function submitBannerAction()
    {
        $id  = (int)$this->_request->getParam('id');
        $token  = $this->_request->getParam('token');
        $title  = addslashes($this->_request->getParam('title'));
        $subTitle  = addslashes($this->_request->getParam('subTitle'));
        $groupType  = (int)$this->_request->getParam('groupType',0);
        $myOrder  = (int)$this->_request->getParam('myOrder');
        $status  = (int)$this->_request->getParam('status');
        $_ztoken = Business_Addon_General::getInstance()->checkToken($token);
        try{
            if ($_ztoken){
                $userid = $this->_identity['userid'];
                if (!$title) {
                    echo json_encode(array('msg' => 'Vui lòng nhập tiêu đề', 'field' => 'inputName'));
                    die();
                }
                if(!$groupType){
                    echo json_encode(array('msg' => 'Vui lòng chọn trang hiển thị', 'field' => 'parentId'));
                    die();
                }
                if (!$id){
                    if(empty($_FILES['images']) || $_FILES['images']['size'] <= 0){
                        echo json_encode(array('msg' => 'Vui lòng thêm ảnh PC', 'field' => 'images'));
                        die();
                    }
                    if(empty($_FILES['imagesMb']) || $_FILES['imagesMb']['size'] <= 0){
                        echo json_encode(array('msg' => 'Vui lòng thêm ảnh MB', 'field' => 'imagesMb'));
                        die();
                    }
                }

                $data = array(
                    'title' => $title,
                    'subTitle' => $subTitle,
                    'group_type'=> $groupType,
                    'stt' => $myOrder,
                    'enabled' => $status,
                    'userid' => $userid,
                );
                $img_name    = 'images';
                $image = null;
                $_general = Business_Addon_General::getInstance();

                $filename = rand(100000,999999).$_general->slugString($title).time();
                if (isset($_FILES[$img_name]) && $_FILES[$img_name]['size'] > 0) {
                    $uploads_dir = BASE_PATH . '/v2/images/uploads/banner/';
                    $tmp_name = $_FILES[$img_name]['tmp_name'];

                    $ext  = Business_Common_Images::get_image_extension($tmp_name);
                    $name = "pc-{$filename}.{$ext}";
                    $name_webp = "pc-{$filename}.webp";
                    if (!is_dir($uploads_dir)) {
                        mkdir($uploads_dir, 0777, true);
                    }
                    Business_Helpers_Image::getInstance()->resizeImage($tmp_name, "$uploads_dir/$name",1920,1080,1);

                    $image = 'v2/images/uploads/banner/'.$name;
                    $data['images'] = $image;
                    if ($image){
                        $url = "https://api.hnammobile.com/webp-convert.php?url=".Globals::getStaticUrl().$image;
                        $webp = @file_get_contents($url);
                        if ($webp){

                            $filename = $uploads_dir = BASE_PATH . '/v2/images/uploads/banner/'.$name_webp;
                            file_put_contents($filename,$webp);
                            $data['images_webp'] = 'v2/images/uploads/banner/'.$name_webp;

                        }
                    }
                }

                $img_name    = 'imagesMb';
                $Mbimage = null;
                $_general = Business_Addon_General::getInstance();

                $filename = rand(100000,999999).$_general->slugString($title).time();
                if (isset($_FILES[$img_name]) && $_FILES[$img_name]['size'] > 0) {
                    $uploads_dir = BASE_PATH . '/v2/images/uploads/banner/';
                    $tmp_name = $_FILES[$img_name]['tmp_name'];

                    $ext  = Business_Common_Images::get_image_extension($tmp_name);
                    $name = "mb-{$filename}.{$ext}";
                    $name_webp = "mb-{$filename}.webp";
                    if (!is_dir($uploads_dir)) {
                        mkdir($uploads_dir, 0777, true);
                    }
                    Business_Helpers_Image::getInstance()->resizeImage($tmp_name, "$uploads_dir/$name", 600, 338, 1);

                    $Mbimage = 'v2/images/uploads/banner/'.$name;
                    $data['imagesMb'] = $Mbimage;
                    if ($Mbimage){
                        $url = "https://api.hnammobile.com/webp-convert.php?url=".Globals::getStaticUrl().$Mbimage;
                        $webp = @file_get_contents($url);
                        if ($webp){

                            $filename = $uploads_dir = BASE_PATH . '/v2/images/uploads/banner/'.$name_webp;
                            file_put_contents($filename,$webp);
                            $data['imagesMb_webp'] = 'v2/images/uploads/banner/'.$name_webp;

                        }
                    }
                }


                if ($id){
                    Business_Addon_General::getInstance()->updateDB("addon_banner",$data,"id = ".$id);
                    $data_res = array(
                        "msg" => "Sửa banner thành công",
                        "reloads"=>true
                    );
                }else{
                    $lastID = Business_Addon_General::getInstance()->insertDB("addon_banner",$data);
                    $data_res = array(
                        "msg" => "Thêm banner thành công",
                        "url"=>"/admin/banner/edit?id=".$lastID
                    );
                }
                $cache = GlobalCache::getCacheInstance('ws');
                $cache->flushAll();

                echo json_encode($data_res);
                die();
            }else{
                $data_res = array(
                    "msg" => "Có lỗi xảy ra. Vui lòng thử lại.",
                    "reloads"=>true
                );
                echo json_encode($data_res);
                die();
            }
        }catch (Exception $e){
            $data_res = array(
                "msg" => "Có lỗi xảy ra. Vui lòng thử lại",
                "reloads"=>true
            );
            echo json_encode($data_res);
            die();
        }



    }

    public function submitBookingAction()
    {
        $id  = (int)$this->_request->getParam('id');
        $token  = $this->_request->getParam('token');
        $customername  = addslashes($this->_request->getParam('customername'));
        $phone  = addslashes($this->_request->getParam('phone'));
        // $groupType  = (int)$this->_request->getParam('groupType',0);
        // $myOrder  = (int)$this->_request->getParam('myOrder');
        // $status  = (int)$this->_request->getParam('status');
        $_ztoken = Business_Addon_General::getInstance()->checkToken($token);
        try{
            if ($_ztoken){
                // $userid = $this->_identity['userid'];
                if (!$customername) {
                    echo json_encode(array('msg' => 'Vui lòng nhập tên khách hàng', 'field' => 'inputCustomername'));
                    die();
                }
                if(!$phone){
                    echo json_encode(array('msg' => 'Vui lòng nhập số điện thoại', 'field' => 'inputPhone'));
                    die();
                }
                // if (!$id){
                //     if(empty($_FILES['images']) || $_FILES['images']['size'] <= 0){
                //         echo json_encode(array('msg' => 'Vui lòng thêm ảnh PC', 'field' => 'images'));
                //         die();
                //     }
                //     if(empty($_FILES['imagesMb']) || $_FILES['imagesMb']['size'] <= 0){
                //         echo json_encode(array('msg' => 'Vui lòng thêm ảnh MB', 'field' => 'imagesMb'));
                //         die();
                //     }
                // }

                $data = array(
                    'customername' => $customername,
                    'phone' => $phone,
                    // 'group_type'=> $groupType,
                    // 'stt' => $myOrder,
                    // 'enabled' => $status,
                    // 'userid' => $userid,
                );
                // $img_name    = 'images';
                // $image = null;
                // $_general = Business_Addon_General::getInstance();

                // $filename = rand(100000,999999).$_general->slugString($title).time();
                // if (isset($_FILES[$img_name]) && $_FILES[$img_name]['size'] > 0) {
                //     $uploads_dir = BASE_PATH . '/v2/images/uploads/banner/';
                //     $tmp_name = $_FILES[$img_name]['tmp_name'];

                //     $ext  = Business_Common_Images::get_image_extension($tmp_name);
                //     $name = "pc-{$filename}.{$ext}";
                //     $name_webp = "pc-{$filename}.webp";
                //     if (!is_dir($uploads_dir)) {
                //         mkdir($uploads_dir, 0777, true);
                //     }
                //     Business_Helpers_Image::getInstance()->resizeImage($tmp_name, "$uploads_dir/$name",1920,1080,1);

                //     $image = 'v2/images/uploads/banner/'.$name;
                //     $data['images'] = $image;
                //     if ($image){
                //         $url = "https://api.hnammobile.com/webp-convert.php?url=".Globals::getStaticUrl().$image;
                //         $webp = @file_get_contents($url);
                //         if ($webp){

                //             $filename = $uploads_dir = BASE_PATH . '/v2/images/uploads/banner/'.$name_webp;
                //             file_put_contents($filename,$webp);
                //             $data['images_webp'] = 'v2/images/uploads/banner/'.$name_webp;

                //         }
                //     }
                // }

                // $img_name    = 'imagesMb';
                // $Mbimage = null;
                // $_general = Business_Addon_General::getInstance();

                // $filename = rand(100000,999999).$_general->slugString($title).time();
                // if (isset($_FILES[$img_name]) && $_FILES[$img_name]['size'] > 0) {
                //     $uploads_dir = BASE_PATH . '/v2/images/uploads/banner/';
                //     $tmp_name = $_FILES[$img_name]['tmp_name'];

                //     $ext  = Business_Common_Images::get_image_extension($tmp_name);
                //     $name = "mb-{$filename}.{$ext}";
                //     $name_webp = "mb-{$filename}.webp";
                //     if (!is_dir($uploads_dir)) {
                //         mkdir($uploads_dir, 0777, true);
                //     }
                //     Business_Helpers_Image::getInstance()->resizeImage($tmp_name, "$uploads_dir/$name", 600, 338, 1);

                //     $Mbimage = 'v2/images/uploads/banner/'.$name;
                //     $data['imagesMb'] = $Mbimage;
                //     if ($Mbimage){
                //         $url = "https://api.hnammobile.com/webp-convert.php?url=".Globals::getStaticUrl().$Mbimage;
                //         $webp = @file_get_contents($url);
                //         if ($webp){

                //             $filename = $uploads_dir = BASE_PATH . '/v2/images/uploads/banner/'.$name_webp;
                //             file_put_contents($filename,$webp);
                //             $data['imagesMb_webp'] = 'v2/images/uploads/banner/'.$name_webp;

                //         }
                //     }
                // }


                if ($id){
                    Business_Addon_General::getInstance()->updateDB("addon_booking",$data,"id = ".$id);
                    $data_res = array(
                        "msg" => "Sửa thông tin đặt phòng thành công",
                        "reloads"=>true
                    );
                }else{
                    $lastID = Business_Addon_General::getInstance()->insertDB("addon_booking",$data);
                    $data_res = array(
                        "msg" => "Thêm thông tin đặt phòng thành công",
                        "url"=>"/admin/booking/edit?id=".$lastID
                    );
                }
                $cache = GlobalCache::getCacheInstance('ws');
                $cache->flushAll();

                echo json_encode($data_res);
                die();
            }else{
                $data_res = array(
                    "msg" => "Có lỗi xảy ra. Vui lòng thử lại. 1",
                    "reloads"=>true
                );
                echo json_encode($data_res);
                die();
            }
        }catch (Exception $e){
            $data_res = array(
                "msg" => "Có lỗi xảy ra. Vui lòng thử lại 2",
                "reloads"=>true
            );
            echo json_encode($data_res);
            die($e);
        }
    }

    public function submitNewsAction()
    {
        $id  = (int)$this->_request->getParam('id');
        $token  = $this->_request->getParam('token');
        $title  = addslashes($this->_request->getParam('title'));
        $parentId  = (int)$this->_request->getParam('parentId',0);
        $description  = addslashes($this->_request->getParam('description'));
        $content  = $this->_request->getParam('content');
        $status  = (int)$this->_request->getParam('status');
        $metaTilte  = addslashes($this->_request->getParam('metaTilte'));
        $metaDescription  = addslashes($this->_request->getParam('metaDescription'));
        $_ztoken = Business_Addon_General::getInstance()->checkToken($token);
        try{
            if ($_ztoken){
                $userid = $this->_identity['userid'];
                if (!$title) {
                    echo json_encode(array('msg' => 'Vui lòng nhập tiêu đề', 'field' => 'inputName'));
                    die();
                }
                if (!$description) {
                    echo json_encode(array('msg' => 'Vui lòng nhập mô tả', 'field' => 'inputDescription'));
                    die();
                }

                $slug = Business_Addon_General::getInstance()->slugString($title);
                $data = array(
                    'title' => $title,
                    'parent_id' => $parentId,
                    'meta_title' => $metaTilte,
                    'meta_description' => $metaDescription,
                    'description' => $description,
                    'userid' => $userid,
                    'enabled' => $status,
                    'content' => $content,
                );
                $img_name    = 'images';
                $image = null;
                $_general = Business_Addon_General::getInstance();

                $filename = rand(100000,999999).$_general->slugString($title).time();
                if (isset($_FILES[$img_name]) && $_FILES[$img_name]['size'] > 0) {
                    $uploads_dir = BASE_PATH . '/v2/images/uploads/news/';
                    $tmp_name = $_FILES[$img_name]['tmp_name'];

                    $ext  = Business_Common_Images::get_image_extension($tmp_name);
                    $name = "{$filename}.{$ext}";
                    if (!is_dir($uploads_dir)) {
                        mkdir($uploads_dir, 0777, true);
                    }
                    Business_Helpers_Image::getInstance()->resizeImage($tmp_name, "$uploads_dir/$name", 600, 450, 1);

                    $image = 'v2/images/uploads/news/'.$name;
                }
                if(isset($image)) {
                    $data['images'] = $image;
                }

                $img_name    = 'imagesFull';
                $image = null;
                $_general = Business_Addon_General::getInstance();

                $filename = rand(100000,999999).$_general->slugString($title).time();
                if (isset($_FILES[$img_name]) && $_FILES[$img_name]['size'] > 0) {
                    $uploads_dir = BASE_PATH . '/v2/images/uploads/news/';
                    $tmp_name = $_FILES[$img_name]['tmp_name'];

                    $ext  = Business_Common_Images::get_image_extension($tmp_name);
                    $name = "social-{$filename}.{$ext}";
                    if (!is_dir($uploads_dir)) {
                        mkdir($uploads_dir, 0777, true);
                    }
                    Business_Helpers_Image::getInstance()->resizeImage($tmp_name, "$uploads_dir/$name", 1920, 1080, 1);

                    $image = 'v2/images/uploads/news/'.$name;
                }
                if(isset($image)) {
                    $data['images_full'] = $image;
                }

                $img_name    = 'imagesSocal';
                $socialimage = null;
                $_general = Business_Addon_General::getInstance();

                $filename = rand(100000,999999).$_general->slugString($title).time();
                if (isset($_FILES[$img_name]) && $_FILES[$img_name]['size'] > 0) {
                    $uploads_dir = BASE_PATH . '/v2/images/uploads/news/';
                    $tmp_name = $_FILES[$img_name]['tmp_name'];

                    $ext  = Business_Common_Images::get_image_extension($tmp_name);
                    $name = "social-{$filename}.{$ext}";
                    if (!is_dir($uploads_dir)) {
                        mkdir($uploads_dir, 0777, true);
                    }
                    Business_Helpers_Image::getInstance()->resizeImage($tmp_name, "$uploads_dir/$name", 400, 300, 1);

                    $socialimage = 'v2/images/uploads/news/'.$name;
                }
                if(isset($socialimage)) {
                    $data['images_social'] = $socialimage;
                }

                if ($id){
                    Business_Addon_General::getInstance()->updateDB("addon_news",$data,"id = ".$id);
                    $data_res = array(
                        "msg" => "Sửa bài viết thành công",
                        "reloads"=>true
                    );
                }else{
                    $data['slug'] = $slug;
                    $lastID = Business_Addon_General::getInstance()->insertDB("addon_news",$data);
                    $data_res = array(
                        "msg" => "Thêm bài viết thành công",
                        "url"=>"/admin/news/edit?id=".$lastID
                    );
                }
                $cache = GlobalCache::getCacheInstance('ws');
                $cache->flushAll();

                echo json_encode($data_res);
                die();
            }else{
                $data_res = array(
                    "msg" => "Có lỗi xảy ra. Vui lòng thử lại.",
                    "reloads"=>true
                );
                echo json_encode($data_res);
                die();
            }
        }catch (Exception $e){
            $data_res = array(
                "msg" => "Có lỗi xảy ra. Vui lòng thử lại",
                "reloads"=>true
            );
            echo json_encode($data_res);
            die();
        }
    }


    public function submitProductsAction()
    {
        $id  = (int)$this->_request->getParam('id');
        $token  = $this->_request->getParam('token');
        $title  = addslashes($this->_request->getParam('title'));
        $parentId  = (int)$this->_request->getParam('parentId',0);
        $description  = addslashes($this->_request->getParam('description'));
        $descriptionRight  = addslashes($this->_request->getParam('descriptionRight'));
        $price = (int) $this->_request->getParam('price');
        $priceSales = (int) $this->_request->getParam('priceSales');
        $metaTilte  = addslashes($this->_request->getParam('metaTilte'));
        $metaDescription  = addslashes($this->_request->getParam('metaDescription'));
        $status  = (int)$this->_request->getParam('status');
        $_ztoken = Business_Addon_General::getInstance()->checkToken($token);
        try{
            if ($_ztoken){
                $userid = $this->_identity['userid'];
                if (!$title) {
                    echo json_encode(array('msg' => 'Vui lòng nhập tiêu đề', 'field' => 'inputName'));
                    die();
                }
                if (!$description) {
                    echo json_encode(array('msg' => 'Vui lòng nhập tiêu đề', 'field' => 'inputName'));
                    die();
                }

                $slug = Business_Addon_General::getInstance()->slugString($title);
                $data = array(
                    'title' => $title,
                    'parent_id' => $parentId,
                    'meta_title' => $metaTilte,
                    'meta_description' => $metaDescription,
                    'description' => $description,
                    'description_right' => $descriptionRight,
                    'price' => $price,
                    'price_sales' => $priceSales,
                    'userid' => $userid,
                    'enabled' => $status,
                );


                $dataImages = Array();
               
                for($i = 0; $i < 10 ; $i++){
                    $img_name    = 'images' . $i;
                    $image = null;
                    $_general = Business_Addon_General::getInstance();

                    $filename = rand(100000,999999).$_general->slugString($title).time();
                    if (isset($_FILES[$img_name]) && $_FILES[$img_name]['size'] > 0) {
                        $uploads_dir = BASE_PATH . '/v2/images/uploads/home/';
                        $tmp_name = $_FILES[$img_name]['tmp_name'];

                        $ext  = Business_Common_Images::get_image_extension($tmp_name);
                        $name = "thumb-{$filename}.{$ext}";
                        if (!is_dir($uploads_dir)) {
                            mkdir($uploads_dir, 0777, true);
                        }
                        if ($i==0){
                            Business_Helpers_Image::getInstance()->resizeImage($tmp_name, "$uploads_dir/$name", 600, 300, 1);
                        }else{
                            Business_Helpers_Image::getInstance()->resizeImage($tmp_name, "$uploads_dir/$name", 1920, 1080, 1);
                        }

                        $image = 'v2/images/uploads/home/'.$name;
                    }
                    if(isset($image)) {
                        $dataImages[$img_name] = $image;
                    }
                }

                if(isset($dataImages)){
                    $data['images'] = json_encode($dataImages);
                }


                $dataProperties = Array();

                for($i = 0; $i < 10 ; $i++){
                    $properties = Array();
                    $title = "dataTitle" . $i;
                    $title = $this->_request->getParam($title);
                    $url = "dataUrl" . $i;
                    $url = $this->_request->getParam($url);
                    $myOrder = "dataMyOrder" . $i;
                    $myOrder = $this->_request->getParam($myOrder);
                    if(isset($title)){
                        $properties['title'] = $title;
                    }else{
                        $properties['title'] = "";
                    }
                    if(isset($url)){
                        $properties['url'] = $url;
                    }else{
                        $properties['url'] = "";
                    }
                    if(isset($myOrder)){
                        $properties['myOrder'] = $myOrder;
                    }else{
                        $properties['myOrder'] = "";
                    }
                    $dataProperties[$i] = $properties;
                }

                if(isset($dataProperties)){
                    $data['properties'] = json_encode($dataProperties);
                }
                if ($id){
                    Business_Addon_General::getInstance()->updateDB("addon_products",$data,"id = ".$id);
                    $data_res = array(
                        "msg" => "Sửa sản phẩm thành công",
                        "reloads"=>true
                    );
                }else{
                    $lastID = Business_Addon_General::getInstance()->insertDB("addon_products",$data);
                    $data_res = array(
                        "msg" => "Thêm sản phẩm thành công",
                        "url"=>"/admin/news/edit?id=".$lastID
                    );
                }
                $cache = GlobalCache::getCacheInstance('ws');
                $cache->flushAll();

                echo json_encode($data_res);
                die();
            }else{
                $data_res = array(
                    "msg" => "Có lỗi xảy ra. Vui lòng thử lại.",
                    "reloads"=>true
                );
                echo json_encode($data_res);
                die();
            }
        }catch (Exception $e){
            $data_res = array(
                "msg" => "Có lỗi xảy ra. Vui lòng thử lại",
                "reloads"=>true
            );
            echo json_encode($data_res);
            die();
        }
    }

    

}