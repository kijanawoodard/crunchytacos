<?php
class_exists('Random') || require('lib/Random.php');
class_exists('AppResource') || require('AppResource.php');
class_exists('SubscriberResource') || require('SubscriberResource.php');
	class SubscriptionResource extends AppResource{
		public function __construct($attributes = null){
			parent::__construct($attributes);
		}
	
		public function __destruct(){
			parent::__destruct();
		}

		public $subscriber;		
		public function post_subscription(Subscriber $subscriber){
			$this->subscriber = Subscriber::findById($subscriber->id);
			if($this->subscriber == null){
				$this->subscriber = new Subscriber();
			}
			$this->subscriber->is_approved = false;
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
			return $this->renderView('subscription/success', null);
		}
	}
?>