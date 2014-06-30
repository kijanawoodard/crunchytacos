<?php
	class_exists('NotificationResource') || require('NotificationResource.php');
	class_exists('AppResource') || require('AppResource.php');
	class PhotoResource extends AppResource{
		public function __construct(){
			parent::__construct();
		}
		public function __destruct(){
			parent::__destruct();
		}
		public $html;
		public $images;
		public $url;
		public function get_photos(){		
			$this->url = 'http://www.tastespotting.com/';
			$this->html = NotificationResource::doRequest($this->url, null, null, 'get', null);
			$dom = new DOMDocument();
			$dom->loadHTML($this->html);
			$xml = simplexml_import_dom($dom);
			$this->images = $xml->xpath('//img/@src');

			$this->title = "Photo Wall";
			$this->output = $this->renderView('photo/index', null);
			return $this->renderView('layouts/clean', null);
		}
		public function getLittleSrc($src){
			return $this->url . $src;
		}
		public function getBigSrc($src){
			//return 'http://fapdb.com/big/' . str_replace('.gif', '.jpg', str_replace('/babes/thumbs/', '', $src));
			return $this->url . $src;
		}
	}

?>