<?php

if(isset($_GET['code'])) {
	$server_timezone = date_default_timezone_get();
	
	$wp_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
	include($wp_root.'/wp-config.php');
	
	define('DOING_AJAX', true);
	define('WP_USE_THEMES', false);
	
	$wp_load = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-load.php';
	require_once($wp_load);
	
	//$individole->debug($_GET, 1);
	
	$client_id 			= '2ccf63ba2180459eb4f3e545cdd15f36';
	$client_secret 		= '3d2b53a08e114958a25b0ccb15d9963e';
	$redirect_uri		= 'https://www.individole.com/wp-content/themes/individole/live/individoleInstagramToken.php';
	
	$fields = array(
		'client_id'     => $client_id,
		'client_secret' => $client_secret,
		'grant_type'    => 'authorization_code',
		'redirect_uri'  => $redirect_uri,
		'code'          => $_GET['code'],
	);
	$url = 'https://api.instagram.com/oauth/access_token';
	
	//$individole->debug($fields, 1);
	
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_TIMEOUT,20);
	curl_setopt($ch,CURLOPT_POST,true); 
	curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);
	$result = curl_exec($ch);
	curl_close($ch); 
			
	$connect_data = json_decode($result);
	
	//$individole->debug($connect_data, 1);
	
	$content = '
	<script type="text/javascript">
	function getParameterByName(name) {
	    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
	        results = regex.exec(location.search);
	    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}
	
	var code = getParameterByName("code");
	if( code !== "" ) {
		window.location.replace("https://smashballoon.com/instagram-feed/token/?code="+code);
	
	} else {
		var access_token = window.location.hash;
		var return_uri = getParameterByName("return_uri");
		var state = getParameterByName("state");
	
		if( state.length > 1 ){
	
			//Prevent unauthorized redirect URLs
			if( state.indexOf("page-sb-instagram-feed") == -1 && state.indexOf("://smashballoon.com") == -1 ){
				window.location.hash = "";
				alert("Unauthorized redirect URL");
			} else {
				//Doesnt allow "=" to be used in the state param so used a hyphen instead and replaced it
				state = state.replace("page-sb-instagram-feed", "page=sb-instagram-feed");
				window.location.replace(state + access_token);
			}
	
		} else {
	
			//Prevent unauthorized redirect URLs
			if( return_uri.indexOf("page=sb-instagram-feed") == -1 && return_uri.indexOf("://smashballoon.com") == -1 ){
				window.location.hash = "";
				alert("Unauthorized redirect URL");
			} else {
				window.location.replace(return_uri + access_token);
			}
	
		}
	
	}
	</script>
	';

	//echo $content;
}