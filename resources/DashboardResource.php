<?php
class_exists('LoginResource') || require('LoginResource.php');
class_exists('AppResource') || require('AppResource.php');
class DashboardResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	
	public function get_dashboard(){
		if(!LoginResource::isAuthorized()){
			FrontController::redirectTo('login');
		}		
		$this->title = "I'm mr. happy.";
		$this->output = $this->renderView('dashboard/index', null);
		return $this->renderView('layouts/dashboard', null);
	}
	
}

?>