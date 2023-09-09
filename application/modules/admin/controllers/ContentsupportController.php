<?php

/* TODO: Add code here */

class Admin_ContentsupportController extends Zend_Controller_Action {

    private $_identity=null;

    public function init() {
        // do something	
        
    }

    public function removeAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $content = $this->_request->getParam("content");
        $content = $this->removeLink($content);
        echo $content;
    }
    
    public function downloadAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $title = $this->_request->getParam("title");
        $content = $this->_request->getParam("content");
        $content = $this->replaceImageForPost($title, $content);
        echo $content;
    }

    public function altImageAction() {
        $this->_helper->Layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $title = $this->_request->getParam("title");
        $content = $this->_request->getParam("content");
        $content = $this->replaceAltImageForPost($title, $content);
        echo $content;
    }
    
    private function removeLink($content) {
        
        if ($content != "") {
            preg_match_all('/<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU', $content, $matches);

            $links = $matches[0];
            $domains = $matches[2];
            $domainnames = $matches[3];
            $whiteList = globals::getConfig("siteinfo")->seo->domainexeclude;
            
            $whiteLists = explode(",", $whiteList);
            for($i=0; $i<count($domains); $i++) {
                $isWhiteList = 0;
                foreach ($whiteLists as $wd) {
                    if (stripos($domains[$i], $wd)!==false) {                           
                        $isWhiteList = 1;
                    }
                    if ($isWhiteList==0) {
                        $content = str_replace($links[$i], $domainnames[$i], $content);
                    }
                }
            }
        }
        
        return $content;
    }
    
    private function replaceImageForPost($title, $content) {

        $content = preg_replace("/[\n\r]/", "", $content);
        $_title = $title;
        $title = md5($title);
        $part = substr($title, 0, 1);
        $des = BASE_PATH . "/downloads/$part/";
        
        $des_original = Globals::getStaticUrl() . "downloads/$part/";
        
        if (!is_dir($des)) {
            mkdir($des);
        }
        preg_match_all('/\<img (.*?)src=\"(.*?)\"(.*?)\>/', $content, $matches);
        $links = $matches[2];
        $count = 0;

        $new_title = Business_Addon_General::getInstance()->slugString($_title);

        foreach ($links as $key=>$img) {
            if (stripos($img, "newcenturyhotel.vn")!==false) {
				continue;
			}
            $names = explode("/", $img);
            //$filename = Business_Common_Utils::adaptTitleLinkURLSEO($names[count($names) - 1]);
			$filename = $names[count($names) - 1];
            $_des_original = $des_original . $filename;
            if (stripos($filename, "?") !== false) {
                $filenames = explode("?", $filename);
                $filename = $filenames[0];
            }
            $_des = $des . $filename;
            $pos = strrpos($filename,'.');
            $ext  = substr($filename, $pos);
            $time = $key.time();
            $_new_des = $des . $new_title. '-' . $time . $ext;
            $isfile = 0;
            if (is_file($_des)) {
                $isfile = 1;
                $size = getimagesize($_des);
                if ($size[0] > 800) {
                    Business_Helpers_Image::getInstance()->resizeImage($_des, $_new_des, 800, 9999, 0);
                    $_des_original = $des_original . $new_title. '-' . $time . $ext;
                }
                elseif(strpos($_des,$new_title)===false) {
                    if(copy($_des, $_new_des)) {
                        $_des_original = $des_original . $new_title. '-' . $time . $ext;
                    }
                }
            }


            $opts = array('http' =>
                array(
                    'method' => 'GET',
                    'timeout' => 5
                )
            );

            if ($isfile == 0) {
                $context = stream_context_create($opts);
                $_img = file_get_contents($img, false, $context);
				if ($_img == false || strlen($_img)==0) {
					$_img = Business_Common_Utils::getContentByCurl($img);
				}
                //$_img = file_get_contents($img);
                if (strlen($_img) > 1000) {
                    $_new_des = $des . $new_title. '-' . $time . '.jpg';
                    file_put_contents($_new_des, $_img);
                    $size = getimagesize($_new_des);
                    if ($size[0] > 800) {
                        Business_Helpers_Image::getInstance()->resizeImage($_new_des, $_new_des, 800, 9999, 0);
                    }
                    $_des_original = $des_original . $new_title. '-' . $time . '.jpg';
                    //copy to farmdt
                    if ($count++ == 0) {
                        $thumb = "/$filename";
                    }
                    $content = str_replace($img, $_des_original, $content);
                }
            } else {
                if ($count++ == 0) {
                    $thumb = "/$filename";
                }
                $content = str_replace($img, $_des_original, $content);
            }
        }
        
        return $content;
    }

    private function replaceAltImageForPost($title, $content) {

        $content = preg_replace("/[\n\r]/", "", $content);
        preg_match_all('/\<img (.*?)alt=\"(.*?)\"(.*?)\>/', $content, $matches);
        $items = $matches[0];
        $afterContent = $matches[1];
        $beforeContent = $matches[3];
        $count = 0;

        foreach ($items as $key=>$item) {
            $count++;
            $alt = "New Century Hotel - {$title} - {$count}";
            $newItem = "<img {$afterContent[$key]}alt=\"{$alt}\"{$beforeContent[$key]}/>";
            $content = str_replace($item, $newItem, $content);
        }

        return $content;
    }
}