<?php
class ConnectionManager{
	public static function connect(){
		$link = mysql_connect('localhost', DB_USER, DB_PASS);
		if (!$link) {
		    die('Could not connect: ' . mysql_error());
		}else if(!mysql_select_db('sourabhc_sourabh')){
			die('Could not connect to database : ' . mysql_error());
		}
		return $link;
	}
	
	public static function disconnect($link){
		mysql_close($link);
	}
	
	public static function query($query){
		$link = ConnectionManager::connect();
		$resource = mysql_query($query, $link);
		ConnectionManager::disconnect($link);
		
		return $resource;
	}
}

?>