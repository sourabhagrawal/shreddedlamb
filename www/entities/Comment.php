<?php
class Comment{
	public $id;
	public $createdOn;
	public $content;
	public $author;
	public $articleId;
	
	public static function getByArticleId($articleId){
		$query = "select c.id, c.created_on, c.content, u.name, c.article_id from comments c left join users u on c.created_by = u.email where article_id = $articleId and hidden = 0 ORDER BY created_on DESC";
		$result = ConnectionManager::query($query);
		if(!$result == false){
			$comments = array();
			while($arr = mysql_fetch_array($result)){
				$comment = self::arrToComment($arr);
				array_push($comments, $comment);
			}
			return $comments;
		}
		return false;
	}
	
	public static function getById($commentId){
		$query = "select c.id, c.created_on, c.content, u.name from comments join users u on c.created_by = u.email where id = $commentId and hidden = 0 ORDER BY created_on DESC";
		$result = ConnectionManager::query($query);
		if(!$result == false){
			if($arr = mysql_fetch_array($result)){
				return self::arrToComment($arr);
			}
		}
		return false;
	}
	
	public static function getCommentCountByArticleId($articleId){
		$comments = self::getByArticleId($articleId);
		if($comments != false){
			return sizeof($comments);
		}
		return 0;
	}
	
	public static function addComment($body, $articleId){
		$body = htmlentities(mysql_escape_string($body));
		$mysqldate = date( 'Y-m-d H:i:s', time());
  		$query = "insert into comments(created_on, content, article_id, created_by, hidden) 
  					values('$mysqldate', '$body', $articleId, '" . $_SESSION['email'] . "', '0')";
  		$result = ConnectionManager::query($query);
  		return $result;
	}
	
	public static function deleteComment($commentId){
		$body = htmlentities(mysql_escape_string($body));
		$mysqldate = date( 'Y-m-d H:i:s', time());
  		$query = "update comments set  hidden = 1 where id = $commentId";
  		$result = ConnectionManager::query($query);
  		return $result;
	}
	
	public function toHtml(){
		$by = "";
		if($this->author && !empty($this->author)){
			$by .= " - by " . $this->author;
		}else{
			$by .= " - by Anonymous User";
		}
		if($_SESSION['priviledges'] == 'A' || ($this->author != null && !empty($this->author) && $this->author == $_SESSION['name'])){
			$by .= " - <a onclick='hideComment($this->articleId, $this->id)'>hide</a>";
		}
		 
		return "<div class='comment'>
					<div class='commentInfo'>".
						date("l, F d, Y, G:i", $this->createdOn)
						. $by
					."</div>
					$this->content
				</div>";
	}
	
	public static function commentsToHtml($articleId, $comments){
		return '<div id="disqus_thread"></div>
				<script type="text/javascript">
				    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
				    var disqus_shortname = "sourabh-agrawals-blog"; // required: replace example with your forum shortname
				    var disqus_identifier = '.$articleId.' ;
				    var disqus_title = "";
				
				    /* * * DONT EDIT BELOW THIS LINE * * */
				    (function() {
				        var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
				        dsq.src = "http://" + disqus_shortname + ".disqus.com/embed.js";
				        (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
				    })();
				</script>';
		$commentsHtml = "<div class='comments'>
	  							<h4>Comments</h4>
	  							<form id='comment-form'>
									<textarea name='body' style='width:200px , margin:10px'></textarea>
									<button type='button' onclick='postComment(" . $articleId . ")'>Post a Comment</button>
									<button type='button' onclick='previewComment()'>Preview</button>
								</form>
	  							<div id='comment-preview'></div>";
		foreach($comments as $comment){
  			$commentsHtml .= $comment->toHtml();
  		}
  		return $commentsHtml."</div>";
	}
	
	private static function arrToComment($arr){
		$comment = new Comment();
		$comment->id = $arr['id'];
		$comment->createdOn = strtotime($arr['created_on']);
		$comment->content = html_entity_decode(stripslashes($arr['content']));
		$comment->author = stripslashes($arr['name']);
		$comment->articleId = stripslashes($arr['article_id']);
		return $comment;
	}
}