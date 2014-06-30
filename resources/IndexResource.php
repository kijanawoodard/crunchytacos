<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('NotificationResource') || require('NotificationResource.php');
class IndexResource extends AppResource{
	public function __construct(){
		parent::__construct();
		$this->known_site_big_src = array('fapdb.com'=>'fapdb');
	}
	public function __destruct(){
		parent::__destruct();
	}
	public $html;
	public $images;
	public $url;
	public $known_site_big_src;
	
	public function get_index($url = null){
		if(strlen($url) > 0){
			if(strpos($url, 'http://') === false){
				$url = "http://$url";
			}
			$this->url = $url;
		}else{
			$this->url = 'http://www.tastespotting.com';
		}
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
		$src_url = $src;
		if(strpos($src, 'http://') === false){
			$src_url = $this->url . $src;
		}
		return $src_url;
	}
	public function getBigSrc($src){
		$compare_url = str_replace('http://', '', $this->url);
		$big_src = $src;
		if(array_key_exists($compare_url, $this->known_site_big_src)){
			$big_src = $this->{$this->known_site_big_src[$compare_url]}($src);
		}
		if(strpos($big_src, 'http://') === false){
			$big_src = $this->url . $src;
		}
		return $big_src;
	}
	
	private function fapdb($src){
		return 'http://fapdb.com/big/' . str_replace('.gif', '.jpg', str_replace('/babes/thumbs/', '', $src));
	}
}

?>