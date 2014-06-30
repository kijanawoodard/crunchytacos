<?php
	class_exists('Object') || require('lib/Object.php');
	abstract class SqlRelationship extends Object{
		public function __construct($args){
			parent::__construct($args);
		}
		public function __destruct(){
			parent::__destruct();
		}
		
		public $through;
		public $withWhom;
		public $columnNames;
		
		public abstract function joinStatement($obj);
		public function selectList($obj){
			$objectColumnNames = array();
			$names = array();
			$objectReflector = new ReflectionClass(get_class($obj));
			$objectProperties = $objectReflector->getProperties();
			$objectProperties = array_filter($objectProperties, array($this, 'isPublicProperty'));
			$reflector = null;
			$through_id = (is_array($this->through) ? $this->through[0] : $this->through);

			if($this->columnNames == null){
				$this->columnNames = array();
				$tableName = $this->withWhom->getTableName();
				$reflector = new ReflectionClass(get_class($this->withWhom));
				foreach($reflector->getProperties() as $property){
					if($property->isPublic() && $property->getName() != $through_id && !$objectReflector->hasProperty($property->getName()) && !is_object($this->withWhom->{$property->getName()})){
						$this->columnNames[] = sprintf("%s.%s", $tableName, $property->getName());
					}
				}
			}
			return $this->columnNames;
		}
		
		public function isPublicProperty($property){
			return $property->isPublic();
		}
	}
?>