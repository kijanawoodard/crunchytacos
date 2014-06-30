<?php
class_exists('AppResource') || require('AppResource.php');
class EmptyResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function get_empty(){		
		return '';
	}
}

?>