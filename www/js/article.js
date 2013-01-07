var previewArticle = function(){
	var title = document.getElementById('article-form').title.value;
	var body = tinyMCE.get('body').getContent();document.getElementById('article-form').body.value;
	var json = {data : 
					{
						title : title,
						body : body
					}
				};
	AJAX('/index.php/article/preview/', json,
		function(status, rText){
			if(status == 200){
				document.getElementById('article-preview').innerHTML = rText;
			}
		}
	);
}

var previewComment = function(){
	var body = document.getElementById('comment-form').body.value;
	
	params = 'body=' + encodeURIComponent(body);
	
	var json = {data : 
					{
						body : body
					}
				};

	AJAX('/index.php/article/previewComment/', json,
		function(status, rText){
			if(status == 200){
				document.getElementById('comment-preview').innerHTML = rText;
			}
		}
	);
}

var postComment = function(articleId){
	var body = document.getElementById('comment-form').body.value;
	
	var json = {data : 
					{
						body : body,
						articleId : articleId
					}
				};

	AJAX('/index.php/article/postComment/', json,
		function(status, rText){
			if(status == 200){
				document.getElementById('comments-container').innerHTML = rText;
			}
		}
	);
}

var hideComment = function(articleId, commentId){
	if(!confirm("Are you sure you want to delete this comment?"))
		return;
	var json = {data : 
					{
						articleId : articleId,
						commentId : commentId
					}
				};

	AJAX('/index.php/article/hideComment/', json,
		function(status, rText){
			if(status == 200){
				document.getElementById('comments-container').innerHTML = rText;
			}
		}
	);
}