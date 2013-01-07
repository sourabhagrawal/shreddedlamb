<?php
require_once 'include.php';

//For a production script it would be better to include the apiKey in from a file outside the web root to enhance security.
$rpx_api_key = 'cadf5a7450154bd577d2c06806a5c5e53d85029f';

/* STEP 1: Extract token POST parameter */
$token = $_POST['token'];

if(strlen($token) == 40) {//test the length of the token; it should be 40 characters

  /* STEP 2: Use the token to make the auth_info API call */
  $post_data = array('token'  => $token,
                     'apiKey' => $rpx_api_key,
                     'format' => 'json',
                     'extended' => 'true'); //Extended is not available to Basic.

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/auth_info');
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_FAILONERROR, true);
  $result = curl_exec($curl);
  if ($result == false){
    echo "\n".'Curl error: ' . curl_error($curl);
    echo "\n".'HTTP code: ' . curl_errno($curl);
  }
  curl_close($curl);


  /* STEP 3: Parse the JSON auth_info response */
  $auth_info = json_decode($result, true);

  if ($auth_info['stat'] == 'ok') {
    /* STEP 4: Use the identifier as the unique key to sign the user into your system.
       This will depend on your website implementation, and you should add your own
       code here. The user profile is in $auth_info.
    */
    $identifier = $auth_info['profile']['identifier'];
    $email = $auth_info['profile']['email'];
    $preferredUsername = $auth_info['profile']['preferredUsername'];
    
  	Helper::authenticate($email, $identifier, $preferredUsername, $_REQUEST["c"], true);
  	exit;
  } else {
      // Gracefully handle auth_info error.  Hook this into your native error handling system.
      echo "\n".'An error occured: ' . $auth_info['err']['msg']."\n";
      var_dump($auth_info);
      echo "\n";
      var_dump($result);
  }
}else{
  // Gracefully handle the missing or malformed token.  Hook this into your native error handling system.
  echo 'Authentication canceled.';
}
?>