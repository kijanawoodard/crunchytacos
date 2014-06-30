<?php
	class_exists('Object') || require('lib/Object.php');
	class TwitterTrendResponse extends Object{
		public function __construct($attributes = null){
			parent::__construct($attributes);
		}
		public function __destruct(){
			parent::__destruct();
		}
		
		public $as_of;
		public $trends;
	}
	
?>