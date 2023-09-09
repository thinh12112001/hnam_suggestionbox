<?php

abstract class Business_Abstract
{
			 
	private function getDbConnection()
	{
		$db    	= Globals::getDbConnection('maindb', false);
		return $db;	
	}
	
	public function adaptSQL($input)
	{
		$input = str_replace("'","''",$input);
		return $input;
	}



	public function getTelegram($group) {
	    $arrs = array(
	        'hcare' => array(
	            'title' => 'HCARE',
                'id' => -1001350918812,
            )
        );
        return (isset($arrs[$group]))?$arrs[$group]:false;
    }
	
	public function parseArrayToObject($array) {
		$object = new stdClass();
		if (is_array($array) && count($array) > 0) {
			foreach ($array as $name=>$value) {
				$name = strtolower(trim($name));
				if (!empty($name)) {
					$object->$name = $value;
				}
			}
		}
		return $object;
	}

    public function excuteCode($query){
        $db     =  $this->getDbConnection();
        $result = $db->query(''.$query.'');
    }

    public function excuteCodev2($query){
        $db     =  $this->getDbConnection();
        $_result = $db->fetchAll($query);
        if($_result) {
            return $_result;
        }
        return 0;
    }

    public function getDataDB($table,$select='',$where='',$orderby='',$limit='') {
        $db = $this->getDbConnection();
        $select = $select?$select:'*';
        $orderby = $orderby?$orderby:'id asc';
        $query = "select $select from $table";
        if($where) {
            $query .= " where (1) and $where";
        }
        $query.= " order by $orderby";
        if($limit) {
            $query .= " limit $limit";
        }
        $_result = $db->fetchAll($query);
        if($_result) {
            return $_result;
        }
        return 0;
    }

    public function insertDB($table,$data) {
        $db = $this->getDbConnection();
        $result = $db->insert($table,$data);
        if ($result > 0) {
            $lastid= $db->lastInsertId($table);
            $cache = GlobalCache::getCacheInstance('ws');
            $cache->flushAll();
        }
        return $lastid;
    }

    public function updateDB($table,$data,$query) {
        $db= $this->getDbConnection();
        $result = $db->update($table, $data, $query);
        $cache = GlobalCache::getCacheInstance('ws');
        $cache->flushAll();
        return $result;
    }

    public function deleteDB($table,$where) {
        $db= $this->getDbConnection();
        $result = $db->delete($table,$where);
        $cache = GlobalCache::getCacheInstance('ws');
        $cache->flushAll();
        return $result;
    }

    public function pageNotFound() {
        Zend_Layout::getMvcInstance()->disableLayout();
        $gbba= Zend_Controller_Action_HelperBroker::getStaticHelper('error');
        $var = $gbba->direct("index");
        die();
    }

    public function checkAction($obj) {
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        $action = $request->getParam('action');
        $function = ucwords(str_replace('-',' ',$action));
        $function = str_replace(' ','',$function);
        $function = strtolower($function[0]).substr($function,1);
        $function = $function.'Action';
        if (!method_exists($obj,$function)) {
            $this->pageNotFound();
        }
    }

    public function slugString($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = preg_replace('/[^A-Za-z0-9]+/', ' ', $str);
        $str = preg_replace('!\s+!', ' ',  trim($str));
        $str = str_replace(' ', '-', $str);
        $str = strtolower($str);
        return $str;
    }
}
?>