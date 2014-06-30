<?php
	class_exists('Security') || require('lib/DataStorage/Security.php');
	class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
	class Sql{
		private $_config;
		private $_connectionId;
		private $_queryId;
		private $_relationships;
		private $_cachedSql;
		
		public $errorNumber;
		public $errorMessage;
		private $security;
		public function __construct($config){
			$this->_config = $config;
			$this->security = new Security();
		}
		public function __destruct(){}
		
		public function sql(){
			return $this->_cachedSql;
		}
		public function delete(FindCommand $command = null, $obj){
			$sql = '';
			$key = 'id';
			if(method_exists($obj, 'getPrimaryKey')){
				$key = $obj->getPrimaryKey();				
			}
			// delete all children specified in it's relationship collection.
			/*if(method_exists($obj, 'getRelationships')){
				foreach($obj->getRelationships as $relationship){
					switch(get_class($relationship)){
						case('HasMany'):
							$sql = $this->constructDelete(new ByClause("$relationship->parentId=" . $obj->getAttribute($key)), $relationship->child);
							$this->execute($sql);
							break;
					}
				}
			}*/
			
			if($obj->{$key} != null){
				$sql = $this->constructDelete($command, $obj);
			}elseif($command != null){
				$sql = $this->constructDelete($command, $obj);
			}else
				return 0;
			$this->execute($sql);
			$affected_rows = $this->getAffectedRows();
			return $affected_rows;
		}

		public function save(FindCommand $findType = null, $obj){
			// First determine if the id is set, if it is, then update, if not then insert.
			$sql = '';
			$key = 'id';
			if(method_exists($obj, 'getPrimaryKey')){
				$key = $obj->getPrimaryKey();				
			}
			
			$id = $obj->{$key};
			$id = empty($id) ? null : $id;
			if($id == null){
				$sql = $this->constructInsert($obj);
				$this->execute($sql);
				$obj->{$key} = $this->getInsertedId();
			}else{
				$sql = $this->constructUpdate($findType, $obj);
				$this->execute($sql);
			}
			return $obj;
		}
		private function getjoins($obj, $relationships){
			$joins = array();
			if(isset($relationships)){
				$joins = array();
				foreach($relationships as $relationship){
					switch(get_class($relationship)){
						case('HasA'):
							$r = new SqlHasA(array('withWhom'=>$relationship->withWhom, 'through'=>$relationship->through));
							$joins[] = $r->joinStatement($obj);
							break;
						case('HasMany'):
							$r = new SqlHasMany(array('withWhom'=>$relationship->withWhom, 'through'=>$relationship->through));
							$joins[] = $r->joinStatement($obj);
							break;
					}
				}
			}
			return implode(' ', $joins);
		}
		public function find(FindCommand $command, $obj){
			$sql = '';
			$records = null;
			$securedSql = $this->security->find($command, $obj);
			$object_to_populate = $obj;
			$key = 'id';
			$tableName = String::pluralize(String::decamelize(get_class($obj)));
			if(method_exists($obj, 'getPrimaryKey')){
				$key = $obj->getPrimaryKey();
			}
			
			if(method_exists($obj, 'getTableName')){
				$tableName = $obj->getTableName();
			}
			
			switch(get_class($command)){
				case('BySql'):
					$sql = $command->sql;
					break;
				case('ByClause'):
					$sql = $this->getSelectList($obj, $command->relationships);
					$sql .= ' from ' . $tableName;
					if($command->relationships != null){
						$sql .= ' ' . $this->getJoins($obj, $command->relationships);
					}
										
					if($command->clause != null){
						$sql .= '
where ' . $command->clause . (strlen($securedSql) > 0 ? ' and ' . $securedSql : '');
					}elseif($securedSql != null){
						$sql .= '
where ' . $securedSql;
					}
					break;
				case('ByIds'):
					if($command->ids != null && is_array($command->ids)){
						$sql = $this->getSelectList($obj, $command->relationships);
						$sql .= ' from ' . $tableName;
						$sql .= ' ' . $this->getJoins($obj, $command->relationships);
					}else{
						throw new Exception('Ids are null or not an array.');
					}
					$sql .= '
where ' . $key . ' in (' . implode(', ', $this->sanitize($command->ids)) . ')' . (strlen($securedSql) > 0 ? ' and ' . $securedSql : '');
					break;
				case('ByAttribute'):
					$sql = $this->getSelectList($obj, $command->relationships);
					$sql .= ' from ' . $tableName;
					$sql .= ' ' . $this->getJoins($obj, $command->relationships);
					if(isset($command->value)){
						if(is_array($command->value)){
							if(ctype_digit($command->value[0])){
								$sql .= '
where ' . $command->name . ' in (' . implode(', ', $this->sanitize($command->value)) . ')' . (strlen($securedSql) > 0 ? ' and ' . $securedSql : '');
							}else{
								$sql .= '
where ' . $command->name . ' in (\'' . implode('\', \'', $this->sanitize($command->value)) . '\')' . (strlen($securedSql) > 0 ? ' and ' . $securedSql : '');
							}
						}else{
							$sql .= '
where ' . $command->name . '=\'' . $this->sanitize($command->value) . '\'';
						}
					}else{
						throw new Exception('ByAttribute:value is null.');
					}
					break;
				case('ById'):				
					$sql = $this->getSelectList($obj, $command->relationships);
					$sql .= ' from ' . $tableName;
					$sql .= ' ' . $this->getJoins($obj, $command->relationships);
					if($command->id == null)
						$sql .= '
where ' . $key . '=' . $this->typeIt($obj->{$obj->primaryKey}) . (strlen($securedSql) > 0 ? ' and ' . $securedSql : '');
					else
						$sql .= '
where ' . $key . '=' . $this->typeIt($this->sanitize($command->id)) . (strlen($securedSql) > 0 ? ' and ' . $securedSql : '');
					break;
				case('All'):
					if($command->sql != null){
						$sql .= $command->sql . (strlen($securedSql) > 0 ? ' and ' . $securedSql : '');
					}else{
						$sql = $this->getSelectList($obj, $command->relationships);
						$sql .= ' from ' . $tableName;
						$sql .= ' ' . $this->getJoins($obj, $command->relationships);
						$sql .= (strlen($securedSql) > 0) ? '
where ' . $securedSql : '';
					}
					break;
				default:
					throw new Exception('Invalid find type.');
					break;
			}
			//$object_to_populate = $this->getObjectToPopulate($command->relationships, $object_to_populate);
			$sql .= $this->addOrderBy($command);
			$sql .= $this->addLimit($command->limit);
			$this->execute($sql);
			$records = $this->getRecords($object_to_populate);
			if(count($records) > 0){
				return (count($records) == 1 && $command->limit == 1) ? $records[0] : $records;
			}else{
				return null;
			}
		}
		
		private function getObjectToPopulate($relationship, $object_to_populate){
			if($relationship != null){
				$object_to_populate = $relationship->withWhom;
			}
			return $object_to_populate;
		}
		private function addOrderBy($command){
			$sql = null;
			$order_by = array();
			if($command->order_by != null){
				foreach($command->order_by as $column_name=>$direction){
					$order_by[] = $column_name . ($direction == 'desc' ? ' desc' : ' asc');
				}
				$sql .= ' order by ' . implode(', ', $order_by);
			}
			return $sql;
		}
		private function addLimit($limit = 0){
			if(!is_array($limit)){
				if($limit > 0){
					return ' limit ' . $limit;
				}else{
					return null;
				}
			}else{
				return " limit $limit[0], $limit[1]";
			}
		}
		private function getSelectList($obj, $relationships){
			$sql = 'select ';
			$sql .= $obj->getTableName() . '.*';
			$SqlRelationship = null;
			if($relationships != null){
				foreach($relationships as $relationship){
					switch(get_class($relationship)){
						case('HasA'):
							$SqlRelationship = new SqlHasA(array('withWhom'=>$relationship->withWhom, 'through'=>$relationship->through));
							$list[] = $SqlRelationship->selectList($obj);
							break;
						case('HasMany'):
							$SqlRelationship = new SqlHasMany(array('withWhom'=>$relationship->withWhom, 'through'=>$relationship->through));
							$list[] = $SqlRelationship->selectList($obj);
							break;
					}
				}
				if(count($list)>0){
					$sql .= ', ';
				}
				foreach($list as $columns){
					$sql .= implode(', ', $columns);
				}
			}			
			return $sql;
		}
		public function find_with_relationships($args, $source){
			if(count($args) > 0){
				if($args[0] == 'findBySql'){
					$this->findBySql($args['sql']);
					return $this->getRecords($source);					
				}
				if(array_key_exists('relationship', $args)){
					$relationship = $args['relationship'];
					$sql = sprintf('select %s.* from %s inner join %s on %s.%s=%s.%s where %s.%s=%s'
						, $relationship->parentTable(), $relationship->parentTable()
						, $relationship->childTable(), $relationship->childTable()
						, $relationship->foreignKey(), $relationship->parentTable()
						, $relationship->parentPrimaryKey(), $relationship->parentTable()
						, $relationship->parentPrimaryKey(), $this->sanitize($this->{$relationship->parentPrimaryKey()}));
					if(array_key_exists('conditions', $args)){
						$sql .= $this->sanitize($args['conditions']);
					}
					$this->findBySql(sql);
					return $this->getRecords($source);
				}
			}
			$sql = $this->constructSql($args, $source);
			$this->findBySql($sql);
			return $this->getRecords($source);
		}
		public function findBySql($sql){
			$this->execute($sql);
		}
		public function useDatabase($databaseName){
			if($databaseName != null && $this->_connectionId != null){
				try{
                    $this->_config->database = $databaseName;
					$this->setError(null);
					if($this->errorNumber > 0){
						throw new Exception("There was a problem using database named '{$this->_config->database}'. {$this->errorNumber}: {$this->errorMessage}
						", $this->errorNumber);		
					}
				}catch(Exception $e){
					throw $e;
				}
			}
		}
		
		public function createDatabase($databaseName){
			if(!$this->exists($databaseName)){
				$sql = "CREATE DATABASE $databaseName";
				$this->execute($sql);
				return true;
			}
			return false;
		}
		
		public function install($table){
			if(!$this->tableExists($table)){
				$sql = <<<eos
CREATE TABLE {$table->name} (
%s
) %s
eos;
				$columnBuilder = array();
				foreach($table->columns as $column){
					$columnBuilder[] = "{$column->name} {$column->type}{$column->getSize()}{$column->getIsNullable()}{$column->getDefault()} {$column->getOptions()}";
				}

				if($table->keys != null && count($table->keys) > 0){
					if(array_key_exists('PRIMARY KEY', $table->keys)){
						if(is_array($table->keys['PRIMARY KEY'])){
							$columnBuilder[] = 'PRIMARY KEY (' . implode($table->keys['PRIMARY KEY'], ", ") . ')';
						}else{
							$columnBuilder[] = "PRIMARY KEY ({$table->keys['PRIMARY KEY']}";
						}
					}

					if(array_key_exists('KEY', $table->keys)){
						$columnBuilder[] = 'KEY ' . $table->keys['KEY'][0] . '(' . $table->keys['KEY'][1] . ')';
					}
				}

				$sql = sprintf($sql, implode(', ', $columnBuilder) . ')', ($table->options != null) ? implode(' ', $table->options) : '');
				$this->execute($sql);
			}
		}
		public function count($obj){
			$sql = sprintf('select count(' . $obj->primaryKey .') as NumberOfRecords from ' . $obj->tableName);
			$this->execute($sql);
			$numberOfRecords = 0;
			if($this->_queryId){
				while($row = Sql_fetch_object($this->_queryId)){
					$numberOfRecords = $row->NumberOfRecords;
				}
			}else{
				throw new Exception('Sql error: ' . $this->errorNumber . '=' . $this->errorMessage);
			}
			return $numberOfRecords;
		}
		public function deleteDatabase($databaseName){
			if($this->exists($databaseName) && !in_array($databaseName, array('information_schema', 'Sql'))){
				$sql = "DROP SCHEMA $databaseName";
				$this->execute($sql);
				return true;
			}
			return false;
		}
		public function columnExists($tableName, $columnName){
			$columns = $this->getColumns($tableName);			
			foreach($columns as $column){
				if($column->Field == $columnName)
					return true;
			}
			return false;
		}
		
		public function testConnection(){
			$this->connect(null);				
		}
		
		public function tableExists($name){
			$sql = 'SHOW TABLES';
			$this->execute($sql);
			$rows = $this->getRows();
			if($rows != null){
				foreach($rows as $table){
					if($table->{'Tables_in_'.$this->_config->database} == $name){
						return true;
					}
				}
			}
			return false;
		}
		public function exists($name){
			$test = 'SHOW DATABASES';
			$this->execute($test);
			$rows = $this->getRows();
			foreach($rows as $row=>$database){
				if($database->Database == $name){
					return true;
				}
			}
			return false;
		}
		public function deleteTable($tableName){
			$sql = 'DROP TABLE ' . $tableName;
			if($this->tableExists($tableName)){	
				$this->execute($sql);
				return true;
			}
			return false;
		}
		private function constructDelete(FindCommand $findType = null, $obj){
			$sql = "DELETE FROM {$obj->getTableName()}";
			$key = 'id';
			if(method_exists($obj, 'getPrimaryKey')){
				$key = $obj->getPrimaryKey();
			}
			$securedSql = $this->security->delete($obj);
			switch(get_class($findType)){
				case('ById'):
					$sql .= " WHERE {$key} = {$findType->id}";
					break;
				case('ByIds'):
					$sql .= " WHERE {$key} in(" . implode(', ', $findType->ids) . ")";
					break;
				case('ByClause'):
					$sql .= ' WHERE ' . $findType->clause;
					break;
				case('All'):
					throw new Exception('All not implemented for delete.');
					break;
				default:
					$sql .= " WHERE {$key} = " . $this->typeIt($obj->{$key});
					break;
			}
			$sql .= (strlen($securedSql) > 0) ? ' ' . $securedSql : '';
			return $sql;
		}
		private function constructInsert($obj){
			$value = null;
			$tableName = $obj->getTableName();
			$reflector = new ReflectionClass(get_class($obj));
			$format = <<<eos
INSERT INTO {$tableName} (%s)
values (%s)
eos;
			$sql = '';
			$columns = array();
			$values = array();
			$key = 'id';

			if(is_object($obj)){
				if(method_exists($obj, 'getPrimaryKey')){
					$key = $obj->getPrimaryKey();
				}
				
				foreach($reflector->getProperties() as $property){
					$name = $property->getName();
					$value = $obj->{$name};
					if($property->isPublic() && $value != null
						&& $name != $key
						&& !is_object($value)
						&& !is_array($value)){
						
						$columns[] = $name;
						$values[] = $this->typeIt($value);
					}
				}
			}
			
			return sprintf($format, implode(', ', $columns), implode(', ', $values));
		}
		private function constructUpdate(FindCommand $findType = null, $obj){
			$id = null;
			$value = null;
			$name = '';
			$tableName = $obj->getTableName();
			$reflector = new ReflectionClass(get_class($obj));
			$key = 'id';
			if(method_exists($obj, 'getPrimaryKey')){
				$key = $obj->getPrimaryKey();
			}
			$format = <<<eos
UPDATE {$tableName} SET %s
eos;

			if($findType != null){
				switch(get_class($findType)){
					case('Clause'):
						$format .= " WHERE $findType->clause";
						break;
				}
			}else{
				$format .= " WHERE " . $key . " = %s";
			}

			$sql = '';
			$columns = array();
			$values = array();
			$getter = null;
			$key = 'id';
			if(is_object($obj)){
				if(method_exists($obj, 'getPrimaryKey')){
					$key = $obj->getPrimaryKey();
				}
				
				$id = $obj->{$key};
				foreach($reflector->getProperties() as $property){
					$name = $property->getName();
					$value = $obj->{$name};
					if($property->isPublic() && $value != null && $name != $key
						&& !is_object($value)
						&& !is_array($value)){

						$values[] = $name . '=' . $this->typeIt($value);
					}
				}
			}
			return sprintf($format, implode(', ', $values), $this->typeIt($id));
		}
		private function castIt($value){
			if(ctype_digit($value))
				return (int)$value;
			else
				return $value;
		}
		private function typeIt($value){
			if(is_bool($value))
				return ($value) ? 1 : 0;
			else if(is_null($value))
				return 'NULL';
			else if(is_float($value))
				return $value;
			else if(is_int($value))
				return $value;
			else if(is_string($value)){
				if(is_numeric($value))
					return $value;
				else
					return "'" . $this->sanitize($value) . "'";
			}
			else if(is_object($value))
				return $value;
			else if(is_array($value))
				return $value;
			else
				return $value;
		}
		private function constructSelect($args, $source){
			$sql = 'select ' . $source->getTableName() . '.* from ' . $source->getTableName();
			if(array_key_exists('conditions', $args))
				$sql .= ' where ' . $this->sanitize($args['conditions']);
			return $sql;
		}
		public function getColumns($table_name){
			$this->connect(null);
			$sql = "show columns from $table_name";
			$this->execute($sql);
			$records = $this->getRows();
			if(count($records) > 0){
				return (count($records) == 1) ? $records[0] : $records;
			}else{return null;}
		}
		public function getDatabases(){
			$this->connect(null);
			$sql = 'show databases';
			$this->execute($sql);
			$records = $this->getRows();
			if(count($records) > 0){
				return (count($records) == 1) ? $records[0] : $records;
			}else{return null;}
		}
		public function getTables($db_name){
			$this->connect(null);
			$sql = "show tables from $db_name";
			$this->execute($sql);
			$records = $this->getRows();
			if(count($records) > 0){
				return (count($records) == 1) ? $records[0] : $records;
			}else{return null;}
		}
		private function connect($config = null){
			if($config != null)
				$this->_config = $config;
			if($this->_config == null){
				throw new Exception('Connection object is not set.');
			}
			if($this->_connectionId == null){
				$this->_connectionId = odbc_connect(sprintf('DRIVER={SQL Server Native Client 10.0};SERVER=%s;DATABASE=%s', $this->_config->host, $this->_config->database), $this->_config->user_name, $this->_config->password);
			}

			if($this->_connectionId == false){
				$this->setError(null);
				throw new DSException(new Exception('The connection to the Sql server failed. Please check your user name and password again and that the database has been created. Sql ERROR: ' . $this->errorMessage, 0));
				
			}

			try{
				if($this->_config->database != null){
					$this->useDatabase($this->_config->database);
				}
			}catch(Exception $e){
				$this->setError($e);
				throw new DSException(new Exception('Database does not exist.', 0));
			}
			
		}
		
		private function setError($e){
			$this->errorNumber = odbc_error();
			$this->errorMessage = odbc_errormsg() . $e;
		}
		public function disconnect($queryId = null){
			if($queryId != null)
				Sql_free_result($queryId);
			elseif($this->_queryId != null)
				Sql_free_result($this->_queryId);
		}
		public function getRows(){
			$rows = null;
			if($this->_queryId){
				while($row = Sql_fetch_object($this->_queryId)){
					$rows[] = $row;
				}
				//$this->disconnect(null);
			}else{
				throw new Exception('Sql error: ' . $this->errorNumber . '=' . $this->errorMessage);
			}
			return $rows;
		}
		private function populateObjectWithRow($obj, $row){
			$attributes = get_object_vars($row);
			foreach($attributes as $attribute=>$value){
				$value = $this->castIt($value);
				$obj->{$attribute} = $value;
			}
			return $obj;
		}
		private function getRecords(Object $object_to_populate){
			$records = array();
			$attributes = array();
			$className = get_class($object_to_populate);
					
			if($this->_queryId){
				while($row = Sql_fetch_object($this->_queryId)){
					// Populate the object hiearchy with the result columns.
					$attributes = get_object_vars($row);
					$model = new $className(null);
					
					$r = new ReflectionClass($className);
					$properties = $r->getProperties();
					foreach($properties as $property){
						$name = $property->getName();
						if($property->isPublic()){
							if(is_object($model->{$name})){
								$model->{$name} = $this->populateObjectWithRow($model->{$name}, $row);
							}elseif(array_key_exists($name, $attributes)){
								$value = $this->castIt($row->$name);
								$model->{$name} = $value;
							}
						}
					}

					/*
					$attributes = get_object_vars($row);
					$model = new $className(null);
					$model->setObservers($object_to_populate->getObservers());
					foreach($attributes as $attribute=>$value){
						$value = $this->castIt($value);
						$model->setAttribute($attribute, $value);
					}
					*/
					$records[] = $model;
				}
			}else{
				throw new Exception('Sql error: ' . $this->errorNumber . '=' . $this->errorMessage);
			}
			return $records;
		}
		private function getInsertedId(){
			return Sql_insert_id($this->_connectionId);
		}
		private function getAffectedRows(){
			return Sql_affected_rows($this->_connectionId);
		}
		public function execute($sql){
			$this->connect($this->_config);
			$this->_cachedSql = $sql;
			$this->_queryId = odbc_exec($this->_connectionId, $this->_cachedSql);				
			$this->setError(null);
			if($this->errorNumber > 0){
				error_log('Sql execute error: ' . $this->errorNumber . ' ' . $this->errorMessage);
				if($this->errorNumber == 1146){
					throw new DSException(new Exception($this->errorMessage, $this->errorNumber));
				}else{
					throw new Exception($this->errorMessage . '>>>' . $sql, $this->errorNumber);					
				}
			}
		}
		private function sanitize($sql){
			$sql = str_ireplace("'", "''", $sql);
			$sql = str_ireplace(";", "&semi;", $sql);
			return $sql;
		}
		private function unsanitize($text){
			$text = str_ireplace("''", "'", $text);
			$text = str_ireplace("&semi;", ";", $text);
			return $text;
		}
		public function getTable($name){
			return new SqlTable($name);
		}
	}
?>
