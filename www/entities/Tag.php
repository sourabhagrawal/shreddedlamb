<?php
class Tag{
	public $id;
	public $name;
	
	public static function getAll(){
		$query = "select * from tags";
		$result = ConnectionManager::query($query);
		if(!$result == false){
			$tags = array();
			while($arr = mysql_fetch_array($result)){
				$tag = self::arrToTag($arr);
				array_push($tags, $tag);
			}
			return $tags;
		}
		return false;
	}
	
	public static function getAllNames(){
		$tags = self::getAll();
		$names = array();
		if($tags != false){
			foreach($tags as $tag){
				array_push($names, $tag->name);
			}
		}
		return $names;
	}
	
	public static function addTag($name){
		$query = "select max(id) from tags";
		$result = ConnectionManager::query($query);
		$maxId = 1;
		if(!$result == false){
			if($arr = mysql_fetch_array($result)){
				$maxId = ($arr[0] ? $arr[0] : 0) + 1;
			}
		}
		$name = htmlentities(mysql_escape_string($name));
  		$query = "insert into tags(id, name) values($maxId, '$name')";
  		$result = ConnectionManager::query($query);
  		if($result)
	  		return $maxId;
	  	return false;
	}
	
	public static function getById($id){
		$query = "select * from tags where id=$id";
		$result = ConnectionManager::query($query);
		if(!$result == false){
			if($arr = mysql_fetch_array($result)){
				return self::arrToTag($arr);
			}
		}
		return false;
	}
	
	private static function arrToTag($arr){
		$tag = new Tag();
		$tag->id = $arr['id'];
		$tag->name = html_entity_decode(stripslashes($arr['name']));
		return $tag;
	}	
}
?>