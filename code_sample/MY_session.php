<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------

/**
 * Custom Native Session Class
 *
 * @package		Custom Library
 * @author		Colin Zhao
 * @link		glcolin@hotmail.com
 * @since		Version 1.0
 * @filesource
 */
class MY_Session{
	
	private $cookie_domain = '';
	private $flashdata_key = 'MY_flash';
	
	public function __construct(){
		//Load config variables:
		$CI =& get_instance();
		$this->cookie_domain = $CI->config->item('cookie_domain'); 
		//Set session cross sub domain:
		//session_set_cookie_params(0, '/', $this->cookie_domain);
		ini_set("session.cookie_domain", $this->cookie_domain);
		//Start session if it is not already started:
		@session_start();
	}//END
	
	function sess_destroy(){
		session_destroy();
	}//END
	
	function userdata($item){
		//Special cases:
		if($item=='session_id'){
			return session_id();
		}
		return ( ! isset($_SESSION[$item])) ? FALSE : $_SESSION[$item];
	}//END
	
	function all_userdata(){
		return $_SESSION;
	}//END
	
	function set_userdata($newdata = array(), $newval = ''){
		if (is_string($newdata)){
			$newdata = array($newdata => $newval);
		}
		if (count($newdata) > 0){
			foreach ($newdata as $key => $val){
				$_SESSION[$key] = $val;
			}
		}
	}//END
	
	function unset_userdata($newdata = array()){
		if (is_string($newdata)){
			$newdata = array($newdata => '');
		}
		if (count($newdata) > 0){
			foreach ($newdata as $key => $val){
				unset($_SESSION[$key]);
			}
		}
	}//END
	
	function set_flashdata($newdata = array(), $newval = ''){
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$flashdata_key = $this->flashdata_key.$key;
				$this->set_userdata($flashdata_key, $val);
			}
		}
	}//END
	
	function flashdata($key){
		$flashdata_key = $this->flashdata_key.$key;
		$data = $this->userdata($flashdata_key);
		$this->unset_userdata($flashdata_key);
		return $data;
	}//END
	
    function cookie($key){ 
        return (isset($_COOKIE[$key]))?$_COOKIE[$key]:FALSE;
    }//END
	
    function set_cookie($key,$value,$duration=99999999){
        setcookie($key, $value, time() + $duration, '/', $this->cookie_domain);
    }//END
	
    function unset_cookie($key){
        setcookie($key, '' , time() - 3600, '/', $this->cookie_domain);
    }//END
	
}
	