<?php
class Category{
	public static $CATEGORY_PERSONAL = "personal";
	
	public $id;
	public $name;
	public $text;
	
	public static function getAll(){
		$query = "select * from categories order by `order`";
		$result = ConnectionManager::query($query);
		if(!$result == false){
			$categories = array();
			while($arr = mysql_fetch_array($result)){
				$category = self::arrToCategory($arr);
				array_push($categories, $category);
			}
			return $categories;
		}
		return false;
	}
	
	public static function getById($id){
		$query = "select * from categories where id=$id";
		$result = ConnectionManager::query($query);
		if(!$result == false){
			if($arr = mysql_fetch_array($result)){
				return self::arrToCategory($arr);
			}
		}
		return false;
	}
	
	public static function getByName($name){
		$query = "select * from categories where name=$name";
		$result = ConnectionManager::query($query);
		if(!$result == false){
			if($arr = mysql_fetch_array($result)){
				return self::arrToCategory($arr);
			}
		}
		return false;
	}
	
	private static function arrToCategory($arr){
		$category = new Category();
		$category->id = $arr['id'];
		$category->name = $arr["name"];
		$category->text = $arr["text"];
		return $category;
	}
}
?>