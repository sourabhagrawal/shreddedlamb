<?php

class AboutContent {
	public function __construct() {
  	}
  	
  	public function read(){
  		return file_get_contents(STATIC_DIR . "about.html");
  	}
}

?>