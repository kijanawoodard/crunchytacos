<?php
class_exists('Friend') || require('models/Friend.php');
class_exists('AppResource') || require('AppResource.php');
class_exists('LoginResource') || require('LoginResource.php');
class FriendResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public $friends;
	public $friend;
	public function get_friends($name = null){
		if(!LoginResource::isAuthorized()){
			FrontController::setRequestedUrl('friends');
			FrontController::redirectTo('login');
		}
		$this->friends = null;
		if($name != null){
			$this->friends = Friend::findByName($name);
			$this->friends = ($this->friends == null ? array() : array($this->friends));
		}else{
			$this->friends = Friend::findAll();
		}
				
		$this->title = "You're roster";
		$this->output = $this->renderView('friend/index', null);
		return $this->renderView('layouts/default', null);
	}
	
	public function get_friend(Friend $friend = null){
		if(!LoginResource::isAuthorized()){
			throw new Exception('401: Unauthorized', 401);
		}
		if($friend != null){
			$this->friend = Friend::findById($friend->id);
		}
		if($this->friend != null){
			$this->title = $this->friend->name;
		}else{
			$this->friend = new Friend();
			$this->title = "Make a new friend";
		}
		$this->output = $this->renderView('friend/edit', null);
		return $this->renderView('layouts/default', null);
	}
	
	public function post_friend(Friend $friend){
		if(!LoginResource::isAuthorized()){
			throw new Exception('401: Unauthorized', 401);
		}
		$errors = $friend->save($friend);
		FrontController::redirectTo('friends');
	}
	
}
?>