<?php

class Helper{
	public static function checkActiveMenu($page, $reqPage){
		return ($page == $reqPage) ? "class='activemenu'" : "class='menua'";
	}
	
	public static function dateAtTimezone($format, $locale, $time){
	    //Switch to new time zone locale
	    $tz = date_default_timezone_get();
	    date_default_timezone_set($locale);
	    
	    //Get the date in the new locale
	    $output = date($format, strtotime($time));
	    
	    //Restore the previous time zone
	    date_default_timezone_set($tz);
	    
	    return $output;
	    
	}
	
	public static function limitString($str, $limit){
		if(strlen($str) > $limit){
			return substr($str, 0, $limit) . "...";
		}
		return $str;
	}
	
	public static function authenticate($email, $pass, $name, $continue, $social){
		$priviledges = 'R';
		$query = "select * from users where email = '$email' " . ($social != true ? " and password = '$pass'" : "") . " and active = 1";
		$result = ConnectionManager::query($query);
		if(!$result == false){
			$articles = array();
			if($arr = mysql_fetch_array($result)){
				$name = $arr["name"];
				$priviledges = $arr["priviledges"];
			}else{
				if($social == true){
					$query = "INSERT INTO users(email, password, active, priviledges, name) VALUES('$email', '$pass', '1', 'R', '$name')";
					$result = ConnectionManager::query($query);
					echo "<br />".$result;
					if($result == false){
						return "<p style='color:#faa;'>could not connect to database.</p>".$this->formHtml;
					}
				}else
					return "<p style='color:#faa;'>wrong email and password combination.</p>".$this->formHtml;
			}
		}else{
			return "<p style='color:#faa;'>could not connect to database.</p>".$this->formHtml;
		}
		
		$_SESSION["email"] = $email;
		$_SESSION['isAuthenticated'] = 1;
		$_SESSION['priviledges'] = $priviledges;
		$_SESSION['name'] = $name;
		setcookie("sourabh_co_in_name", $name);
		setcookie("sourabh_co_in_email", $email);
		
		$href = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$continue;
  		header( "Location: $href");
	}
}

?>
