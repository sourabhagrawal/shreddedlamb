<?php
class ArticleManager{
	public static function getArticles($categories, $pageNo){
		$query = "select a.id, a.created_on, a.last_modified_on, a.title, a.content, u.name, a.category_id from 
			articles a join categories c on a.category_id = c.id
			join users u on a.created_by = u.email" . ($categories && !empty($categories) ? " where c.name in ($categories)" : "") . " and a.disabled = 0 ORDER BY created_on DESC" . 
		($pageNo ? " limit " . (($pageNo - 1) * ARTICLE_PAGE_SIZE) . ", " . ARTICLE_PAGE_SIZE : "");
		$result = ConnectionManager::query($query);
		if(!$result == false){
			$articles = array();
			while($arr = mysql_fetch_array($result)){
				$article = self::arrToArticle($arr);
				array_push($articles, $article);
			}
			return $articles;
		}
		return false;
	}
	
	public static function getArticlesForTags($tags, $pageNo){
		$query = "select a.id, a.created_on, a.last_modified_on, a.title, a.content, u.name, a.category_id from 
			articles a join articles_tags at on a.id = at.article_id 
			join tags t on at.tag_id = t.id 
			join users u on a.created_by = u.email" 
			. ($tags && !empty($tags) ? " where t.name in ($tags)" : "") . " and a.disabled = 0 ORDER BY created_on DESC"
			. ($pageNo ? " limit " . (($pageNo - 1) * ARTICLE_PAGE_SIZE) . ", " . ARTICLE_PAGE_SIZE : "");
		$result = ConnectionManager::query($query);
		if(!$result == false){
			$articles = array();
			while($arr = mysql_fetch_array($result)){
				$article = self::arrToArticle($arr);
				array_push($articles, $article);
			}
			return $articles;
		}
		return false;
	}
	
	public static function getArticleById($articleId){
		$query = "select a.id, a.created_on, a.last_modified_on, a.title, a.content, u.name, a.category_id from 
			articles a join users u on a.created_by = u.email where a.id = " . $articleId . " and a.disabled = 0";
		$result = ConnectionManager::query($query);
		if(!$result == false){
			if($arr = mysql_fetch_array($result)){
				$article = self::arrToArticle($arr);
				return $article;
			}
		}
		return false;
	}
	
	public static function getArticlesCount($categories){
		$query = "select count(*) from 
			articles a join categories c on a.category_id = c.id
			join users u on a.created_by = u.email where a.disabled = 0 " . 
			($categories && !empty($categories) ? " and c.name in ($categories)" : "") . 
			" ORDER BY created_on DESC";
		$result = ConnectionManager::query($query);
		if(!$result == false){
			if($arr = mysql_fetch_array($result)){
				return $arr[0];
			}
		}
		return false;
	}
	
	public static function getArticlesCountForTags($tags){
		$query = "select count(*) from 
			articles a join articles_tags at on a.id = at.article_id 
			join tags t on at.tag_id = t.id where a.disabled = 0 " 
			. ($tags && !empty($tags) ? " and t.name in ($tags)" : "");
		$result = ConnectionManager::query($query);
		if(!$result == false){
			if($arr = mysql_fetch_array($result)){
				return $arr[0];
			}
		}
		return false;
	}
	
	public static function addArticle($title, $body, $category){
		$query = "select max(id) from articles";
		$result = ConnectionManager::query($query);
		$maxId = 1;
		if($result != false){
			if($arr = mysql_fetch_array($result)){
				$maxId = ($arr[0] ? $arr[0] : 0) + 1;
			}
		}
		
		$title = htmlentities(mysql_escape_string($title));
		$body = htmlentities(mysql_escape_string($body));
		$mysqldate = date( 'Y-m-d H:i:s', time());
  		$query = "insert into articles(id, created_on, last_modified_on, title, content, category_id, created_by, last_modified_by) 
  					values($maxId, '$mysqldate', '$mysqldate', '$title', '$body', $category, '" . $_SESSION['email'] . "', '" . $_SESSION['email'] . "')";
  		$result = ConnectionManager::query($query);
  		if($result)
	  		return $maxId;
	  	return false;
	}
	
	public static function updateArticle($title, $body, $category, $id){
		$mysqldate = date( 'Y-m-d H:i:s', time());
		$title = htmlentities(mysql_escape_string($title));
		$body = htmlentities(mysql_escape_string($body));
  		$query = "UPDATE articles SET last_modified_on = '$mysqldate', last_modified_by = '" . $_SESSION['email'] . "', 
  					title = '$title', content = '$body', category_id = $category where id = $id";
  		$result = ConnectionManager::query($query);
  		if($result)
	  		return $id;
	  	return false;
	}
	
	public static function deleteTags($articleId){
		$query = "delete from articles_tags where article_id=$articleId";
		$result = ConnectionManager::query($query);
		return $result;
	}
	
