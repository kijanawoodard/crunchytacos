<?php
class_exists('AppResource') || require('AppResource.php');
class LoginResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	
	public function get_login(){
		$this->title = 'Login';
		$this->output = $this->renderView('user/login');
		return $this->renderView('layouts/default');			

	}
	public function post_login($email, $password){
		$isAuthed = false;
		if(self::isAuthorized()){
			$isAuthed = true;
		}

		$isAuthed = self::doVerification($email, $password);

		if($isAuthed){
			if($email != null && !empty($email)){
				self::setAuthKey($email, $password);					
			}
			FrontController::redirectTo(FrontController::requestedUrl());
		}else{
			self::setUserMessage("Not a valid combo.");
			FrontController::redirectTo('login');
		}
	}
	
	public function get_login_logout(){
		unset($_COOKIE['authKey']);
		$_COOKIE['authKey'] = false;
		unset($_SESSION['authKey']);
		FrontController::redirectTo(null);
	}
	
	public static function authKey(){
		$sessionAuthKey = (array_key_exists('authKey', $_SESSION) && !empty($_SESSION['authKey']) ? $_SESSION['authKey'] : null);
		return $sessionAuthKey;
	}
	private static function setAuthKey($email, $password){
		$domain = $_SERVER['SERVER_NAME'];
		$encryptedValue = self::encrypt($email . $password);
		$didSet = setcookie( 'authKey', $encryptedValue, 0, '/', $domain, false, false);
		if(!$didSet){
			self::setErrorrs(array('failed to write the cookie.'));
		}	
		$_SESSION['authKey'] = $encryptedValue;
	}
	
	public static function isAuthorized(){
		$cookieAuthKey = (array_key_exists('authKey', $_COOKIE) && !empty($_COOKIE['authKey']) ? $_COOKIE['authKey'] : null);
		$sessionAuthKey = self::authKey();
		if($cookieAuthKey == null && $sessionAuthKey == null){
			return false;
		}else{
			return $cookieAuthKey == $sessionAuthKey;
		}
	}

	public static function doVerification($email, $password){
		$config = new AppConfiguration(null);
		$password = self::encrypt($password);
		return ($config->email == $email && $config->site_password == $password);
	}
	
	public static function encrypt($value){
		return sha1($value);
	}
}
?>