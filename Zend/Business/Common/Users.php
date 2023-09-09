<?php

class Business_Common_Users extends Business_Abstract
{
	private $_tablename = 'zfw_users';
	
	const KEY_LIST = 'zfw_users.list';
	const KEY_DETAIL = 'zfw_users.uid.%s';
    const KEY_CHECKROLE = 'zfw_users.checkrole';
	
	protected static $_current_rights = null;
	
	protected static $_instance = null; 

	function __construct()
	{
		
	}

    public function checkRole($username,$password)
    {
        $cache = $this->getCacheInstance();
        $key = $this->getKeyCheckRole();
        $result = $cache->getCache($key);
        $result=FALSE;
        if($result === FALSE)
        {
            $db = $this->getDbConnection();
            $query = "SELECT * FROM $this->_tablename where username = '$username' and password = '$password'";
//			var_dump($query);exit();
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result))
            {
                $cache->setCache($key, $result);
            }
        }

        return $result[0];
    }

	public function getListByUserid($str_userid)
	{
        $cache = $this->getCacheInstance();
        $key = "getListByUserid".  $this->_tablename.$str_userid;
        $result = $cache->getCache($key);
        $result = false;
        if($result ===FALSE){
            $db = $this->getDbConnection();
            $query = "SELECT * FROM " . $this->_tablename . " WHERE  userid IN ($str_userid)";
            $result = $db->fetchAll($query);
            if(!is_null($result) && is_array($result)){
                $cache->setCache($key, $result);
            }
        }
		return $result;
	}
	
	/**
	 * get instance of Business_Common_Users
	 *
	 * @return Business_Common_Users
	 */
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function getUserCart($ship_method=1) {
	    if ($ship_method==1){
            $sql = "SELECT * FROM addon_user_sale where enable =1 and action_user = 0 order by id ASC";
            $list = Business_Addon_General::getInstance()->excuteCodev2($sql);
            if ($list){
                $id = $list[0]['userid'];
                $sid = $list[0]['id'];
                $query2 = "UPDATE addon_user_sale SET action_user = 1 where id = $sid";
                Business_Addon_General::getInstance()->excuteCode($query2);
                return $id;
            }else{
                $query = "UPDATE addon_user_sale SET action_user = 0";
                Business_Addon_General::getInstance()->excuteCode($query);
                $sql = "SELECT * FROM addon_user_sale where enable =1 and action_user = 0 order by id ASC";
                $list = Business_Addon_General::getInstance()->excuteCodev2($sql);
                if ($list!=0){
                    $id = $list[0]['userid'];
                    $sid = $list[0]['id'];
                    $query2 = "UPDATE addon_user_sale SET action_user = 1 where id = $sid";
                    Business_Addon_General::getInstance()->excuteCode($query2);
                    return $id;
                }else{
                    return 0;
                }
            }
        }else{
            return 0;
        }

    }

	public function getRandomUIDByParentID($parentid)
	{
	    
		$cache = $this->getCacheInstance();
		$key = $this->getKeyDetail("p".$parentid);
		//$result = $cache->getCache($key);
		$result=false;
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			//hardcode cho nhân viên sale
			$query = "SELECT *, rand() as random FROM " . $this->_tablename . " WHERE parentid = ? and is_actived=1 and idregency = 18 and username NOT IN ('hnam_tranhb1','hnam_nhungttp','hnam_nhungttp','hnam_quynhtnt','hnam_hanhnth','hnam_phuongptt1','hnam_tranghtt','hnam_toainc','hnam_hienttm','hnam_phuongptt') ORDER BY random ASC LIMIT 0,1";
			$data = array($parentid);
//			var_dump($query); exit();
			$result = $db->fetchAll($query, $data);
			if(!is_null($result) && is_array($result) && count($result) > 0)
			{		
                                $id = $result[0]["userid"];
				$result = $id;
				//$result = $cache->setCache($key, $id, 60);
			}			
		}
		return $result;
	}
        
        public function getListByUname($exclude=null){
            $db = $this->getDbConnection();
            $ch = 'vote_';
            if ($exclude==null) {
                $query = "select * from $this->_tablename where username like '%$ch%'";
            } else {
                
                $query = "select * from $this->_tablename where username like '%$ch%' AND username != 'vote_all' AND username !='vote_saleonline'";
            }
            $result = $db->fetchAll($query);
//            var_dump($result);exit();
            return $result;
        }
	
	public static function checkRight($right = '')
	{
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		
		if(!$auth->hasIdentity()) return false;
		
		if(is_null(self::$_current_rights))
		{			
			if($auth->hasIdentity())
			{
				$_user = self::getInstance();			
				$userid = $identity->userid;
				self::$_current_rights = $_user->getRolesForUser($userid);	  
			}
			else
			{
				self::$_current_rights = array();
			}			
		}

		$username = $identity->username;
		if($username == "admin") return true;
		
		if(in_array($right, self::$_current_rights)) return true;
		else return false;
	}
	
	private function getKeyList()
	{
		return sprintf(Business_Common_Users::KEY_LIST);
	}
    private function getKeyCheckRole()
    {
        return sprintf(Business_Common_Users::KEY_CHECKROLE);
    }
	private function getKeyDetail($uid)
	{
		return sprintf(Business_Common_Users::KEY_DETAIL, $uid);
	}
	
	/**
	 * get Zend_Db connection
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	function getDbConnection()
	{		
		$db    	= Globals::getDbConnection('maindb', false);
		return $db;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return Maro_Cache
	 */
	function getCacheInstance()
	{
		$cache = GlobalCache::getCacheInstance();		
		return $cache;
	}
	
	public function getList()
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyList();
		$result = $cache->getCache($key);
						
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " ORDER BY username";
			$result = $db->fetchAll($query);
			if(!is_null($result) && is_array($result))
			{
				$cache->setCache($key, $result);
			}
		}
		
		return $result;		
	}
	
	public function getListUserPush($uid)
	{
			$db = $this->getDbConnection();
			$query = "SELECT userid FROM `zfw_users` WHERE `parentid` = $uid AND `idregency` IN (11,14) and is_actived=1";
			$result = $db->fetchAll($query, $data);	
			return $result;
	}
	public function deleteListUserPush($listid)
	{
			$db = $this->getDbConnection();
			$query = "Delete FROM `ws_push` WHERE userid in ($listid)";
			$result = $db->query($query);	
			return $result;
	}

	public function deleteEndpointPush($endpoint)
	{
			$db = $this->getDbConnection();
			$query = "Delete FROM `ws_push` WHERE endpoint like '%$endpoint%' ";
			$result = $db->query($query);	
			return $result;
	}

	public function getUserByUid($uid)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyDetail($uid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE userid = ?";
			$data = array($uid);
			$result = $db->fetchAll($query, $data);
			if(!is_null($result) && is_array($result) && count($result) > 0)
			{
				$result = $result[0];
				$result = $cache->setCache($key, $result);
			}			
		}
		return $result;
	}

	public function getCUserByUid($uid)
	{
		$cache = $this->getCacheInstance();
		$key = $this->getKeyDetail($uid);
		$result = $cache->getCache($key);
		if($result === FALSE)
		{
			$db = $this->getDbConnection();
			$query = "SELECT * FROM " . $this->_tablename . " WHERE userid = ?";
			$data = array($uid);
			$result = $db->fetchAll($query, $data);
			if(!is_null($result) && is_array($result) && count($result) > 0)
			{
				$result = $result[0];
				$cache->setCache($key, $result);
			}
		}
		return $result;
	}
	
	public function getUser($username)
	{
		$list = $this->getList();
		if($list != null && is_array($list) && count($list) > 0)
		{
			for($i=0;$i<count($list);$i++)
			{
				if($list[$i]['username'] == $username)
				{
					return $list[$i];
				}
			}
		}
		return null;
	}
	
	//return userid last inserted
	public function addUser($data)
	{
		$db = $this->getDbConnection();
		$result = $db->insert($this->_tablename, $data);
		
		$lastid = 0;
		if($result)
		{
			$lastid = $db->lastInsertId();
			$cache = $this->getCacheInstance();
			$key = $this->getKeyList();
			$cache->deleteCache($key);
		}
		return $lastid;
	}
	
	public function updateUser($userid, $data)
	{
		$db = $this->getDbConnection();
		$where = array();
		$where[] = "userid='" . parent::adaptSQL($userid) . "'";
		$result = $db->update($this->_tablename, $data, $where);
		if($result)
		{
			$cache = $this->getCacheInstance();
			$key = $this->getKeyList();
			$cache->deleteCache($key);
			
			$key = $this->getKeyDetail($userid);
			$cache->deleteCache($key);
		}
		return $result;
	}
	
	public function getRolesForUser($userid)
	{
		$_roles = Business_Common_Roles::getInstance();
		
		$user_roles = $_roles->getRolesByUser($userid);		
				
		$user_perm = array();
		
		$_permission = Business_Common_Permissions::getInstance();
		
		if($user_roles != null && is_array($user_roles) && count($user_roles) > 0)
		{
			for($i=0;$i<count($user_roles);$i++)
			{
				$pid = $user_roles[$i]['pid'];				
				$perm = $_permission->getPermision($pid);				
				$perm = explode(',',$perm['permission']);												
				$user_perm = array_merge($user_perm,$perm);				
			}
		}		
		return $user_perm;
	}
	
	
		
}
?>