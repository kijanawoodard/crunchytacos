<?php
	class_exists('Object') || require('lib/Object.php');
	class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
	class Post extends Object{
		public function __construct($attributes = null){
			parent::__construct($attributes);
			$this->type = 'post';
			$this->is_published = false;
		}
		public function __destruct(){
			parent::__destruct();
		}
		
		public $id;
		public $title;
		public $type;
		public $body;
		public $source;
		public $url;
		public $description;
		public $date;
		public $custom_url;
		public $is_published;
		
		// I need a way to tell the data storage whether or not to add the id in the sql statement
		// when inserting a new record. This is it. The data storage should default it to false, so
		// if this method doesn't exist, it'll default to false.
		public function shouldInsertId(){
			return true;
		}
		public function willAddFieldToSaveList($name, $value){
			
			if($name == 'id' && empty($this->id)){
				error_log("$name=$value from post");
				return uniqid(null, true);
			}
			return $value;			
		}
		public static function findAll(){
			$config = new AppConfiguration();				
			$db = Factory::get($config->db_type, $config);
			$list = $db->find(new All(null, null, 0, null), new Post(null));
			$list = ($list == null ? array() : (is_array($list) ? $list : array($list)));
			return $list;
		}
		public static function findPublished($start, $limit, $sort_by, $sort_by_direction = 'desc'){
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			if(empty($sort_by)){
				$sort_by = $post->getTableName() . '.id';
			}
			$list = $db->find(new ByAttribute('is_published', true, array($start, $limit), array($sort_by=>$sort_by_direction)), $post);
			$list = ($list == null ? array() : (is_array($list) ? $list : array($list)));
			return $list;
		}
		public static function find($start, $limit, $sort_by, $sort_by_direction = 'desc'){
			$config = new AppConfiguration();
			$post = new Post(null);
			$db = Factory::get($config->db_type, $config);
			if(empty($sort_by)){
				$sort_by = $post->getTableName() . '.id';
			}
			$list = $db->find(new All(null, null, array($start, $limit), array($sort_by=>$sort_by_direction)), $post);
			$list = ($list == null ? array() : (is_array($list) ? $list : array($list)));
			return $list;
		}
		public static function findById($id = 0){
			$config = new AppConfiguration();				
			$db = Factory::get($config->db_type, $config);
			$post = $db->find(new ById($id), new Post(null));
			return $post;
		}
		
		public function getTableName($config = null){
			if($config == null){
				$config = new AppConfiguration();
			}
			return $config->prefix . 'posts';
		}

		public static function save(Post $post){
			$errors = self::canSave($post);
			$config = new AppConfiguration();
			if(count($errors) == 0){
				$db = Factory::get($config->db_type, $config);
				$db->save(null, $post);
				self::notify('didSavePost', $post, $post);
			}
			return $errors;
		}
		
		public static function canSave(Post $post){
			$errors = array();
			return $errors;
		}
		
		public function install(Configuration $config){
			$message = '';
			$db = Factory::get($config->db_type, $config);
			try{
				$table = new Table($this->getTableName($config), $db);
				$table->addColumn('id', 'string', array('is_nullable'=>false, 'size'=>255));
				$table->addColumn('title', 'string', array('is_nullable'=>true, 'default'=>'', 'size'=>255));
				$table->addColumn('type', 'string', array('is_nullable'=>true, 'default'=>'post', 'size'=>80));
				$table->addColumn('body', 'text', array('is_nullable'=>true, 'default'=>''));
				$table->addColumn('source', 'string', array('is_nullable'=>true, 'default'=>'', 'size'=>255));
				$table->addColumn('url', 'string', array('is_nullable'=>true, 'default'=>'', 'size'=>255));
				$table->addColumn('description', 'string', array('is_nullable'=>true, 'default'=>'', 'size'=>255));
				$table->addColumn('date', 'datetime', array('is_nullable'=>false));
				$table->addColumn('custom_url', 'string', array('is_nullable'=>true, 'default'=>'', 'size'=>255));
				$table->addColumn('is_published', 'boolean', array('is_nullable'=>true, 'default'=>false));
				$table->addColumn('timestamp', 'timestamp', array('is_nullable'=>false));		
				
				$table->addKey('primary', 'id');
				$table->addKey('key', array('title_key'=>'title'));
				$table->addKey('key', array('custom_url_key'=>'custom_url'));
				$table->addKey('key', array('is_published_key'=>'is_published'));
				$table->addOption('ENGINE=MyISAM DEFAULT CHARSET=utf8');
				$errors = $table->save();
				if(count($errors) > 0){
					foreach($errors as $error){
						$message .= $error;
					}
					throw new Exception($message);
				}
			}catch(Exception $e){
				$db->deleteTable($this->getTableName($config));
				throw $e;
			}
		}
		
	}
?>