<?php
	class_exists('AppResource') || require('AppResource.php');
    class_exists('Configuration') || require('models/Configuration.php');
	class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
	class_exists('LoginResource') || require('LoginResource.php');
    class InstallResource extends AppResource{
        public function __construct(){
            parent::__construct();
        }
        
        public function __destruct(){
            parent::__destruct();
        }
        
        public $configuration;

        public function get_install(){
			if($this->config != null && $this->config->installed){
				FrontController::redirectTo(null);
			}
			$this->title = "App Installation";
			$this->output = $this->renderView('install/index', null);
			return $this->renderView('layouts/install', null);
        }

		public function get_install_configuration(){
			if(!array_key_exists('configuration', $_SESSION)){
				$_SESSION['configuration'] = serialize(new Configuration(array('user_name'=>'user name', 'password'=>'pasword', 'host'=>'localhost', 'prefix'=>'sixd_', 'database'=>'database', 'theme'=>'default', 'db_type'=>'MySql', 'email'=>'graphite@joeyguerra.com')));
			}
			
			$this->title = "Createa a Configuration File";
			$this->configuration = unserialize($_SESSION['configuration']);
			$this->output = $this->renderView('install/config', null);
			return $this->renderView('layouts/install', null);			
		}
		
		public function get_install_done(){
			$this->title = "Completed Installation";
			$this->output = $this->renderView('install/done', null);
			return $this->renderView('layouts/install', null);
        }

        
		private function createTables($db, Configuration $config){
			$didCreate = true;
			$errors = array();
			if(!$db->exists($config->database)){
				error_log('creating db...');
				$didCreate = $db->createDatabase($config->database);				
			}

			if(!$didCreate){
				$errors[] = 'Failed to create the database.';
			}else{
				$root = str_replace('resources', '', dirname(__FILE__));
				$folder = dir($root . 'models');
				$className = null;
				$reflector = null;
				error_log('installing schema...');
				while(($file = $folder->read()) !== false){
					error_log($file);
					if(preg_match('/^\./', $file) == 0){
						$className = str_replace('.php', '', $file);
						class_exists($className) || require('models/' . $file);
						$reflector = new ReflectionClass($className);
						if($reflector->hasMethod('install')){
							$model = $reflector->newInstanceArgs(array(null, null));
							try{$model->install($config);}catch(Exception $e){$errors[] = $e->getMessage();}
						}
					}
				}
				
				$folder->close();
				
				if(count($errors) == 0){
					
				}
								
			}
			return $errors;
		}

		public function post_install_settings(Configuration $config){
			// Set the dbType here. This should be the only place where we define the database type.
			$errors = array();
			$_SESSION['configuration'] = serialize($config);
			$this->configuration = $_SESSION['configuration'];
			$db = Factory::get($config->db_type, $config);

			try{
				$db->testConnection();
			}catch(Exception $e){
				$errors[] = $e->getCode() . ':' . $e->getMessage();
			}
			
			if(count($errors) > 0){
				try{
					$db->createDatabase($config->database);
					$errors = array();
				}catch(Exception $e){
					error_log('error message ' . $db->errorMessage);
				}
			}
			
			$errors = array_merge($config->validate(), $errors);
			try{
				if(count($errors) == 0){
					$config->site_password = LoginResource::encrypt($config->site_password);
					$config->installed = true;
					$config->save('AppConfiguration.php');
				}
			}catch(Exception $e){
				$errors[] = $e->getMessage();
			}
			
			if(count($errors) == 0){
				error_log('installing...');
				$errors = $this->createTables($db, $config);
				error_log('done!');
			}
			
			if(count($errors) > 0){
				$message = $this->renderView('install/error', array('message'=>"The following errors occurred when saving the configuration file. Please resolve and try again.", 'errors'=>$errors));					
				AppResource::setUserMessage($message);
				$this->output = $this->renderView('install/config', null);
				return $this->renderView('layouts/install', null);			
			}else{
				unset($_SESSION['configuration']);
				FrontController::redirectTo('install/done');
			}
        }
        
    }
?>