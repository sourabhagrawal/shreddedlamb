<?php 
	require_once 'include.php';
	
	class UrlParser{ 	
	  	public static function parsePage(){
	  		$pageLink = str_replace($_SERVER["SCRIPT_NAME"], "", $_SERVER["PHP_SELF"]);
	  		$urlTokens = explode("/", $pageLink);
			return array(
				page => isset($urlTokens[1]) && !empty($urlTokens[1]) ? $urlTokens[1] : "article",
				action => isset($urlTokens[2]) && !empty($urlTokens[2]) ? $urlTokens[2] : "read",
				params => isset($urlTokens[3]) && !empty($urlTokens[3]) ? array_splice($urlTokens, 3) : array()
			);
	  	}
	}
	$pageParams = UrlParser::parsePage();
	$page = $pageParams['page'];
	
	$response = null;
	$file = CONTENT_DIR.$page.'Content.php';
	if (is_file($file)) {
		require_once $file;
		$class = ucfirst($page).'Content';
		$obj = new $class();
		$response = $obj->$pageParams['action']($pageParams['params']);
	}else{
		$response = "Don't type random Urls! Use navigation on the left.";
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Shredded Lamb</title>
		<link type="text/css" href="/css/default.css" rel="stylesheet" />
		<link rel="alternate" type="application/atom+xml" title="RSS Feeds" href="/rss.php">
		<script type="text/javascript" src="/js/autocomplete.js"></script>
		<script type="text/javascript" src="/js/ajax.js"></script>
		<script type="text/javascript" src="/js/article.js"></script>
		<script type="text/javascript" src="/js/archive.js"></script>
		<script type="text/javascript">
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-29557122-1']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
	</head>
	<body>
		<center>
			<div class="loggedin-details">
			<span style="float : left">A man is a free man if he is happy alone. Rest all are nothing but slaves.</span>
			<?php if(!$_SESSION['isAuthenticated']){?>
				<a href='/index.php/users/login?continue=<?php echo $_SERVER["PHP_SELF"]?>'>Login</a>
			<?php }else{?>
					logged in as : <?php echo $_SESSION["email"]?>
					<?php if($_SESSION['priviledges'] == "A" || $_SESSION['priviledges'] == "C"){?>
						- <a href="/index.php/article/create">New Post</a>
					<?php }?> -
					<a href="/index.php/users/logout?continue=<?php echo $_SERVER["PHP_SELF"]?>">Logout</a>
			<?php }?>
			</div>
		<div id = 'main'>
			<div class="header">
				<div class="header-inner">
					<div class="header-title">shredded lamb</div>
					<div class='header-tagline'>
						This is a blog of developer &amp; cynic <a href = '/index.php/about'>Sourabh Agrawal</a>. 
						A melting pot of ramblings, ideas &amp; knowledge.
					</div>
				</div>
			</div>
			<div id = 'sidebar'>
				<ul class='menu'>
					<li><a href='/' <?php echo Helper::checkActiveMenu(($page == "article" && $pageParams['action'] == 'read' && !$pageParams['params'][0]) ? "all" : "", "all")?>>All</a></li>
					<?php 
						$categories = Category::getAll();
						if($categories != false){
							foreach($categories as $category){
								$name = $category->name;
								$text = $category->text;
								echo "<li><a href='/index.php/article/read/1/" . $name . "' " . 
									Helper::checkActiveMenu($pageParams['params'][1] ? $pageParams['params'][1] : "", $name) .">" . 
									$text . "</a></li>";
							}
						}
					?>
					<li><a href='/index.php/photos' <?php echo Helper::checkActiveMenu($page, "photos")?>>Photos</a></li>
					<li><a href='/index.php/quotes' <?php echo Helper::checkActiveMenu($page, "quotes")?>>Quotes</a></li>
					<li><a href='/index.php/about' <?php echo Helper::checkActiveMenu($page, "about")?>>About Me</a></li>
					<li><a href='/index.php/contact' <?php echo Helper::checkActiveMenu($page, "contact")?>>Get In Touch</a></li>
				</ul>
				<div class = "profiles">
					<a href='http://www.facebook.com/iitr.sourabh' target='_blank'><img alt="facebook" src="/images/fb_logos_01.png"></a>
					<a href='http://www.last.fm/user/sourabhagrawal' target='_blank'><img alt="last.fm" src="/images/lastfm_logos_01.png" width = 37></a>
					<a href='http://www.linkedin.com/in/sourabhagrawal09' target='_blank'><img alt="linkedin" src="/images/in_logos_01.png"></a>
				</div>
				<div class="archives left-box">
					<h3>Archives</h3>
					<ul>
						<?php 
							$tree = ArticleManager::getArchiveMenuData();
							foreach($tree as $yearn => $year){
								foreach($year as $monthn => $month){
									$timestamp = mktime(0, 0, 0, $monthn, 1, $yearn);
    								$dateText = date("M - y", $timestamp);
    								$dateId = date("My", $timestamp);
									echo "<li>
										<span class='archives-month-heading' onclick=\"toggleArchiveList('$dateId-list', '$dateId-bullet');\">
											<span class='bullets' id='$dateId-bullet'>&#9654;</span><span class='bulleted archives-month-heading-text'>$dateText (" . sizeof($month) . ")</span>
										</span>
										<ul class='archives-month' id = '$dateId-list'>";
									foreach($month as $article){
										echo "<li><span class='bullets'>&#9654;</span><span class='bulleted'><a href='" . ArticleManager::href($article) . "' >" .Helper::limitString($article->title, 20)."</a></span></li>";
									}
									echo "</ul></li>";
								}
							}
						?>
					</ul>
				</div>
				<div class="left-box">
					<h3>Blogroll</h3>
					<ul>
						<li><span class="bullets">&#9654;</span> <span class="bulleted"><a href='http://www.devashish.co.in/' target='_blank'>TwentySeven...</a></span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted"><a href='http://grasskode.com/' target='_blank'>Humbug</a></span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted"><a href='http://imanva.blogspot.com/' target='_blank'>Execution</a></span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted"><a href='http://www.stuffed-apple.blogspot.com/' target='_blank'>the highwayman</a></span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted"><a href='http://geekheads.blogspot.com/' target='_blank'>GEEK HEADS</a></span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted"><a href='http://dev-s8n-2.blogspot.com/' target='_blank'>Share, one must</a></span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted"><a href='http://face-inside.blogspot.com/' target='_blank'>My Experiments with Truth</a></span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted"><a href='http://refreshyourscope.blogspot.com/' target='_blank'>REFRESH YOUR SCOPE</a></span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted"><a href='http://supriy.me/' target='_blank'>supriy.me</a></span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted"><a href='http://steve-yegge.blogspot.com/' target='_blank'>STEVEY'S BLOG RANTS</a></span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted"><a href='http://reloadedmemories.blogspot.in/' target='_blank'>The First Step..</a></span></li>
					</ul>
				</div>
				<div class="todo left-box">
					<h3>What am i working on?</h3>
					<ul>
						<li><span class="bullets">&#9654;</span> <span class="bulleted">a proper logo</span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted">integrating a WYSWYG editor</span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted">news feeds</span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted">gallery for photos</span></li>
						<li><span class="bullets">&#9654;</span> <span class="bulleted">drafts in posts</span></li>
					</ul>
				</div>
			</div>
			<div id = 'content'>
				<div class="articles">
					<?php 
						echo $response;
					?>
				</div>
				<div class="copyright">
					Copyright 2011, Sourabh Agrawal - 
					<a href="/index.php/about">About Me</a> - 
					<a href="/index.php/contact">Get in touch</a> - 
					Hosted by <a href="http://www.bluehost.com" target="_blank">bluehost</a>
				</div>
			</div>
		</div>
		</center>
	</body>
</html>
