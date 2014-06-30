<?php
	class_exists('NotificationResource') || require('NotificationResource.php');
	class_exists('AppResource') || require('AppResource.php');
	class TwitterResource extends AppResource{
		public function __construct(){
			parent::__construct();
		}
		public function __destruct(){
			parent::__destruct();
		}
		public $trends;
		public $search_response;
		public function get_twitter($query = null){
			if(strlen($query) > 0){
				$url = 'http://search.twitter.com/search.json?q=' . urlencode($query);
				$json = NotificationResource::doRequest($url, null, null, 'get', null);
				$json = str_replace('\\"', '', $json);
				$this->search_response = json_decode($json);
				$this->title = "Twitter Query for $query";
				$this->output = $this->renderView('twitter/search_results', null);
			}else{
				$url = 'http://search.twitter.com/trends/current.json';
				$json = NotificationResource::doRequest($url, null, null, 'get', null);
				$json = str_replace('\\"', '', $json);
				$this->trends = json_decode($json);
				$this->title = "Current Twitter Trends";
				$this->output = $this->renderView('twitter/index', null);
			}
			return $this->renderView('layouts/default', null);
		}
	
		public function linkify_tweet($tweet) {
		    $tweet = preg_replace('/(^|\s)@(\w+)/', '\1@<a href="http://www.twitter.com/\2">\2</a>', $tweet);
		    //return preg_replace('/(^|\s)#(\w+)/', '\1#<a href="http://search.twitter.com/search?q=%23\2">\2</a>', $tweet);
			/*$matches = array();
			preg_match('/(^|\s)#(\w+)/', $tweet, &$matches);
			$url = '';
			$link = '';
			for($i = 1; count($matches) - 1; $i++){
				$value = $matches[$i];
				$url = FrontController::urlFor('twitter', array('query'=>$value), false);
				$link = sprintf('<a href="%s" title="%s">%s</a>', $url, $value, $value);
				$tweet = str_replace($value, $link, $tweet);					
				$value = '';
			}*/
			return $tweet;
		}
	}

?>