<?php
if (!defined('CC_INI_SET')) die("Access Denied");

class session {

	var $ccUserData;
	var $ccUserBlocked = false;
	
	var $config;
	var $db;
	var $glob;
	var $ini;
	
	function session() {
	
		global $config, $db, $glob, $ini;
		
		$this->config	= $config;
		$this->db		= $db;
		$this->glob		= $glob;
		$this->ini		= $ini;
		
		if (isset($_GET[CC_SESSION_NAME])) {
			$this->set_cc_cookie(CC_SESSION_NAME, $_GET[CC_SESSION_NAME], $this->config['sqlSessionExpiry']);
		} else {
			## see if session is still in db
			$query = sprintf("SELECT sessId FROM %ssessions WHERE sessId=%s", $this->glob['dbprefix'], $this->db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));
			
			$results = $this->db->select($query);
			
			## !empty($results[0]['sessId']) critical in case results=true if session DB table has an empty sessionId!!
			if ($results && !empty($results[0]['sessId'])) {
				$data["timeLast"] = $this->db->mySQLSafe(time());
				$data["location"] = $this->db->mySQLSafe(currentPage());
				$update = $this->db->update($this->glob['dbprefix']."sessions", $data, "sessId=".$this->db->mySQLSafe($results[0]['sessId']));
			} else {
				$this->makeSession();
			}
		}
		
