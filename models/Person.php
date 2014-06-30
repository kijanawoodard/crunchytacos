<?php
	class_exists('Object') || require('lib/Object.php');
	class_exists('Table') || require('lib/DataStorage/Table.php');
	class_exists('Factory') || require('lib/DataStorage/Factory.php');
	class_exists('All') || require('lib/DataStorage/All.php');
	class_exists('ById') || require('lib/DataStorage/ById.php');
	class_exists('ByClause') || require('lib/DataStorage/ByClause.php');
	class Person extends Object{
		public function __construct($attributes = null){
			parent::__construct($attributes);
		}
		public function __destruct(){
			parent::__destruct();
		}
		
		private $relationships;
		
		public $id;
		public $session_id;
		public $name;
		public $email;
		public $should_subscribe;
		public $phone;
		public $timestamp;
		
		private static $errors;
		
		
		public function getTableName(){
			$config = new AppConfiguration();
			return $config->prefix . 'people';
		}
				
		public static function save(Person $person, &$errors){
			$config = new AppConfiguration();
			$errors = self::canSave($person);
			if(count($errors) == 0){
				$db = Factory::get($config->db_type, $config);
				$db->save(null, $person);
			}
		}
		public static function canSave(Person $person){
			$errors = array();
			return $errors;
		}
		
		public function install(Configuration $config){
			/*$message = '';
			$db = Factory::get($config->db_type, $config);
			try{
				$table = new Table($config->prefix . 'Orders', $db);
				$table->addColumn('PersonID', 'integer', array('is_nullable'=>false, 'auto_increment'=>true));
				
				$table->addColumn('UserName', 'string', array('is_nullable'=>false, 'size'=>80));
				$table->addColumn('Password', 'string', array('is_nullable'=>false, 'default'=>'', 'size'=>8));
				$table->addColumn('Fraudulent', 'boolean', array('is_nullable'=>false, 'default'=>false));
				$table->addColumn('FirstName', 'string', array('is_nullable'=>false, 'default'=>'', 'size'=>80));
				$table->addColumn('MiddleName', 'string', array('is_nullable'=>false, 'default'=>'', 'size'=>50));
				$table->addColumn('LastName', 'string', array('is_nullable'=>false, 'default'=>'', 'size'=>80));
				$table->addColumn('timestamp', 'timestamp', array('is_nullable'=>false, 'default'=>''));
				
				$table->addKey('primary', 'PersonID');
				$table->addKey('key', array('UserName'=>'UserName'));
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
			}*/
		}
	}
?>