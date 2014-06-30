<?php
	class_exists('Object') || require('lib/Object.php');
	class_exists('Table') || require('lib/DataStorage/Table.php');
	class_exists('Factory') || require('lib/DataStorage/Factory.php');
	class_exists('ByClause') || require('lib/DataStorage/ByClause.php');
	class_exists('ByAttribute') || require('lib/DataStorage/ByAttribute.php');
	class_exists('All') || require('lib/DataStorage/All.php');
	class Friend extends Object{
		public function __construct($attributes = null){
			parent::__construct($attributes);
		}
		public function __destruct(){
			parent::__destruct();
		}
		
		public $id;
		public $name;
		public $site;
		public $permission;
		public $timestamp;
		
		private static $errors;
		public static function getErrors(){
			return self::$errors;
		}
		
		public static function findAll(){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$list = $db->find(new All(null, null, 0, null), new Friend(null));
			$list = ($list == null ? array() : (is_array($list) ? $list : array($list)));
			return $list;
		}
		public static function findById($id = 0){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$friend = $db->find(new ById($id), new Friend(null));
			return $friend;
		}
		public static function findBySite($site = null){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$friend = $db->find(new ByAttribute('site', $site, 1, null), new Friend(null));
			return $friend;
		}
		public static function findByName($name = null){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$friend = $db->find(new ByAttribute('name', $name, 1, null), new Friend(null));
			return $friend;
		}
		
		public function getTableName($config = null){
			if($config == null){
				$config = new AppConfiguration();				
			}
			return $config->prefix . 'friends';
		}

		public static function save(Friend $friend){
			$config = new AppConfiguration();
			$errors = self::canSave($friend);
			if(count($errors) == 0){
				$db = Factory::get($config->db_type, $config);
				$db->save(null, $friend);
			}else{
				self::$errors = $errors;
			}
			
		}
		
		public static function canSave(Friend $friend){
			$errors = array();
			
			$duplicate = self::findBySite($friend->site);
			if($duplicate != null && $friend->id != $duplicate->id){
				$errors[] = 'This site already exists.';
			}
			
			if(empty($friend->name)){
				$errors[] = 'Name is required.';
			}

			if(empty($friend->site)){
				$errors[] = 'Site is required.';
			}
					
			return $errors;
		}
		
		public function install(Configuration $config){
			$message = '';
			$db = Factory::get($config->db_type, $config);
			try{
				$table = new Table($this->getTableName($config), $db);
				$table->addColumn('id', 'biginteger', array('is_nullable'=>false, 'auto_increment'=>true));
				$table->addColumn('name', 'string', array('is_nullable'=>false, 'default'=>'', 'size'=>255));
				$table->addColumn('site', 'string', array('is_nullable'=>false, 'default'=>'', 'size'=>255));
				$table->addColumn('permission', 'string', array('is_nullable'=>false, 'default'=>'', 'size'=>255));
				
				$table->addColumn('timestamp', 'timestamp', array('is_nullable'=>false));
				$table->addKey('primary', 'id');
				$table->addKey('key', array('name'=>'name'));
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