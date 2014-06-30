<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
class_exists('LoginResource') || require('LoginResource.php');
class DbResource extends AppResource{
	public function __construct(){
		if(!LoginResource::isAuthorized()){
			throw new Exception('401: Unauthorized', 401);
		}
		parent::__construct();
		$this->db = Factory::get($this->config->db_type, $this->config);
	}
	public function __destruct(){
		parent::__destruct();
	}
	private $db;
	public $databases;
	public $tables;
	public $field_name;
	public function get_db(){
		$this->databases = $this->db->getDatabases();
		$this->title = $this->config->database;
		$this->output = $this->renderView('db/index', null);
		return $this->renderView('layouts/db', null);
	}
	
	public function get_db_tables($db_name){
		$this->tables = $this->db->getTables($db_name);
		$this->field_name = "Tables_in_$db_name";
		$this->output = $this->renderView('db/tables', null);
		return $this->renderView(null);
	}
	public function forDisplay($text){
		$string = $text;
		if(strlen($string) > 26){
			$string = substr($string, 0, 25) . '...';
		}
		return $string;
	}
	public function showColumnsFor($db_name, $table_name){
		$col = $this->db->getColumns($db_name, $table_name);
		$this->view->setColumns($col);
		$this->view->setDb_name($db_name);
		$this->view->setTable_name($table_name);
		$this->view->addFileWithTheme('database/columns');
		return $this->view->render();
	}
	public function show($db_name){
		if($db_name == null)
			$db_name = $this->connectionArgs['databaseName'];
		try{
			$tables = $this->db->getTables($db_name);
		}catch(Exception $e){
			error_log($e->getMessage());
		}
		
		$this->view->setTables($tables);
		$this->view->setDb_name($db_name);
		$this->view->addFileWithTheme('database/tables');
		return $this->view->render();
	}
	public function create($db_name){
		$this->db->createDatabase($db_name);
		return $this->showDashboard();
	}

	public function post_db_query($query, $db_name){
		$query = str_replace('\\', '', $query);
		$this->db->useDatabase($db_name);
		$this->db->execute($query);
		$rows = $this->db->getRows();
		$this->output = $this->renderView('db/results', array('rows'=>$rows));
		return $this->renderView(null);
	}
	public function deleteDatabase($db_name){
		$this->db->deleteDatabase($db_name);
		return $this->showDashboard();
	}
	public function deleteTable($db_name, $table_name){
		if($table_name == 'users'){
			$this->setUser_id(null);
		}
		$this->db->useDatabase($db_name);
		$this->db->deleteTable($table_name);
		return $this->show($db_name);
	}
	protected function createFirstVersion(){
		$factory = new DatabaseFactory();			
		$db = $factory->get(Config::getInstance()->getDatabaseProvider(), $this->connectionArgs, Config::getInstance()->getLogPath());
		if(!$db->exists(Config::getInstance()->getDatabase()))
			$did_create = $db->createDatabase(Config::getInstance()->getDatabase());

	}
	
}

?>