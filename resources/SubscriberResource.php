<?php
class_exists('Random') || require('lib/Random.php');
class_exists('AppResource') || require('AppResource.php');
class_exists('LoginResource') || require('LoginResource.php');
class_exists('Subscriber') || require('models/Subscriber.php');
class_exists('NotificationResource') || require('NotificationResource.php');
	class SubscriberResource extends AppResource{
		public function __construct($attributes = null){
			parent::__construct($attributes);
			Subscriber::addObserver(new NotificationResource());
		}
	
		public function __destruct(){
			parent::__destruct();
		}

		public $subscribers;
		public $subscriber;
		public function get_subscriber(Subscriber $subscriber){
			if(!LoginResource::isAuthorized()){
				FrontController::setRequestedUrl('subscriber');
				throw new Exception('401: Unauthorized', 401);
			}
			if($subscriber != null && $subscriber->id > 0){
				$this->subscriber = Subscriber::findById($subscriber->id);
				$this->title = 'Subscriber: ' . $this->subscriber->email;
				$this->output = $this->renderView('subscriber/one', null);
				return $this->renderView('layouts/default', null);
			}else{
				$this->subscriber = new Subscriber();
				$this->title = "Add a subscriber";
				$this->output = $this->renderView('subscriber/one', null);
				return $this->renderView('layouts/default', null);
			}
			
		}
		public function get_subscribers(){
			if(!LoginResource::isAuthorized()){
				FrontController::setRequestedUrl('subscribers');
				throw new Exception('401: Unauthorized', 401);
			}
			$this->subscribers = Subscriber::findAll();
			if($this->subscribers == null){
				$this->subscribers = array();
			}
			$this->title = 'Subscribers';
			$this->output = $this->renderView('subscriber/index', null);
			return $this->renderView('layouts/default', null);
		}
		public function delete_subscriber(Subscriber $subscriber){
			if(!LoginResource::isAuthorized()){
				throw new Exception('401: Unauthorized', 401);
			}
			Subscriber::delete($subscriber);
			FrontController::redirectTo('subscribers');
		}
		
		public function post_subscriber(Subscriber $subscriber){
			//TODO: I need to modify this to do what post_post does, allows posts with a public_key.
			if(!LoginResource::isAuthorized()){
				throw new Exception('401: Unauthorized', 401);
			}
			$this->subscriber = Subscriber::findById($subscriber->id);
			if($this->subscriber == null){
				$this->subscriber = new Subscriber();
			}
			$this->subscriber->is_approved = $subscriber->is_approved;
			$this->subscriber->email = $subscriber->email;
			$this->subscriber->url = urldecode($subscriber->url);
			$this->subscriber->time = date(time());
			if($this->subscriber->id == null || $this->subscriber->id == 0 || $this->subscriber->private_key == null){
				$this->subscriber->private_key = Random::getPassword();
				$this->subscriber->public_key = Random::getPassword();				
			}
			
			$errors = Subscriber::save($this->subscriber);
			if($errors != null && count($errors) > 0){
				error_log('subscriber failed to save');
				self::setUserMessage("Subscriber failed to save.");
			}
			FrontController::redirectTo('subscribers');
		}
	}
?>