<?php
class_exists('LoginResource') || require('LoginResource.php');
class_exists('AppResource') || require('AppResource.php');
class_exists('Post') || require('models/Post.php');
class_exists('NotificationResource') || require('NotificationResource.php');
class_exists('Publisher') || require('models/Publisher.php');
class PostResource extends AppResource{
	public function __construct(){
		parent::__construct();
		$this->max_filesize = str_replace('M', '', ini_get('upload_max_filesize')) * 1000000;
		$this->post = new Post();
	}
	public function __destruct(){
		parent::__destruct();
	}
	private $notificationResource;
	public $posts;
	public $post;
	public $max_filesize;
	
	public function get_post(Post $post){
		if($post != null && $post->id > 0){
			$this->post = Post::findById($post->id);
			$this->title = $this->post->title;
			$this->output = $this->renderView('post/new_post', null);
			return $this->renderView('layouts/default', null);
		}else{
			if(!LoginResource::isAuthorized()){
				throw new Exception('401: Unauthorized', 401);
			}
			$this->post = new Post();
			$this->title = "Write a post";
			$this->output = $this->renderView('post/new_post', null);
			return $this->renderView('layouts/default', null);
		}
	}
	public function get_posts(){
		$this->posts = Post::find(0, 10, 'date', 'desc');	
		$this->title = "All your posts";
		$this->output = $this->renderView('post/index', null);
		if($this->file_type == 'html'){
			return $this->renderView('layouts/default', null);
		}else{
			return $this->renderView(null, null);
		}
	}
	
	public function get_post_new($type = null){
		if(!LoginResource::isAuthorized()){
			throw new Exception('401: Unauthorized', 401);
		}
		
		if(empty($type)){
			$type = 'post';
		}
		
		$this->title = "Create a new post";
		$this->output = $this->renderView('post/new_' . $type, null);
		return $this->renderView('layouts/default', null);
	}
	public function put_post_photo($photo = null){
		if(!LoginResource::isAuthorized()){
			throw new Exception('401: Unauthorized', 401);
		}
		$photo['error_message'] = '';
		if(!in_array($photo['type'], array('image/jpg', 'image/jpeg', 'image/gif', 'image/png'))){
			$photo['error_message'] = "I don't accept that type of file.";
		}else{
			$file_type = str_replace('image/', '', $photo['type']);
			$file_type = String::replace('/jpeg/', 'jpg', $file_type);
			if(is_uploaded_file($photo['tmp_name'])){
				
				$photo_name = String::replace('/\.*/', '', uniqid(null, true));
				$folder = sprintf('media/%s', date('Y'));
				if(!file_exists($folder)){
					mkdir($folder, 0777, true);
				}
				$folder .= sprintf('/%s', date('n'));
				
				if(!file_exists($folder)){
					mkdir($folder, 0777, true);
				}
				
				$folder .= sprintf('/%s', date('j'));
				if(!file_exists($folder)){
					mkdir($folder, 0777, true);
				}
				$path = sprintf('%s/%s.%s', $folder, $photo_name, $file_type);//'media/'.basename($photo['name']);
				$did_move = move_uploaded_file($photo['tmp_name'], $path);
				if($did_move === false){
					$photo['error_message'] .= ' Failed to move the photo to ' . $path;
				}
			}
		}
		/*["name"]=>
	  ["type"]=>
	  string(10) "text/plain"
	  ["tmp_name"]=>
	  string(26) "/private/var/tmp/php7ObsWD"
	  ["error"]=>
	  int(0)
	  ["size"]=>*/
  
		return $this->renderView('post/photo_upload_success', array('photo'=>$photo, 'photo_name'=>$photo['name'], 'file_name'=>$photo_name));
	}

	public function post_post(Post $post, $public_key = null, $photo_names = array()){
		if(!empty($public_key)){
			$public_key = urldecode($public_key);
			$publisher = Publisher::findByPublicKey($public_key);
			if($publisher != null && $publisher->is_approved){
				$post->date = date('c');
				$post->source = $publisher->email;
				$post->is_published = false;
				$errors = $post->save($post);
				if(count($errors) > 0){
					foreach($errors as $key=>$error){
					}
				}
			}
			return 'ok';
		}elseif(!LoginResource::isAuthorized()){
			throw new Exception('401: Unauthorized', 401);
		}
		
		foreach($photo_names as $key=>$value){
			$message .= ', ' . $key . '=' .$value;
		}
		//error_log('photo_names = ' . $message);
		$this->notificationResource = new NotificationResource();
		Post::addObserver($this->notificationResource);
		
		$post->date = date('c');
		$errors = $post->save($post);
		FrontController::redirectTo('posts');
	}

}

?>