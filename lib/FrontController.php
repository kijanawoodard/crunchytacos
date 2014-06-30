<?php
class_exists('String') || require('lib/String.php');
if(file_exists('AppConfiguration.php')){
	class_exists('AppConfiguration') || require('AppConfiguration.php');	
}
class FrontController{
	public function __construct(){
		if(class_exists('AppConfiguration')){
			$this->config = new AppConfiguration();
		}else{
			$this->config = null;
		}
		$this->initSitePath();
	}
	public function __destruct(){}
	private $config;
	public static $site_path;

	private $did_send_headers;
	
	private function addJsonpHeaders(){
		$this->did_send_headers = true;
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 04 Oct 2004 10:00:00 GMT');
		header('Content-type: application/javascript;charset=UTF-8');
		//header('Content-type: multipart/x-mixed-replace;boundary=eof');
	}
	
	private function addJavascriptHeaders(){
		$this->did_send_headers = true;
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 04 Oct 2004 10:00:00 GMT');
		header('Content-type: text/javascript;charset=UTF-8');
	}
	
	private function addJsonHeaders(){
		$this->did_send_headers = true;
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 04 Oct 2004 10:00:00 GMT');
		header('Content-type: application/json;charset=UTF-8');
	}
	
	public static function themePath(){
		$config = null;
		if(class_exists('AppConfiguration')){
			$config = new AppConfiguration();
		}
		if($config != null){
			return 'themes/' . $config->theme;
		}else{
			return 'themes/default';
		}
	}
	public static function isSecure(){
		return array_key_exists('HTTPS', $_SERVER);
	}
	public static function urlFor($resource = null, $params = null, $make_secure = false){
		$config = (class_exists('AppConfiguration') ? new AppConfiguration(null) : new Object());
        $use_clean_urls = file_exists('.htaccess') || $resource == null;
        $query_string = null;

        if($resource == 'themes'){
			$resource = self::themePath();
            $use_clean_urls = true;
		}
        
        if($resource != null){
            $resource .= '/';
        }        
		
        if($params != null){
			$resource .= ($use_clean_urls ? '?' : '&');
			$query_string = array();
			foreach($params as $key=>$val){
				$query_string[] = sprintf('%s=%s', $key, $val);
			}
		}
        
		$url = '';
        if(!$use_clean_urls){
            $resource = 'index.php?r=' . $resource;
        }
        if($query_string != null){
            $resource .= implode('&', $query_string);
        }
		if($make_secure && $config != null && $config->ssl_path != null){
			$url = sprintf('https://%s/%s', $config->ssl_path, $resource);
		}else{
			$site_path = self::$site_path;
			if($make_secure){
				$site_path = str_replace('http:', 'https:', $site_path);
			}else{
				$site_path = str_replace('https:', 'http:', $site_path);
			}
			
			$url = $site_path . $resource;
		}
		return $url;
	}
	private function initSitePath(){
        $is_secure = self::isSecure();
		if($this->config != null && $this->config->site_path != null){
			self::$site_path = sprintf('%s://%s/', ($is_secure ? 'https' : 'http'), $this->config->site_path);
		}else{
			$segments = explode('/', $_SERVER['SCRIPT_NAME']);
			$virtual_path = null;
			array_shift($segments);
			array_pop($segments);
			if(count($segments) > 0){
				$virtual_path = implode('/', $segments);
			}
			self::$site_path = sprintf('%s://%s%s/', ($is_secure ? 'https' : 'http'), $_SERVER['SERVER_NAME'], ($virtual_path != null ? '/'.$virtual_path : null));
		}
	}
	
