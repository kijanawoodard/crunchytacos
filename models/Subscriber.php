<?php
	class_exists('Object') || require('lib/Object.php');
	class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
	class Subscriber extends Object{
		public function __construct($attributes = null){
			parent::__construct($attributes);
		}
		public function __destruct(){
			parent::__destruct();
		}
		
		public $id;		
		public $email;
		public $url;
		public $is_approved;
		public $private_key;
		public $public_key;
		public $time;
		public $timestamp;
		
		public function getTableName($config = null){
			if($config == null){
				$config = new AppConfiguration();
			}
			return $config->prefix . 'subscribers';
		}
		public static function delete(Subscriber $subscriber){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			return $db->delete(null, $subscriber);
		}
		public static function save(Subscriber $subscriber){
			$errors = self::canSave($subscriber);
			$config = new AppConfiguration();
			if(count($errors) == 0){
				$db = Factory::get($config->db_type, $config);
				$db->save(null, $subscriber);
				self::notify('didSaveSubscriber', $subscriber, $subscriber);
			}
			return $errors;
		}
		
		public static function findAll(){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$list = $db->find(new All(null, null, 0, null), new Subscriber());
			return $list;
		}
		
		public static function findById($id){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$list = $db->find(new ById($id), new Subscriber());
			return $list;
		}
		
		public static function canSave(Subscriber $subscriber){
			$errors = array();

			if(empty($subscriber->email)){
				$errors[] = 'Email is required.';
			}
			if(empty($subscriber->url)){
				$errors[] = 'Url is required.';
			}
			
			return $errors;
		}
		
		public function install(Configuration $config){
			$message = '';
			$db = Factory::get($config->db_type, $config);
			error_log('installing subscribers');
			try{
				$table = new Table($this->getTableName($config), $db);
				$table->addColumn('id', 'biginteger', array('is_nullable'=>false, 'auto_increment'=>true));
				$table->addColumn('email', 'string', array('is_nullable'=>false, 'default'=>'', 'size'=>255));
				$table->addColumn('url', 'string', array('is_nullable'=>false, 'default'=>'', 'size'=>255));
				$table->addColumn('private_key', 'string', array('is_nullable'=>false, 'size'=>255));
				$table->addColumn('public_key', 'string', array('is_nullable'=>false, 'size'=>255));
				$table->addColumn('is_approved', 'boolean', array('is_nullable'=>true, 'default'=>0));
				$table->addColumn('time', 'datetime', array('is_nullable'=>false));
				$table->addColumn('timestamp', 'timestamp', array('is_nullable'=>false));
				$table->addKey('primary', 'id');
				$table->addKey('key', array('url'=>'url'));
				$table->addKey('key', array('email'=>'email'));
				$table->addOption('ENGINE=MyISAM DEFAULT CHARSET=utf8');
				$errors = $table->save();
				if(count($errors) > 0){
					foreach($errors as $error){
						$message .= $error;
					}
					throw new Exception($message);
				}
			}catch(Exception $e){
				$db->deleteTable($this->tableName);
				throw $e;
			}
		}
		
	}
?>