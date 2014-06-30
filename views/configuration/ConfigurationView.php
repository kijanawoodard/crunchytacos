<?php
	class_exists('ViewController') || require('lib/ViewController.php');
	class ConfigurationView extends ViewController{
		public function __construct($attributes = null){
			parent::__construct($attributes);
		}
		public function __destruct(){
			parent::__destruct();
		}
		
		public $configuration;
	}
?>