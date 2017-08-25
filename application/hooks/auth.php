<?php

class Auth {
	
	private $CI;
	private $loginUri = 'welcome/login';
	private $loginAuth = FALSE;
	private $sessId = null;
	
	public function __construct(){
		$this->CI = &get_instance();
		$this->CI->load->helper('url');
		$this->initSession();
		$this->setLoginAuth();
	}
	
	
	public function initSession(){
		if(session_status()===1){
			session_name(SESS_NAME);
			if($this->sessId!==null){
				session_id($this->sessId);
			}
			$lifeTime = 24 * 3600; 
			session_set_cookie_params($lifeTime); 
			session_start();
		}
	}
	
	public function setLoginAuth(){
		if(!empty($_SESSION['admin_user_id'])){
			$this->loginAuth = TRUE;
		}
	}
	
	public function chkLoginAuth(){
		if(!$this->loginAuth && !in_array(uri_string(),array('login', 'login/ajaxsub'))){
			redirect($this->loginUri);
		}
		return TRUE;
	}
}