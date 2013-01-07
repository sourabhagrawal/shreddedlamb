<?php

class ContactContent {
	public function __construct() {
  	}
  	
  	public function read(){
  		return file_get_contents(STATIC_DIR . "contactform.html");
  	}
}
?>