	public static function addTag($articleId, $tagName){
		$query = "select * from tags where name='$tagName'";
		$result = ConnectionManager::query($query);
		if($result != false){
			$tagId = -1;
			if($arr = mysql_fetch_array($result)){
				$tagId = $arr['id'];
			}else{
				$tagId = Tag::addTag($tagName);
			}
			
			if($tagId && $tagId != false && $tagId != -1){
				$query = "select * from articles_tags where article_id=$articleId and tag_id=$tagId";
				$result = ConnectionManager::query($query);
				if($result != false){
					if($arr = mysql_fetch_array($result)){
						// tag is already associated
					}else{
						$query = "insert into articles_tags(article_id, tag_id) values($articleId, $tagId);";
						$result = ConnectionManager::query($query);
						return $result;
					}
				}
			}
		}
		return false;
	}
	
	public static function getTags($articleId){
		$tags = array();
		$query = "select * from articles_tags where article_id=$articleId";
		$result = ConnectionManager::query($query);
		if($result != false){
			while($arr = mysql_fetch_array($result)){
				$tagId = $arr["tag_id"];
				$tag = Tag::getById($tagId);
				if($tag != false){
					array_push($tags, $tag);
				}
			}
		}
		return $tags;
	}
	
	public static function getTagString($articleId){
		$tagStr = "";
		$tags = self::getTags($articleId);
		foreach($tags as $tag){
			$tagStr .= $tag->name . ",";
		}
		return $tagStr;
	}
	
	public static function getArchiveMenuData(){
		$articles = self::getArticles();
		$tree = array();
		foreach($articles as $article){
			$createdOn = $article->createdOn;
			$month = date('n', $createdOn);
			$year = date('Y', $createdOn);
			if($tree[$year] == null){
				$tree[$year] = array();
			}
			
			if($tree[$year][$month] == null){
				$tree[$year][$month] = array();
			}
			
			array_push($tree[$year][$month], $article);
		}
		return $tree;
	}
	
	private static function arrToArticle($arr){
		$article = new Article();
		$article->id = $arr['id'];
		$article->createdOn = strtotime($arr['created_on']);
		$article->lastModifiedOn = strtotime($arr['last_modified_on']);
		$article->title = html_entity_decode(stripslashes($arr['title']));
		$article->content = html_entity_decode(stripslashes($arr['content']));
		$article->author = stripslashes($arr['name']);
		$article->categoryId = $arr['category_id'];
		return $article;
	}
	
	public static function href($article){
		$articleId = $article->id;
		$title = $article->title;
		
		$href = preg_replace("/\s+/", "_", strtolower($title)); 
		$href = preg_replace("/[^A-Za-z0-9_]/", "", $href); 
		return "/index.php/article/view/$articleId/$href";
	}
	
	public static function toHtml($article, $isSingle){
		$articleId = $article->id;
		$title = $article->title;
		$content = $article->content;
		$author = $article->author;
		$createdOn = $article->createdOn;
		$categoryId = $article->categoryId;
		
		$category = Category::getById($categoryId);
		$tags = self::getTags($articleId);
		
		$by = ($author == $_SESSION['name'] || $_SESSION['priviledges'] == 'A') ? (" - <a href='/index.php/article/edit/$articleId'>edit</a>") : "";
		
		$html = "<div class='article" . ($isSingle == true ? "single" : "") . "'>
					<h2><a href='" . self::href($article) . "'>$title</a></h2>
					<div class='articleInfo'>"
					."<div class='article-meta'>"
					.date("l, F d, Y, G:i", $createdOn) 
					. " - by " . $author . $by . "<br />";
		if($category != false){
			$html .= "category : <a href='/index.php/article/read/1/$category->name'>$category->text</a>";
		}
		if($tags != false && sizeof($tags) > 0){
			if($category != false)
				$html .= " - ";
			$html .= "tags : ";
			$count = 0;
			foreach($tags as $tag){
				if($count > 0)
					$html .= ", ";
				$html .= "<a href='/index.php/article/tag/1/$tag->name'>$tag->name</a>";
				$count++;
			}
		}
		$html .= "</div>";
		$html .= "<a style = 'float:right; margin-right : 20px;' href= '" 
					. self::href($article) 
					. "#disqus_thread' data-disqus-identifier = '$articleId'></a>"
					."</div>
					<div class='article-content'>$content</div>
				</div>";
		return $html;
	}
	
	public static function toFeed($article){
  		$href = HOST_URL . self::href($article);
  		$xml = "<item>";
  		$xml .= "<title>" . $article->title . "</title>";
  		$xml .= "<link>" . $href . "</link>";
  		$xml .= "<guid>" . $href . "</guid>";
  		$xml .= "<pubDate>" . $article->createdOn . "</pubDate>";
  		$xml .= "<description>" . $article->content . "</description>";
  		$xml .= "</item>";
  		
  		return $xml;
  	}
}
?>