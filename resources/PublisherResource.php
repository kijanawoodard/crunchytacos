<?php
class_exists('Random') || require('lib/Random.php');
class_exists('AppResource') || require('AppResource.php');
class_exists('LoginResource') || require('LoginResource.php');
class_exists('Publisher') || require('models/Publisher.php');
class_exists('aes128') || require('lib/aes128lib/aes128.php');
	class PublisherResource extends AppResource{
		public function __construct($attributes = null){
			parent::__construct($attributes);
		}
	
		public function __destruct(){
			parent::__destruct();
		}

		public $publishers;
		public $publisher;
		public function get_publisher(Publisher $publisher){
			if(!LoginResource::isAuthorized()){
				FrontController::setRequestedUrl('publisher');
				throw new Exception('401: Unauthorized', 401);
			}
			if($publisher != null && $publisher->id > 0){
				$this->publisher = Publisher::findById($publisher->id);
				$this->title = 'Publisher: ' . $this->publisher->email;
				$this->output = $this->renderView('publisher/one', null);
				return $this->renderView('layouts/default', null);
			}else{
				$this->publisher = new Publisher();
				$this->title = "Add a publisher";
				$this->output = $this->renderView('publisher/one', null);
				return $this->renderView('layouts/default', null);
			}
			
		}
		public function get_publishers(){
			if(!LoginResource::isAuthorized()){
				FrontController::setRequestedUrl('publishers');
				throw new Exception('401: Unauthorized', 401);
			}
			$this->publishers = Publisher::findAll();
			if($this->publishers == null){
				$this->publishers = array();
			}
			$this->title = 'Publishers';
			$this->output = $this->renderView('publisher/index', null);
			return $this->renderView('layouts/default', null);
		}
		public function delete_publisher(Publisher $publisher){
			if(!LoginResource::isAuthorized()){
				throw new Exception('401: Unauthorized', 401);
			}
			Publisher::delete($publisher);
			FrontController::redirectTo('publishers');
		}
		
		public function post_publisher(Publisher $publisher){
			// Check if the public_key is set, then use that as verification.		
			if(!empty($publisher->public_key)){
				$this->publisher = Publisher::findByEmail(urldecode($publisher->email));
				if($this->publisher != null){
					$this->publisher->public_key = $publisher->public_key;
					$this->publisher->time = date(time());
					$errors = Publisher::save($this->publisher);
					if($errors != null && count($errors) > 0){
						foreach($errors as $key=>$error){
							error_log("$key=$error");
						}
					}
				}else{
					error_log('failed to find publisher = ' . urldecode($publisher->email));
				}
			}elseif(LoginResource::isAuthorized()){
				$this->publisher = $publisher;
				if($publisher->id > 0){
					$this->publisher = Publisher::findById($publisher->id);
					$this->publisher->is_approved = $publisher->is_approved;
					$this->publisher->email = $publisher->email;
					$this->publisher->url = $publisher->url;
				}
				$this->publisher->time = date('c');
				$errors = Publisher::save($this->publisher);
				if($errors != null && count($errors) > 0){
					self::setUserMessage('Failed to save publisher');
				}else{
					FrontController::redirectTo('publishers');
				}
			}
			return 'ok';
		}
	}
?>