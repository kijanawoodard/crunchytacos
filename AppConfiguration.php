<?php
	class_exists('Object') || require('lib/Object.php');
	class AppConfiguration extends Object{
		public function __construct($attributes = null){
			$this->user_name = 'kijcpp_eater';
			$this->password = '+QC-}r(&ztKL';
			$this->host = 'localhost';
			$this->database = 'kijcpp_crunchytacos';
			$this->prefix = 'sixd_';
			$this->db_type = 'MySql';
			$this->theme = 'default';
			$this->email = 'graphite@joeyguerra.com';
			$this->site_password = 'eb60db737c6b06e9b97cda9b4b2b2d2f75665fe9';
			$this->ssl_path = '';
			$this->site_path = '';
			$this->installed = true;
			parent::__construct($attributes);
        }
        public function __destruct(){
            parent::__destruct();
        }
        public $user_name;
        public $password;
        public $host;
        public $database;
        public $prefix;
		public $db_type;
		public $theme;
		public $email;
		public $site_password;
		public $ssl_path;
		public $site_path;
		public $installed;

	}
?>