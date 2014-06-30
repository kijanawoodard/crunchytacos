<?php
	class_exists('Resource') || require('lib/Resource.php');
	class_exists('UserResource') || require('resources/UserResource.php');
	class_exists('FrontController') || require('lib/FrontController.php');
	class AppResource extends Resource{
		public function __construct(){
			parent::__construct();
			if(isset($_SERVER['PHP_AUTH_USER'])){
				
			}
			$this->resource_css = 'css/'.strtolower(str_replace('Resource', '', get_class($this))) . '.css';
			if(file_exists(FrontController::themePath() . '/' . $this->resource_css)){
				$this->resource_css = FrontController::urlFor('themes') . $this->resource_css;
				$this->resource_css = sprintf('<link rel="stylesheet" type="text/css" href="%s" media="all" />', $this->resource_css);
			}else{
				$this->resource_css = null;
			}

			if(!class_exists('AppConfiguration')){
				if(get_class($this) != 'InstallResource'){
					FrontController::redirectTo('install', null);
				}
			}else{
				$this->config = new AppConfiguration();
			}
		}
		
		public function __destruct(){
			parent::__destruct();
		}
		protected $config;
		
		public $search_term;
		
		public function didFinishLoading(){
			AppResource::setUserMessage(null);
		}
		public function unauthorizedRequestHasOccurred(){
			FrontController::redirectTo('login');
		}
		
		public static function getUserMessage(){
			if(array_key_exists('userMessage', $_SESSION)){
				return $_SESSION['userMessage'];
			}else{
				return null;
			}
		}

		public static function setUserMessage($value){
			if($value == null){
				unset($_SESSION['userMessage']);
			}else{
				$_SESSION['userMessage'] = $value;
			}
		}
		
		public static function randomIndexWithWeights($weights) {
		    $r = mt_rand(1,1000);
		    $offset = 0;
		    foreach ($weights as $k => $w) {
		        $offset += $w*1000;
		        if ($r <= $offset) {
		            return $k;
		        }
		    }
		}
	}
?>