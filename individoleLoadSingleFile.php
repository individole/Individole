<?php

// session_start();

if(isset($_SESSION['serviceworker'])) {
	unset($_SESSION['serviceworker']);
	//dfdfdfd
}

if(isset($_GET['file'])) {
	$server_timezone = date_default_timezone_get();
	
	$file = $_GET['file'];
	if(isset($_GET['offline'])) {
		$file = $file.'?offline=1';
		
	}
		
	$content = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
	<html>
	<title></title>
	<body style="margin:0px; padding: 0px;">
		<script type="text/javascript">
			if("serviceWorker" in navigator) {
				window.addEventListener("load", () => {
					navigator.serviceWorker.register("/service-worker.js", {
						scope: "/"
					}).then(function(registration) {
						
					}).catch(function(error) {
						
					});
				});
				
			} else {
				clog("Service worker NOT supported");
			}
		</script>
		<img src="'.$file.'" style="width: 100%;" />
	</body></html>';
	
	echo $content;
}