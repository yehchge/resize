<?php

set_time_limit(0);
error_reporting(1);
ini_set('display_errors',1);
ini_set('memory_limit', '32M');
/*
Author : Hemant Kr Tiwari
Email : t.hemantkumar@gmail.com
contact :  +91-9818664766
licence : GNU General Public License
*/


define('NO_THUMB_IMAGE_URL', 'https://texipaleo.files.wordpress.com/2014/01/cropped-cropped-cropped-cropped-blogheader4.jpg');
define('UNAUTHORIZED_CALL', 'unauthorized call or invalid parameter');
define('VAILD_HTTP_RESPONSE', 200);
define('OUTPUT_IMG_TYPE', 'image/jpg');
define('UNEXPECTED_ERROR', 'Unexpected Error');
define('DEFAULT_SIZE', '100x100-S');

Class resizer{

	private $source_image_url, $size_option, $command;

	/*
	* default cunstructor to this class
	*/

	function __construct($data) {

		$this->removeXSS($data);
		$this->source_image_url = (!empty($_GET['url']) ? $_GET['url'] : NO_THUMB_IMAGE_URL ); 
		$this->size_option = (!empty($_GET['size']) ? strtolower($_GET['size']) : DEFAULT_SIZE);
		$this->command = "";

	}	

	/*
	* function use remove xss form the input parameter .
	*/	

	private function removeXSS($xssStr) {
		$patterns[0] = '/alert(.*)/is';
		$patterns[1] = '/expression\([^<]+\)/is';
		$patterns[2] = '/expression[^<]+\)/i';
		$patterns[3] = '/background:expression\([^<]+\)/is';
		$patterns[4] = '/eval\([^<]+\)/is';
		$patterns[5] = '/style\=\"background:expression\([^<]+\)/is';
		$replaceWith[0] = '';
		$replaceWith[1] = '';
		$replaceWith[2] = '';
		$replaceWith[3] = '';
		$replaceWith[4] = '';
		$replaceWith[5] = '';
		foreach($patterns as $patt) {
			if(preg_match($patt, $xssStr, $matches)) {
				die(UNAUTHORIZED_CALL);
			} 
		}

	}


	/*
	* function use to check if image file exists on destination location or not .
	*/	


	private function checkDestinationFile(){

		// create curl resource 
		$ch = curl_init(); 

		// set url 
		curl_setopt($ch, CURLOPT_URL, $this->source_image_url); 

		//return string without body 
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);

		// $output contains the output string 
		curl_exec($ch); 

		$content_type = curl_getinfo($ch, CURLINFO_HTTP_CODE);


		// close curl resource to free up system resources 
		curl_close($ch); 

		if($content_type != VAILD_HTTP_RESPONSE)
			$this->source_image_url = NO_THUMB_IMAGE_URL;
	}

	/*
	* function use to doing resize and sent to medium.
	*/

	private function do_resizing() {
		header("Content-type:".OUTPUT_IMG_TYPE);
		$descriptorspec = array(
			0 => array("pipe", "r"), 
			1 => array("pipe", "w"), 
			2 => array("pipe", "w") 
		);
		$pipes= array();
		$process = proc_open($this->command, $descriptorspec, $pipes);
		if (!is_resource($process)) {
			die(UNEXPECTED_ERROR);
		}
		fclose($pipes[0]);
		stream_set_blocking($pipes[1],false);
		stream_set_blocking($pipes[2],false);
		while( true ) {
			$read= array();
			if( !feof($pipes[1]) ) $read[]= $pipes[1];
			if( !feof($pipes[2]) ) $read[]= $pipes[2];
			if (!$read) break;
			$ready= stream_select($read, $write=NULL, $ex= NULL, 2);
			if ($ready === false) {
				die(UNEXPECTED_ERROR);
			}
			foreach ($read as $r) {
				echo fread($r,4096);
			}
		}
		fclose($pipes[1]);
		fclose($pipes[2]);
		$code= proc_close($process);
		die;
	}


	/*
	* function use to prepare command to resize the image.
	*/

	private function prepare_command() {
		$this->source_image_url = str_replace(' ', "%20", $this->source_image_url);
		if(empty($this->size_option))
			die(UNAUTHORIZED_CALL);	

		$option=explode('-',$this->size_option);
		$s=explode('x',$option[0]);
		$w=abs($s[0]);
		$h=abs($s[1]);
		$size=$w.'x'.$h;

		if(empty($option[1])){
		 	$para=' -resize '.$size.'\! ';
			$this->command =' convert '.$para.' '.$this->source_image_url.' :- 2>&1';
		}

		switch (strtoupper($option[1])){
			case 'S':
				$para=' -resize '.$size.'\! ';
				$this->command =' convert '.$para.' '.$this->source_image_url.' :- 2>&1';
				break;
			case 'PC':
				$para=' -resize '.$size.'^ -gravity center -crop '.$size.'+0+0 +repage ';
				$this->command =' convert '.$para.' '.$this->source_image_url.' :- 2>&1';
				break;

			default:
				$para=' -resize '.$size.'\! ';
				$this->command =' convert '.$para.' '.$this->source_image_url.' :- 2>&1';
				break;
		}
	}

	/*
	Function is used to check file, prepare command to resize and resize
	*/

	public function resize(){
		$this->checkDestinationFile();
		$this->prepare_command();
		$this->do_resizing();

	}
}

$objResizer = new resizer($_GET);
$objResizer->resize();

