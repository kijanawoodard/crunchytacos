<?php
	class_exists('Object') || require('lib/Object.php');
	class_exists('DataStorage') || require('lib/DataStorage/DataStorage.php');
	class_exists('Subscriber') || require('Subscriber.php');
	class Subscription extends Object{
		public function __construct($attributes = null){
			parent::__construct($attributes);
		}
		public function __destruct(){
			parent::__destruct();
		}
		
		public $subscriber;
		
		public static function save(Subscriber $subscriber){
			$errors = self::canSave($subscriber);
			$config = new AppConfiguration();
			if(count($errors) == 0){
				$db = Factory::get($config->db_type, $config);
				$db->save(null, $subscriber);
				self::notify('didSaveSubscription', $subscriber, $subscriber);
			}
			return $errors;
		}		
	}
?>