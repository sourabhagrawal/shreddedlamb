<?php 
	require_once 'include.php';
	
	$response = null;
	$file = CONTENT_DIR.'articleContent.php';
	if (is_file($file)) {
		require_once $file;
		$class = 'ArticleContent';
		$obj = new $class();
		$response = $obj->feed();
	}else{
		$response = "Feed Unavailable";
	}
	
	echo $response;
?>