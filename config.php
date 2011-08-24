<?php
/*********************************************************************************************
*
*	Description: defining sitespecifik variables and constants
*
*	Author: Niklas Odén
*
***********************************************************************************************/

//----------------------------------------------------------------------------------------------
//
//	database info
//

define('DB_HOST', 'hostname');
define('DB_USER', 'user');
define('DB_PASSWORD', 'password');
define('DB_DATABASE', 'database');
define('DB_PREFIX', 'dol_');

//----------------------------------------------------------------------------------------------
//
//	sitespecifik
//

define('WS_SITELINK',   'http://www.yourdomain.com'); // Link to site.
define('WS_TITLE' , 'Dolphin');
define('WS_STYLESHEET', 'stylesheets/style.css');
//define('WS_STYLESHEET', 'stylesheets/stylePlain.css');
define('WS_FOOTER' , 'DOLPHIN &copy; 2010-2011 by Niklas Odén');
define('WS_FAVICON' , 'images/favicon.ico');
define('WS_CHARSET' , 'UTF-8');
define('WS_SYSADMIN' , 'dolphin');
define('WS_LANGUAGE' , 'sv');
define('WS_COLUMN' , 'disappear');
define('WS_JAVASCRIPT' , WS_SITELINK . 'js/');
define('APP_DIRECTORY', "/dolphin/");
define('WS_IMAGES' , WS_SITELINK . 'images/');



//----------------------------------------------------------------------------------------------
//
//	meny
//

$wsMeny = ARRAY (
	'Hem' => '?m=core&amp;p=home',
	'sida 1' => '?m=core&amp;p=cleanpage',
	'sida 2' => '?m=core&amp;p=cleanpage',
	'sida 3' => '?m=core&amp;p=cleanpage',
	'Template' => '?m=core&amp;p=template',
//	'Källkod' => '?m=core&amp;p=view',		// uncomment to see sourcecode
	
	);
	
define('WS_MENY', serialize($wsMeny));

//----------------------------------------------------------------------------------------------
//
//	Hashing algoritm
//

define('DB_PASSWORDHASHING', 'MD5');
//define('DB_PASSWORDHASHING', 'SHA-1');
//define('DB_PASSWORDHASHING', 'PLAIN');



// -------------------------------------------------------------------------------------------
//
// Server keys for reCAPTCHA. Get your own keys for your server.
// http://recaptcha.net/whyrecaptcha.html
//


define('RECAPTCHA_PUBLIC', 'your_key_here');
define('RECAPTCHA_PRIVATE', 'your_key_here');

// customize recaptcha
define('RECAPTCHA_STYLE', "clean");


//----------------------------------------------------------------------------------------------
//
//	settings for filearchive and upload/download
//

define('MAX_FILE_SIZE', 200000);
define('FILE_ARCHIVE_PATH', 'path_to_your_archive_directory');



?>