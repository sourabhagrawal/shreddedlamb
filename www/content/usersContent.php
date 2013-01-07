<?php
class UsersContent {
	
	public function __construct() {
  		$this->formHtml = '<div class="loginbox">
			<form method="post" action="/index.php/users/authenticate" class="loginform">
				<input type="hidden" name="continue" value='.$_REQUEST["continue"].' />
				<div class="formrow">
					<div class="formlabel">Email</div><input name="email"/>
				</div>
				<div class="formrow">
					<div class="formlabel">Password</div><input type="password" name="password"/>
				</div>
				<div class="formrow">
					<button type="submit">Login</button>
					<a href="/index.php/users/forgot">Forgot Password?</a>
				</div>
				<div class="formrow">
					
				</div>
			</form>
		</div> <iframe src="http://sourabh-co-in.rpxnow.com/openid/embed?token_url=http%3A%2F%2Fwww.sourabh.co.in%2Frpx.php?c='.$_REQUEST["continue"].'" 
			scrolling="no" frameBorder="no" allowtransparency="true" style="width:400px;height:200px"></iframe>';
	}
  	
  	public function login(){
  		return $this->formHtml;
  	}
  	
  	public function authenticate(){
  		$email = $_POST["email"];
  		$pass = $_POST["password"];
  		
  		Helper::authenticate($email, $pass, null, $_REQUEST["continue"], false);
  		exit;
  	}
  	
  	public function logout(){
  		session_destroy();
  		setcookie("sourabh_co_in_email","xyz",time() - 1024);
  		setcookie("sourabh_co_in_name","xyz",time() - 1024);
  		
  		$continue = $_REQUEST["continue"];
  		header( 'Location: http://'.$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$continue);
  		exit;
  	}
}
?>