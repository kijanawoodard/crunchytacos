<?php
    class_exists('Object') || require('lib/Object.php');
    class Configuration extends Object{
        public function __construct($attributes = null){
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
		
		public function validate(){
			$errors = array();
			if(!isset($this->user_name)){
				$errors[] = 'User name is required.';
			}
			
			if(!isset($this->password)){
				$errors[] = 'Password is required.';
			}
			
			if(!isset($this->site_password)){
				$errors[] = 'Site Password is required.';
			}elseif(strlen($this->site_password) < 5){
				$errors[] = 'Site Password needs to be something.';
			}
			
			if(!isset($this->host)){
				$errors[] = 'Host is required.';
			}
			
			return $errors;
		}
		
		public function save($location){
			$text = <<<eos
<?php
	class_exists('Object') || require('lib/Object.php');
	class AppConfiguration extends Object{
		public function __construct(\$attributes = null){
			parent::__construct(\$attributes);
        }
        public function __destruct(){
            parent::__destruct();
        }
        public \$user_name;
        public \$password;
        public \$host;
        public \$database;
        public \$prefix;
		public \$db_type;
		public \$theme;
		public \$email;
		public \$site_password;
		public \$ssl_path;
		public \$site_path;
		public \$installed;

	}
?>
eos;
			$text = $this->map($this, $text);
			if($text != null){
				$result = file_put_contents($location, $text);				
				if($result === false){
					throw new Exception("Failed to create the configuration file. Please make the install directory writable. You might want to set the permissions back to what they were after installation.", 500);
				}
				chmod($location, 0755);
			}
		}
		
		public function map($obj, $text){
			$pos = strpos($text, 'parent::__construct($attributes);');
			if($pos !== false){
				$first = substr($text, 0, $pos-1);
				$last = substr($text, $pos, strlen($text)-1);
				$middle = '	$this->user_name = \''. $obj->user_name . '\';
			$this->password = \''. $obj->password . '\';
			$this->host = \''. $obj->host . '\';
			$this->database = \''. $obj->database . '\';
			$this->prefix = \''. $obj->prefix . '\';
			$this->db_type = \''. $obj->db_type . '\';
			$this->theme = \''. $obj->theme . '\';
			$this->email = \''. $obj->email . '\';
			$this->site_password = \''. $obj->site_password . '\';
			$this->ssl_path = \''. $obj->ssl_path . '\';
			$this->site_path = \''. $obj->site_path . '\';
			$this->installed = '. ($obj->installed ? 'true' : 'false') . ';
			';
				$text = $first . $middle . $last;
				return $text;
			}
			return null;
		}
    }
?>