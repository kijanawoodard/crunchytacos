<?php
	class Log{
		public function __construct($path, $level = 5, $to_console=false){
			$this->path = $path;
			$this->level = $level;
			$this->to_console = $to_console;
		}
		public $path;
		public $level;
		public $to_console;
		
		public function writeLine($message){
			$this->write($message . "\n");
		}
		public function write($message){
			if($this->level > 0){
				if(!$this->to_console){
					$file_name = $this->getFileName();
					if(!file_exists($this->path))
						throw new Exception("The path: '$this->path' , doesn't exist.");
					if(is_writable($this->path)){
						$handle = fopen($this->path . '/'. $file_name, "ab");
						fwrite($handle, date("g:i:s A") . " - " . $message . '
');
						fclose($handle);
					}else{
						throw new Exception("Log file is not writable: '{$this->path}{$file_name}'. The current path is: {$_SERVER['SCRIPT_FILENAME']}.");
					}
				}else{
					printf ('%s - %s', date("g:i:s A"), $message);
				}
			}
		}
		private function getFileName(){
			return date("Ymd") . ".txt";
		}
	}
?>