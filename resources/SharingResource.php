
<?php
class_exists('AppResource') || require('AppResource.php');
class_exists('NotificationResource') || require('NotificationResource.php');
class SharingResource extends AppResource{
	public function __construct(){
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}

	public function post_sharing($email, $message, $url){
		if(strlen($email) > 0){
			$url = urldecode($url);
			$our_message = <<<eos
Someone wanted to share this photo with you:
<a href="$url">Photo</a>
$message
eos;
			$client_address = $_SERVER['REMOTE_ADDR'];
			//TODO: Need rate limiting logic to stop scripts from hamering email.
			// perhaps something that only allows email requests from the page???
			$this->send(array($email), $our_message, 'Photo from crunchytacos.com');
			return $this->renderView('sharing/success', null);
		}else{
			return 'no';
		}
	}
	
	private function send($emails, $message=null, $subject){
		$to = implode($emails,",");
		$headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=iso-8859-1\r\nFrom: webmaster@crunchytacos.com\r\nReply-To: webmaster@crunchytacos.com\r\nX-Mailer: PHP/" . phpversion();

		if(!empty($emails[0])){
			if(mail($to, $subject, $message, $headers)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
}

?>