	public function execute(){		
        $file_type = 'html';
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		
		// During development, I was getting weird behavior on a login page posting to a secure server from
		// an insecure request. So I added this message because the request method turned out to be options. 
		// I have no idea why it was options.
		if($method == 'options'){
			error_log('method is options. You might want to check that you are loading the page via ssl');
		}
		// This is for browsers that don't support other methods like delete, put, trace, options.
		$_method = (array_key_exists('_method', $_REQUEST) ? strtolower($_REQUEST['_method']) : null);
		if($_method != null){
			$method = $_method;
		}
		$resource_path = 'resources/';
		$path = explode('/', $_SERVER['QUERY_STRING']);
		$r = (array_key_exists('r', $_GET) ? $_GET['r'] : null);
		if($r == null){
			$r = 'index';
		}
		$parts = explode('/', $r);

		// $parts contains empty items. I want to remove those items.
		$parts = array_filter($parts, array($this, 'isEmpty'));

		// This logic just sets the resource from the url, assuming that the resource name is the first item
		// in the array.
		if(count($parts) > 0){
			$r = array_shift($parts);
		}

		// Get the file type so we can present the data in different formats like html, xml, json, javascript and 
		// whatever else. Maybe even .atom, .rss, etc...
		if(stripos($r, '.') !== false){
			$extension = explode('.', $r);
			$r = $extension[0];
			$file_type = $extension[1];
		}
		
		$resource_name = String::camelize($r);
		$class_name = sprintf('%sResource', $resource_name);
		$file = $resource_path . $class_name . '.php';
		// Pass all versions of the controller name to the controller. See if it's pluralized first.
		if(!file_exists($file)){
			$singular_version = sprintf('%sResource', String::singularize($resource_name));
			$file = $resource_path . $singular_version . '.php';
			if(file_exists($file)){
				$class_name = $singular_version;
			}
		}

		$method = sprintf('%s_%s', $method, $r);
		if(file_exists($file)){
			class_exists($class_name) || require($file);
			session_start();			
			$obj = new $class_name();
			ob_start();
			if($file_type == 'jsonp' && !$this->did_send_headers){
				$this->addJsonpHeaders();
			}

			if($file_type == 'json' && !$this->did_send_headers){
				$this->addJsonHeaders();
			}

			if($file_type == 'js' && !$this->did_send_headers){
				$this->addJavascriptHeaders();
			}
			
			try{
				$output = Resource::sendMessage($obj, $method, $parts);
			}catch(Exception $e){				
				if($e->getCode() == 401){
					Resource::sendMessage($obj, 'unauthorizedRequestHasOccurred');
			    }
				throw $e;
			}
			
			ob_end_flush();
			Resource::sendMessage($obj, 'didFinishLoading');			
			return $output;
		}else{
			throw new Exception('404: Not found - '. $_SERVER['QUERY_STRING'], 404);
		}
	}
	public function isEmpty($value){
		return ($value != null || strlen(trim($value)) > 0);
	}
	
	public static function setNeedsToRedirectToPrevious($callback = null){
		$referer = (array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : null);
		$appendValue = null;
		if($callback != null){
			$appendValue = (is_array($callback) ? $callback[0]->$callback[1]($referer) : $callback($referer));				
		}
		self::setNeedsToRedirectRaw($referer . $appendValue, false);
	}
	public static function setNeedsToRedirectRaw($url){
		header(sprintf('Location: %s', $url));
	}

	public static function redirectTo($url, $params = null, $securely = false){
		self::setNeedsToRedirectRaw(self::urlFor($url, $params, $securely));
	}
	
	public static function requestedUrl(){
		if(array_key_exists('requested_url', $_SESSION)){
			return $_SESSION['requested_url'];
		}else{
			return null;
		}
	}
	public static function setRequestedUrl($value){
		$_SESSION['requested_url'] = $value;
	}
	public function errorDidHappen($code, $message, $file, $line){
		$html = sprintf('<h1>%s error message = %s</h1>', $code, $message);		
		$html .= '<ul>';
		foreach(debug_backtrace() as $key=>$value){
			$html .= sprintf('<li>%d: %s', $key, $value['class']);
			$html .= sprintf('::%s in %s at line # %d', $value['function'], $value['file'], $value['line']);
			$html .= '</li>';
		}
		if($code > 2){
			echo $html . '</ul>';			
		}
		
		// Make sure this line is commented out in prod because if an error occurs in the database
		// code, it'll display your user name and password.
		//debug_print_backtrace();
	}
	public function exceptionDidHappen($e){
		echo $e;
	}
}
?>