		## get all session data and store as class array
		$query = sprintf("SELECT * FROM %1\$ssessions LEFT JOIN %1\$susers ON %1\$ssessions.user_id = %1\$susers.user_id WHERE sessId = %2\$s", $this->glob['dbprefix'], $this->db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));
		$result = $this->db->select($query);
		// security checks
		$this->ccUserData = $result[0];
		if (empty($this->ccUserData['email_address']) && isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
			$this->authenticate($_COOKIE['username'], $_COOKIE['password'], true, true);	
		}
	}	

	function destroySession($sessionId) {
		
		## removed to keep basket data
		$this->set_cc_cookie('username', '', time()-3600);
		$this->set_cc_cookie('password', '', time()-3600);
		
		$data["user_id"] = '0';
		$update = $this->db->update($this->glob['dbprefix']."sessions", $data,"sessId=".$this->db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));
		return ($update) ? true : false;
	}

	function makeSession() {
		$sessionId = $this->makeSessId();
		$this->set_cc_cookie(CC_SESSION_NAME, $sessionId, $this->config['sqlSessionExpiry']);
		
		## set session global var because cookie won't show until next page load
		$GLOBALS[CC_SESSION_NAME] = $sessionId;
		
		## insert sessionId into db
		$data["sessId"] 		= 	$this->db->mySQLSafe($sessionId);		
		$timeNow 				= 	$this->db->mySQLSafe(time());
		$data["timeStart"] 		= 	$timeNow;	
		$data["timeLast"] 		= 	$timeNow;
		$data["user_id"] 	= 	0;
		$data["ip"] 			= 	$this->db->mySQLSafe(get_ip_address());
		$data["browser"] 		= 	$this->db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
		$insert = $this->db->insert($this->glob['dbprefix']."sessions", $data);
		$this->deleteOldSessions();
	}
	
	function deleteOldSessions() {
		$expiredSessTime = time() - $this->config['sqlSessionExpiry'];
		## delete sessions older than time set in config file
		//$delete = $this->db->delete($this->glob['dbprefix']."sessions", "timeLast<".$expiredSessTime);
	}
	
	function createSalt($user,$pass,$remember) {
		$salt = randomPass(6);
		$pass_hash = md5(md5($salt).md5($pass));
		$this->db->update($this->glob['dbprefix']."users", array("password" => $this->db->mySQLSafe($pass_hash),"salt" => $this->db->mySQLSafe($salt)),"email_address=".$this->db->mySQLSafe($user));
		$this->authenticate($user,$pass,$remember);
	}

	function authenticate($user, $pass, $remember = false, $cookie_login = false) {
		if ($cookie_login) {
			$user		= sanitizeVar($_COOKIE['username']);
			$passMD5	= sanitizeVar($_COOKIE['password']); 
		} else {
			$user		= sanitizeVar($user);
			$passMD5	= md5(sanitizeVar($pass));
		}
		
		$query = "SELECT `user_id`, `salt` FROM ".$this->glob['dbprefix']."users WHERE `activated`>0 AND `email_address`=".$this->db->mySQLSafe($user);
		$salt = $this->db->select($query);
		if($salt[0]['user_id']>0 && empty($salt[0]['salt']) && $cookie_login == false) {
			$query = "SELECT `user_id` FROM ".$this->glob['dbprefix']."users WHERE email_address=".$this->db->mySQLSafe($user)." AND `password` = ".$this->db->mySQLSafe($passMD5)." AND activated>0";
		if($users = $this->db->select($query)) {
				$this->createSalt($user,$pass,$remember);
			} else {
				return false;
			}
		} else { 
			 $passMD5 = md5(md5($salt[0]['salt']).md5($pass));
			$query = "SELECT `user_id` FROM ".$this->glob['dbprefix']."users WHERE email_address=".$this->db->mySQLSafe($user)." AND password = ".$this->db->mySQLSafe($passMD5)." AND activated>0";
			$users = $this->db->select($query);
			
		}
		
		if (!$users) {
			if ($this->db->blocker($user, $this->ini['bfattempts'], $this->ini['bftime'], false, 'f')) {
				$this->ccUserBlocked = true; 	
			}
		} else if ($users[0]['user_id']>0) {
		
			// remember user for as long as sessions are allowed in DB
			if ($remember == true) {
				$this->set_cc_cookie('username', $user, $this->config['sqlSessionExpiry']);
				$this->set_cc_cookie('password', $passMD5, $this->config['sqlSessionExpiry']); 
				$this->set_cc_cookie(CC_SESSION_NAME, $GLOBALS[CC_SESSION_NAME], $this->config['sqlSessionExpiry']);	
			}
			
			if ($this->db->blocker($user, $this->ini['bfattempts'], $this->ini['bftime'], true, 'f')) {
				$this->ccUserBlocked = true;
			} else {
				$data["user_id"] 	= $users[0]['user_id'];
				$data["ip"] 			= $this->db->mySQLSafe(get_ip_address());
				$data["browser"] 		= $this->db->mySQLSafe($_SERVER['HTTP_USER_AGENT']); 
				$update = $this->db->update($this->glob['dbprefix']."sessions", $data,"sessId=".$this->db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));
					$last_login=$this->db->mySQLSafe(gmdate("Y-m-d H:i:s"));
	$update = $this->db->update($this->glob['dbprefix']."users", array("last_login"=>$last_login),"user_id=".$users[0]['user_id']);
			httpredir($GLOBALS['rootRel']."dashboard.php");
		}
		} 
	}
	function authenticate2($user, $pass, $remember = false, $cookie_login = false) {
		if ($cookie_login) {
			$user		= sanitizeVar($_COOKIE['username']);
			$passMD5	= sanitizeVar($_COOKIE['password']); 
		} else {
			$user		= sanitizeVar($user);
			$passMD5	= md5(sanitizeVar($pass));
		}
		
		$query = "SELECT `user_id`, `salt` FROM ".$this->glob['dbprefix']."users WHERE `activated`>0 AND `username`=".$this->db->mySQLSafe($user);
		$salt = $this->db->select($query);
		if($salt[0]['user_id']>0 && empty($salt[0]['salt']) && $cookie_login == false) {
			$query = "SELECT `user_id` FROM ".$this->glob['dbprefix']."users WHERE username=".$this->db->mySQLSafe($user)." AND `password` = ".$this->db->mySQLSafe($passMD5)." AND activated>0";
		if($users = $this->db->select($query)) {
				$this->createSalt($user,$pass,$remember);
			} else {
				return false;
			}
		} else { 
			 $passMD5 = md5(md5($salt[0]['salt']).md5($pass));
			$query = "SELECT `user_id` FROM ".$this->glob['dbprefix']."users WHERE username=".$this->db->mySQLSafe($user)." AND password = ".$this->db->mySQLSafe($passMD5)." AND activated>0";
			$users = $this->db->select($query);
			
		}
		
		if (!$users) {
			if ($this->db->blocker($user, $this->ini['bfattempts'], $this->ini['bftime'], false, 'f')) {
				$this->ccUserBlocked = true; 	
			}
		} else if ($users[0]['user_id']>0) {
		
			// remember user for as long as sessions are allowed in DB
			if ($remember == true) {
				$this->set_cc_cookie('username', $user, $this->config['sqlSessionExpiry']);
				$this->set_cc_cookie('password', $passMD5, $this->config['sqlSessionExpiry']); 
				$this->set_cc_cookie(CC_SESSION_NAME, $GLOBALS[CC_SESSION_NAME], $this->config['sqlSessionExpiry']);	
			}
			
			if ($this->db->blocker($user, $this->ini['bfattempts'], $this->ini['bftime'], true, 'f')) {
				$this->ccUserBlocked = true;
			} else {
				$data["user_id"] 	= $users[0]['user_id'];
				$data["ip"] 			= $this->db->mySQLSafe(get_ip_address());
				$data["browser"] 		= $this->db->mySQLSafe($_SERVER['HTTP_USER_AGENT']); 
				$update = $this->db->update($this->glob['dbprefix']."sessions", $data,"sessId=".$this->db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));
					$last_login=$this->db->mySQLSafe(date("Y-m-d H:i:s"));
	$update = $this->db->update($this->glob['dbprefix']."users", array("last_login"=>$last_login),"user_id=".$users[0]['user_id']);
			httpredir($GLOBALS['rootRel']."dashboard.php");
		}
		} 
	}
	
	function makeSessId() {
		session_start();
		session_regenerate_id(true);
		return session_id();
	}
	
	/* defunct
	function get_cookie_domain($domain) {
		$cookie_domain = str_replace(array('http://', 'https://', 'www.'), '', strtolower($domain));
		$cookie_domain = explode("/", $cookie_domain);
		$cookie_domain = explode(":", $cookie_domain[0]);
		return '.'.$cookie_domain[0];
	}
	*/
	
	function set_cc_cookie($name, $value, $length = 0) {
		## only set the cookie if the visitor is not a spider or search engine system is off
		if (!$this->user_is_search_engine() || $this->config['sef'] == false) {
			$expires = ($length>0) ? (time()+$length) : 0;
			$urlParts = parse_url($GLOBALS['storeURL']);
			$domain = (empty($urlParts['host']) || !strpos($urlParts['host'], ".")) ? false : str_replace("www.",".",$urlParts['host']);
			
			setcookie($name, $value, $expires, $GLOBALS['rootRel'], $domain);
		}
	}
	
	function user_is_search_engine() {
		$user_agent		= strtolower($_SERVER['HTTP_USER_AGENT']);
		if (($user_agent != '') && (strtolower($user_agent) != 'null') && (strlen(trim($user_agent)) > 0)) {
			$spiders	= file(CC_ROOT_DIR.CC_DS.'spiders.txt');
			foreach ($spiders as $spider) {
				if (($spider != '') && (strtolower($spider) != 'null') && (strlen(trim($spider)) > 0)) {
					if (strpos($user_agent, trim($spider)) !== false) {
						$spider_flag	= true;
						break;
					}
				}
			}
		}
		return (isset($spider_flag)) ? true : false;
	}
}

?>
