<?php
require_once APPLICATION_PATH . "/etc/Globals.php";

class ProfilerPlugin extends Zend_Controller_Plugin_Abstract
{

    private static $_time_start_render = 0;

    private static $_time_end_render = 0;

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if ( isset($_REQUEST['s']) && $_REQUEST['s'] == 10) {
            echo "Module: ".$request->getParam('module');
            echo "<br>Controller: " . $request->getParam('controller');
            echo "<br>Action: ".$request->getParam('action');
            die();
        }
		$files = $_FILES;
		if (count($files)>0) {
			foreach($_FILES as $fileName => $fileInfo) {
				$rawName = $fileInfo["name"];
				if ($rawName=="") continue;
                $rawNames = explode(".", $rawName);
                $rawNameExt = strtolower($rawNames[count($rawNames)-1]);
                if ($rawNameExt == "php") {
                    die("Invalid data!!!!");
                }
				$mime = $fileInfo["type"];
				if (strpos($mime, "image")===false) {
					die("Invalid data!!!!");
				}
			}
		}

//        $this->adminPermission();
//        $this->updateViewDetail();
        $cache = GlobalCache::getCacheInstance('ws');
        if(isset($_REQUEST['d']) and $_REQUEST['d']==10) {
            $cache->flushAll();
        }
    }

    public function dispatchLoopShutdown()
    {
    }

    private function updateViewDetail() {
        $_general = Business_Addon_General::getInstance();
        $_service = Business_Hshop_Service::getInstance();
        $_blog = Business_Hshop_Blog::getInstance();
        if (!$_general->is_bot()) {
            $module = $this->_request->getParam('module');
            $controller = $this->_request->getParam('controller');
            $action = $this->_request->getParam('action');
            $itemid = $this->_request->getParam('itemid');
            if ($module == 'hnamcare' && $action == 'detail' && in_array($controller, array('service', 'blog')) && $itemid > 0) {
                $table = '';
                switch ($controller) {
                    case 'service':
                        $table = 'hshop_service_items';
                        $item = $_service->getItemByID($itemid, 1);
                        break;
                    case 'blog':
                        $table = 'hshop_blog_items';
                        $item = $_blog->getItemByID($itemid, 1);
                        break;
                }
                if ($item) {
                    $date = date('Y-m-d H:i:s');
                    $query = "UPDATE {$table} SET hit = hit + 1, last_hit = '{$date}' WHERE id = {$itemid}";
                    $_general->excuteCode($query);
                }
            }
        }
    }

    private function adminPermission()
    {
        $_module  = $this->_request->getParam('module');
        if ($_module == 'admin') {
            $currentUrl = Business_Common_Utils::curPageURL(1);
            $base_url = Globals::getBaseUrl();
            $_controller  = $this->_request->getParam('controller');
            $_action  = $this->_request->getParam('action');
            $check = false;
            if ($_controller == 'auth' && in_array($_action, array('login','logout'))) {
                $check = true;
            }

            if (!$check) {
                $currentUrl = str_replace(array('http://','https://'),array('',''),$currentUrl);
                $base_url = str_replace(array('http://','https://'),array('',''),$base_url);
                $currentUrl = str_replace($base_url,'',$currentUrl);
                $auth = Zend_Auth::getInstance();
                $check_admin = false;
                $check_comment = false;

                $url = '/admin/login?redirect='.$currentUrl;
                if(!$auth->hasIdentity())
                {

                    $check_admin = true;
                    $check_comment = true;
                }
                else {

                    $user = (array) $auth->getIdentity();
                    if($user['hcare']){
                        if ($user['hcare'] == 2) {
                            $check_admin = true;
                            $url = '/';
                        }
                        elseif(in_array($user['hcare'],array(3,4))) {

                            $menus = Business_Hshop_Admin::getInstance()->getMenusLink($user['hcare']);
                            $_url = parse_url($currentUrl)['path'];
                            if (!in_array($_url,$menus)) {
                                $url = $menus[0];
                                $check_admin = true;
                            }
                        }
                        elseif($user['hcare'] != 1) {
                            $check_admin = true;
                        }
                    }
                    else {
                        $check_admin = true;
                    }
                }
                if ($check_admin) {
                    header("Location: {$url}");
                }
            }
        }
    }
    
    // ///////////////////// private functions ///////////////////////////////
    private function dumpPageRenderProfiler($start_time, $end_time)
    {
        $print = "";
        
        $diff = ($end_time - $start_time);
        
        $print .= '<br><table border=1 cellspacing="5" cellpadding="5" style="border-collapse:collapse">';
        $print .= '<tr><td><b>page render time : ' . $diff . ' secs</b></td></tr>';
        $print .= "</table><br>";
        return $print;
    }

    private function dumpMemoryUsageProfler()
    {
        $print = '';
        $print .= '<br><table border=1 cellspacing="5" cellpadding="5" style="border-collapse:collapse">';
        $print .= '<tr><td><b>Php Memory usage: ' . $this->adaptMB(@memory_get_usage()) . ' (' . $this->adaptMB(@memory_get_usage(true)) . ' ) Mbytes</b></td></tr>';
        $print .= "</table>";
        return $print;
    }

    private function dumpProfilerLog()
    {
        $print = '<br>';
        $print .= ProfilerLog::dumpLog();
        return $print;
    }

    private function adaptMB($value)
    {
        $size = $value / (1024 * 1024);
        return number_format($size, 2);
    }

    private function dumpCacheProfiler()
    {
        $print = "";
        
        $print .= Maro_Cache_MemGlobalCache::getAllProfilerData();
        
        return $print;
    }

    private function dumpDbProfiler()
    {
        $print = "";
        $arr = GlobalsDB::$arrDB;
        if (is_array($arr) && count($arr) > 0) {
            $print .= '<br><br><table border=1 cellspacing="5" cellpadding="5" style="border-collapse:collapse">' . "<tr><th colspan=3 bgcolor='#dddddd'>Database Profiler</th></tr>" . "<tr><th width=50>No.</th><th>Query</th><th>Time elapsed in secs</th>";
            if (is_array($arr) && count($arr) > 0) {
                foreach ($arr as $key => $value) {
                    $count = 1;
                    $profiler = $value->getProfiler();
                    $print .= "<tr><td colspan='3' align='left'><b>debug profiler for db " . $key . "</b> --- ";
                    $print .= "Total query : " . $profiler->getTotalNumQueries() . " ---- Total time elapsed : " . number_format($profiler->getTotalElapsedSecs(), 9) . " seconds";
                    $print .= "</td></tr>";
                    $profiler_arr = $profiler->getQueryProfiles();
                    if (is_array($profiler_arr) && count($profiler_arr) > 0) {
                        foreach ($profiler_arr as $query) {
                            $print .= "<tr><td>" . $count ++ . "</td><td align='left'>" . $query->getQuery() . "</td><td align='left'>" . number_format($query->getElapsedSecs(), 9) . "</td></tr>";
                        }
                    }
                }
            }
            $print .= '</table><br><br><br>';
        } else {
            $print .= "no database instance created.<br><br>";
        }
        return $print;
    }
}
?>
