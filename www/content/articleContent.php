<?php
class ArticleContent {
	
	public function __construct() {
  	}
  	
  	public function create(){
  		return $this->getForm();
  	}
  	
  	public function edit($params){
  		if($params & sizeof($params > 0)){
  			return $this->getForm($params[0]);
  		}else{
  			return $this->getForm();
  		}
  	}
  	
  	public function publish($params){
  		$title = $_POST['title'];
  		$body = $_POST['body'];
  		$category = $_POST["category"];
  		$tags = explode(",", $_POST["tags"]);
  		
  		$result = false;
  		if($params & sizeof($params > 0)){
  			$result = ArticleManager::updateArticle($title, $body, $category, $params[0]);
  		}else{
  			$result = ArticleManager::addArticle($title, $body, $category);
  		}
		if($result == false){
			return $this->getForm();
		}else{
			ArticleManager::deleteTags($result);
			foreach($tags as $tag){
				$tag = trim($tag);
				if(!empty($tag)){
					ArticleManager::addTag($result, $tag);
				}
			}
			header( 'Location: http://'.$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']."/index.php/article/view/$result");
		}
  	}
  	
  	public function preview(){
  		$data = $_REQUEST['data'];
  		$dataObj = json_decode(stripslashes(urldecode($data)), true);
  		$article = new Article();
  		$article->title = $dataObj['title'];
  		$article->content = $dataObj['body'];
  		$article->lastModifiedOn = time();
  		$article->author = $_SESSION['name'];
  		echo ArticleManager::toHtml($article);
  		exit;
  	}
  	
  	public function read($params){
  		$articles = false;
  		$categoryStr = "";
  		$page = 1;
  		if($params & sizeof($params > 0)){
  			$page = $params[0] ? $params[0] : 1;
  			$categoryStr = $params[1] ? "'" .$params[1]. "'" : "";
  		}
  		$articles = ArticleManager::getArticles($categoryStr, $page);
  		$count = ArticleManager::getArticlesCount($categoryStr);
  		$html = self::articlesToHtml($articles, $count, 'read', $categoryStr, $page);
  		return $html;
  	}
  	
	public function feed(){
  		$articles = array();
  		
  		for($i = 1; $i < 6; $i++){
  			$res = ArticleManager::getArticles(null, $i);
  			if($res !== false){
  				$articles = array_merge($articles, $res);
  			}
  		}
		
  		$xml = '<?xml version="1.0" encoding="utf-8"?>
				<rss version="2.0">
					<channel>
					    <title>Shredded Lamb</title>
						<link>http://shreddedlamb.com/</link>
						<description>This is a blog of developer &amp; cynic Sourabh Agrawal. 
										A melting pot of ramblings, ideas &amp; knowledge.</description>';
  		foreach($articles as $article){
  			$xml .= ArticleManager::toFeed($article);
  		}
  		
  		$xml .= "</channel></rss>";
  		return $xml;
  	}
  	
	public function tag($params){
  		$articles = false;
  		$tagStr = "";
  		$page = 1;
  		if($params & sizeof($params > 0)){
  			$page = $params[0] ? $params[0] : 1;
  			$tagStr = $params[1] ? "'" .$params[1]. "'" : "";
  		}
  		$articles = ArticleManager::getArticlesForTags($tagStr, $page);
  		$count = ArticleManager::getArticlesCountForTags($tagStr);
  		$html = self::articlesToHtml($articles, $count, 'tag', $tagStr, $page);
  		return $html;
  	}
  	
  	public static function articlesToHtml($articles, $count, $op, $filter, $page){
  		$html = "";
  		if($articles != false && $count != false){
  			foreach($articles as $article){
	  			$html .= ArticleManager::toHtml($article);
	  		}
	  		
	  		$html .= "<div style='display:block; overflow : hidden; margin-bottom : 70px;'>";
	  		$newDisabled = true;
	  		$oldDisabled = true;
	  		if($page > 1){
	  			$newDisabled = false;
	  		}
	  		if($count > $page * ARTICLE_PAGE_SIZE){
	  			$oldDisabled = false;
	  		}
	  		$html .= "<span class='page-arrow " . ($newDisabled == true ? "page-arrow-disabled" : "") . "' style='float : right;'>
	  				<a " . ($newDisabled == false ? "href='/index.php/article/$op/" . ($page - 1) . "/$filter'" : "") . " title='newer posts'>&#9654;</a></span>";
	  		$html .= "<span class='page-arrow " . ($oldDisabled == true ? "page-arrow-disabled" : "") . "' style='float : left;'>
	  				<a " . ($oldDisabled == false ? "href='/index.php/article/$op/" . ($page + 1) . "/$filter'" : "") . " title='older posts'>&#9664;</a></span>";
	  		$html .= "</div>";
	  		$html .= '<script type="text/javascript">
			    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
			    var disqus_shortname = "sourabh-agrawals-blog"; // required: replace example with your forum shortname
			
			    /* * * DONT EDIT BELOW THIS LINE * * */
			    (function () {
			        var s = document.createElement("script"); s.async = true;
			        s.type = "text/javascript";
			        s.src = "http://" + disqus_shortname + ".disqus.com/count.js";
			        (document.getElementsByTagName("HEAD")[0] || document.getElementsByTagName("BODY")[0]).appendChild(s);
			    }());
			</script>';
  		}
  		return $html;
  	}
  	
	public function view($params){
  		if($params & sizeof($params > 0)){
	  		$article = ArticleManager::getArticleById($params[0]);
	  		if($article != false){
	  			$articleHtml =  ArticleManager::toHtml($article, true);
	  			$comments = Comment::getByArticleId($article->id);
	  			$commentsHtml = "<div id='comments-container'>";
	  			$commentsHtml .= Comment::commentsToHtml($article->id, $comments);
	  			$commentsHtml .= "</div>" ;
	  			return $articleHtml.$commentsHtml;
	  		}
  		}
  	}
  	
  	private function getForm($articleId){
  		$article = ArticleManager::getArticleById($articleId);
  		$categoryId = $article ? $article->categoryId : null;
  		$tags = ArticleManager::getTagString($articleId);
  		
  		$categories = Category::getAll();
  		$allTags = Tag::getAllNames();
  		
  		$formHtml = "
  		<script type='text/javascript' src='/js/tinymce/jscripts/tiny_mce/tiny_mce.js'></script>
		<script type='text/javascript'>
			tinyMCE.init({
				// General options
				mode : 'textareas',
				theme : 'advanced',
				plugins : 'autolink,lists,pagebreak,style,layer,table,save,advhr,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,advlist,autosave',
		
				// Theme options
				theme_advanced_buttons1 : 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
				theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',
				theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
				theme_advanced_buttons4 : 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft',
				theme_advanced_toolbar_location : 'top',
				theme_advanced_toolbar_align : 'left',
				theme_advanced_statusbar_location : 'bottom',
				theme_advanced_resizing : true,
		
				// Style formats
				style_formats : [
					{title : 'Bold text', inline : 'b'},
					{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
					{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
					{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
				]
			});
		</script>
		<!-- /TinyMCE -->
  		
  		<form action='/index.php/article/publish/" . ($articleId ? $articleId : '') . "' method='POST' class='contactform' id = 'article-form'>
			<div class='formrow'>
				<div class='formlabel'>title : </div><input name='title' value='" . ($article ? $article->title : "") . "' />
			</div>
			<div class='formrow'>
				<div class='formlabel'>body : </div><textarea name='body'>" . ($article ? $article->content : "") . "</textarea>
			</div>
			<div class='formrow'>
				<div class='formlabel'>category : </div>
				<select name='category'>" ;
			  		if($categories != false){
						foreach($categories as $category){
							$id = $category->id;
							$text = $category->text;
							$isSelected = (!$categoryId && $id == 3) || ($categoryId == $id);
							$formHtml .= "<option value='$id' " . ($isSelected ? "selected" : "") . ">$text</option>";
						}
					}
					$formHtml .= "</select>
			</div>
			<div class='formrow'>
				<div class='formlabel'>labels : </div>
				<div id='tagsbox' style='display:block;overflow:hidden;'></div>
			</div>
			<script type='text/javascript'>
				var inputId = new AutoComplete('tagsbox', 'tags', " . json_encode($allTags) . ", '$tags');
			</script>
			<div class='formrow'>
				<button type = 'submit'>Publish</button>
				<button type = 'button' onclick='previewArticle()'>Preview</button>
			</div>
		</form>
		<div id='article-preview'></div>";
  		return $formHtml;
  	}
  	
	public function previewComment(){
  		$data = $_POST['data'];//$this->getDataFromRequest($_REQUEST);
  		$dataObj = json_decode(stripslashes($data), true);
  		$comment = new Comment();
  		$comment->content = $dataObj['body'];
  		$comment->createdOn = time();
  		$comment->author = $_SESSION['name'];
  		echo $comment->toHtml();
  		exit;
  	}
  	
	public function postComment(){
  		$data = $_POST['data'];//$this->getDataFromRequest($_REQUEST);
  		$dataObj = json_decode(stripslashes($data), true);
  		$postResult = Comment::addComment($dataObj['body'], $dataObj['articleId']);
  		if($postResult != false){
  			$comments = Comment::getByArticleId($dataObj['articleId']);
  			echo Comment::commentsToHtml($dataObj['articleId'], $comments);
  		}
  		exit;
  	}
  	
	public function hideComment(){
  		$data = $_POST['data'];//$this->getDataFromRequest($_REQUEST);
  		$dataObj = json_decode(stripslashes($data), true);
  		$postResult = Comment::deleteComment($dataObj['commentId']);
  		if($postResult != false){
  			$comments = Comment::getByArticleId($dataObj['articleId']);
  			echo Comment::commentsToHtml($dataObj['articleId'], $comments);
  		}
  		exit;
  	}
}
?>