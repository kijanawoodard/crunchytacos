<?php
	class_exists('Notification') || require('models/Notification.php');
	class_exists('AppResource') || require('AppResource.php');
	class_exists('Subscriber') || require('models/Subscriber.php');
	class_exists('UserResource') || require('UserResource.php');
	class_exists('String') || require('lib/String.php');
	class_exists('Post') || require('models/Post.php');
	class_exists('aes128') || require('lib/aes128lib/aes128.php');
	class NotificationResource extends AppResource{
		public function __construct($attributes = null){
			parent::__construct($attributes);
		}
	
		public function __destruct(){
			parent::__destruct();
		}
		public $notifications;
		public $posts;
		public function get_notifications(){
			$this->notifications = Notification::findRecent();
			$this->posts = array();
			foreach($this->notifications as $notification){
				$this->posts[] = json_decode($this->getFromFriend($notification));
			}
			$this->title = 'Notifications';
			$this->output = $this->renderView('notification/recent', null);
			return $this->renderView('layouts/default', null);
		}
		private function getFromFriend(Notification $notification){
			$type = $notification->name;
			$url = sprintf("http://%s/index.php?r=%s.json", $notification->appName, strtolower(String::pluralize($notification->name)));
			$response = $this->doRequest($url, null, 'get');
			return $response;
		}
		public function post_notification(Notification $notification){
			$notification->appName = urldecode($notification->appName);
			$notification->time = urldecode($notification->time);
			error_log($notification->name . ' ' . $notification->time);
			$errors = Notification::save($notification);
			if($errors != null && count($errors) > 0){
				error_log('notification failed to save');
				return 'not ok';
			}else{
				return 'ok';		
			}
		}
		
		public function didSavePost(Post $post, $info){
			error_log('didSavePost');
			$subscribers = Subscriber::findAll();
			$data = null;
			$config = new AppConfiguration();
			$info->source = $config->email;
			if($subscribers != null && count($subscribers) > 0){
				foreach($subscribers as $subscriber){
					if($info->is_published && $subscriber->is_approved){
						$data = sprintf("title=%s&body=%s&source=%s&is_published=%s&date=%s&public_key=%s", urlencode($info->title), urlencode($info->body), urlencode($info->source), $info->is_published, urlencode($info->date), urlencode($subscriber->public_key));
						self::sendNotification($subscriber, 'post', $data);
						$data = null;
					}
				}
			}
		}
		
		// When a subscriber is updated and been approved, we need to save this publiser on the subscriber's
		// site with the private and public keys.
		public function didSaveSubscriber(Subscriber $subscriber, $info){
			if($info->is_approved){
				/*
				public $id;		
				public $email;
				public $url;
				public $body;
				public $is_approved;
				public $public_key;
				public $time;
				
				*/
				$config = new AppConfiguration();
				
				$data = sprintf("email=%s&url=%s&is_approved=%d&public_key=%s", urlencode($config->email), urlencode('http://'.$config->site_path), $subscriber->is_approved, urlencode($subscriber->public_key));
				self::sendNotification($subscriber, 'publisher', $data);
			}
		}
		public static function sendNotification(Subscriber $subscriber, $type, $data){
			$config = new AppConfiguration();
			$segments = explode('/', $_SERVER['SCRIPT_NAME']);
			array_pop($segments);
			$path = implode('/', $segments);
			$appName = sprintf('%s%s', $_SERVER['SERVER_NAME'], $path);
			$url = sprintf("http://%s", $subscriber->url);
			$path = sprintf("index.php?r=%s", $type);
			/*$aes = new aes128();
			$key = $aes->makeKey($subscriber->public_key);
			error_log($subscriber->public_key);
			error_log(serialize($obj));
			$body = $aes->blockEncrypt(serialize($obj),$key);
			error_log($body);
			*/
			$response = self::doRequest($url, $path, $data, 'post', null);
		}

		public static function doRequest($url, $path, $data, $method = 'get', $optionalHeaders = null){
			// create curl resource 
			if($path != null){
				$url .= '/' . $path;
			}
	        $ch = curl_init(); 
			$curl_options = array(
				CURLOPT_AUTOREFERER=>true
				//, CURLOPT_HEADER=>true
				, CURLOPT_FOLLOWLOCATION=>false
				, CURLOPT_ENCODING=>''
				, CURLOPT_USERAGENT=>'6degrees agent for joey guerra'
				, CURLOPT_CONNECTTIMEOUT=>5
				, CURLOPT_TIMEOUT=>5
				, CURLOPT_MAXREDIRS=>2
				, CURLOPT_URL=>$url
				, CURLOPT_RETURNTRANSFER=>true
				, CURLOPT_VERBOSE=>false
			);
			
			if($optionalHeaders != null && is_array($optionalHeaders)){
				$curl_options = array_merge($curl_options, $optionalHeaders);
			}

			if($method == 'post'){
				$curl_options[CURLOPT_POST] = true;
				$curl_options[CURLOPT_POSTFIELDS] = $data;
				$curl_options[CURLOPT_HTTPGET] = false;
			}
			
	        // set url 
			curl_setopt_array($ch, $curl_options);

	        // $output contains the output string 
	        $output = curl_exec($ch); 

			if(curl_errno($ch) > 0){
				error_log(curl_error($ch));
			}
			$headers = curl_getinfo($ch);
			$header = array();
			// 301 and 302 are redirects. I'm seeing this happen when I don't use www. in the domain name for the url.
			// So i'm going to just get the location to redirect to and repost.
			if($headers['http_code'] == 301 || $headers['http_code'] == 302){
				$output = preg_replace('/\\r\\n/', '\r\n', $output);
				$lines = explode('\r\n\r\n', $output);
				$lines = explode('\r\n', $lines[0]);
				foreach($lines as $line){
					$pairs = explode(': ', $line);
					if(count($pairs) > 1){
						$header[$pairs[0]] = $pairs[1];
					}
				}
			}
			if(array_key_exists('Location', $header)){
				error_log('redirecting to ' . $header['Location']);
				self::doRequest($header['Location'], $path, $data, $method, $optionalHeaders);
			}
			//error_log(String::stripCarriageReturnsAndTabs($output));
	        // close curl resource to free up system resources 
	        curl_close($ch);
			return $output;
		}
		
		// Modified from http://netevil.org/blog/2006/nov/http-post-from-php-without-curl
		/*public static function doRequest($url, $data, $method = "get", $optionalHeaders = null){
			$method = strtoupper($method);
			$params = array('http' => array(
				'method' => $method
				, 'content' => $data
				, 'header' => 'Content-type: application/x-www-form-urlencoded'
			));
			if ($optionalHeaders !== null) {
				$params['http']['header'] .= $optionalHeaders;
			}
			
			$ctx = stream_context_create($params);
			$fp = @fopen($url, 'rb', false, $ctx);
			if (!$fp) {
				throw new Exception("Problem with $url");
			}
			error_log('sending notification ' . $url . $data);
			$response = @stream_get_contents($fp);
			if ($response === false) {
				throw new Exception("Problem reading data from $url");
			}
			error_log('response ' . $response);
			return $response;
		}*/
		
	}
?>