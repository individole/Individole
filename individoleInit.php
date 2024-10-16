<?php

if(session_status() === PHP_SESSION_NONE) {
	session_cache_limiter('private, must-revalidate');
	session_cache_expire(0);
	ini_set('session.gc_maxlifetime', 7200);
	session_start();
}

if(!function_exists('wp_get_current_user')) {
	include(ABSPATH . "wp-includes/pluggable.php");
}
	
$individole_version = 'live';
$user = wp_get_current_user();

$user_id = 0;
if(isset($user->data->ID)) {
	$user_id = $user->data->ID;
	
} else if(isset($user->ID)) {
	$user_id = $user->ID;
}

$theme_dir = WP_CONTENT_DIR.'/themes/';

if($user_id > 0) {
	$c = get_option('individole_options_user');
	
	if(isset($c[$user_id]['dev_individole_theme']) && $c[$user_id]['dev_individole_theme'] == 1) {
		$folder_individole_dev = ABSPATH.'wp-content/individole/dev';
		
		if(file_exists($folder_individole_dev) && is_dir($folder_individole_dev)) {
			$individole_version = 'dev';
		}
	}
}

(!defined("WP_AUTO_UPDATE_CORE")) 								? define('WP_AUTO_UPDATE_CORE', false)								: "";
(!defined("DISABLE_WP_CRON")) 									? define('DISABLE_WP_CRON', true)									: "";
(!defined("EWWW_IMAGE_OPTIMIZER_DISABLE_AUTOCONVERT")) 	? define('EWWW_IMAGE_OPTIMIZER_DISABLE_AUTOCONVERT', true) 	: "";
(!defined("EWWW_IMAGE_OPTIMIZER_DELAY")) 						? define('EWWW_IMAGE_OPTIMIZER_DELAY', 5) 						: "";
(!defined("WP_POST_REVISIONS")) 									? define('WP_POST_REVISIONS', false) 								: "";
(!defined("AUTOSAVE_INTERVALL")) 								? define('AUTOSAVE_INTERVALL', 600) 								: "";
(!defined("TAGSTOCHECKLIST")) 									? define('TAGSTOCHECKLIST', true)									: "";
(!defined("CREATE_SITEMAP")) 										? define('CREATE_SITEMAP', 100)										: "";
(!defined("CREATE_SITEMAP_CRON")) 								? define('CREATE_SITEMAP_CRON', true)								: "";
(!defined("ICL_DONT_LOAD_NAVIGATION_CSS")) 					? define('ICL_DONT_LOAD_NAVIGATION_CSS', true)					: "";
(!defined("ICL_DONT_LOAD_LANGUAGES_JS")) 						? define('ICL_DONT_LOAD_LANGUAGES_JS', true)						: "";
(!defined("ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS")) 			? define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true)			: "";
(!defined("WPML_DO_NOT_LOAD_EMBEDDED_TM")) 					? define('WPML_DO_NOT_LOAD_EMBEDDED_TM', true)			: "";
(!defined("TAB")) 													? define('TAB', chr(9))													: "";
(!defined("DISALLOW_FILE_EDIT")) 								? define('DISALLOW_FILE_EDIT', true)								: "";

if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == "/index.php") {
	(!defined("DONOTCACHEPAGE")) ? define('DONOTCACHEPAGE', true) : "";
}

if(isset($_GET['action']) && $_GET['action'] == "lostpassword") {
	if(isset($_SERVER['REQUEST_SCHEME']) && isset($_SERVER['HTTP_REFERER']) && isset($_SERVER['SERVER_NAME'])) {
		$url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];
		
		if(!substr_compare($_SERVER['HTTP_REFERER'], $url, 0, strlen($url)) === 0) {
			exit;
		}
		
	} else {
		exit;
	}
}

$filepath_dbug = ABSPATH.'wp-content/individole/'.$individole_version.'/individoleDbug.php';
$filepath_individole = ABSPATH.'wp-content/individole/'.$individole_version.'/individole.php';

include_once($filepath_dbug);
include_once($filepath_individole);

global $wp_query;
$wp_query_base = $wp_query;

$individole_admin_show_debug 	= 0;
$individole_configs 				= array();
$config_cpt 						= array();

$individole = new individole();

$individole->error_log_old = ABSPATH.'wp-content/debug.log';
$individole->error_log = dirname(ABSPATH).'/debug.log';
ini_set('error_log', $individole->error_log);

if(isset($_SESSION['superadmin']) && is_user_logged_in()) {
	add_filter('admin_body_class', function( $classes ) {
		return $classes.' is_superadmin';
	});
	
	if(defined("MYSQL_SSL_CERT")) {
		$mysql = mysqli_init();
		mysqli_ssl_set($mysql, NULL,NULL, MYSQL_SSL_CERT, NULL, NULL);
		mysqli_real_connect($mysql, DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, 3306, NULL, MYSQLI_CLIENT_SSL);
		
	} else {
		$mysql = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	}
	
	$q = '
	SELECT
		1
	FROM
		'.TABLE_PREFIX.'users AS t1
	LEFT JOIN
		'.TABLE_PREFIX.'usermeta AS t2
	ON
		(t1.ID = t2.user_id AND t2.meta_key = "'.TABLE_PREFIX.'capabilities")
	WHERE
		t1.ID = '.$_SESSION['superadmin']['id'].'
		AND MD5(t1.user_login) = "'.$_SESSION['superadmin']['user'].'"
		AND t2.meta_value LIKE "%\"administrator\"%"
	LIMIT
		1
	';
	$result = mysqli_query($mysql, $q);
	mysqli_close($mysql);
	
	if($result->num_rows > 0 && WP_DEBUG === true) {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		ini_set('log_errors', 1 );
	}
}