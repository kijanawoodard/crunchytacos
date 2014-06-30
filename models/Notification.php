<?php
	class_exists('Object') || require('lib/Object.php');
	class_exists('Table') || require('lib/DataStorage/Table.php');
	class_exists('Factory') || require('lib/DataStorage/Factory.php');
	class_exists('ByClause') || require('lib/DataStorage/ByClause.php');
	class Notification extends Object{
		public function __construct($attributes = null){
			parent::__construct($attributes);
		}
		public function __destruct(){
			parent::__destruct();
		}
		
		public $id;		
		public $name;
		public $time;
		public $appName;
		public $timestamp;
		
		private static $errors;
		
		public function getTableName($config = null){
			if($config == null){
				$config = new AppConfiguration();
			}
			return $config->prefix . 'notifications';
		}
		
		public static function save(Notification $notification){
			$errors = self::canSave($notification);
			$config = new AppConfiguration();
			if(count($errors) == 0){
				$db = Factory::get($config->db_type, $config);
				$db->save(null, $notification);
				self::notify('didSaveNotification', $notification, $notification);
			}
			return $errors;
		}
		
		public static function findRecent(){
			$config = new AppConfiguration();
			$db = Factory::get($config->db_type, $config);
			$list = $db->find(new ByClause(sprintf("time >= '%s'", date(time())), null, 0, null), new Notification());
			return $list;
		}
		
		public static function canSave(Notification $notification){
			$errors = array();
			if(empty($notification->time)){
				$errors[] = 'Time is required.';
			}
			if(empty($notification->name)){
				$errors[] = 'Name is required.';
			}
			if(empty($notification->appName)){
				$errors[] = 'AppName is required.';
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
				$table->addColumn('time', 'datetime', array('is_nullable'=>true, 'default'=>null));
				$table->addColumn('appName', 'string', array('is_nullable'=>false, 'default'=>'', 'size'=>255));
				$table->addColumn('timestamp', 'timestamp', array('is_nullable'=>false));
				$table->addKey('primary', 'id');
				$table->addKey('key', array('appName'=>'appName'));
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