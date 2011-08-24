<?php
/**********************************************************************************************
*
*	Dolphin , software to build webbapplications.
*	Copyright (C) 2011 Niklas Odén (niklasoden@hotmail.com)
*
* 	This file is part of Dolphin.
*
* 	Dolphin is free software: you can redistribute it and/or modify
* 	it under the terms of the GNU General Public License as published by
* 	the Free Software Foundation, either version 3 of the License, or
* 	(at your option) any later version.
*
* 	Dolphin is distributed in the hope that it will be useful,
* 	but WITHOUT ANY WARRANTY; without even the implied warranty of
* 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* 	GNU General Public License for more details.
*
* 	You should have received a copy of the GNU General Public License
* 	along with Dolphin. If not, see <http://www.gnu.org/licenses/>.
*
*
*	Description: defining variables and constants for the TunaTalk forum
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
//
//

define('WS_SITELINK',   'http://www.yourdomain.com'); // Link to site.
define('WS_TITLE' , 'Dolphin');
//define('WS_STYLESHEET', 'stylesheets/style.css');
define('WS_STYLESHEET', 'stylesheets/stylePlain.css');
define('WS_FOOTER' , 'DOLPHIN &copy; 2010-2011 by Niklas Odén');
define('WS_FAVICON' , 'images/favicon.ico');
define('WS_CHARSET' , 'UTF-8');
define('WS_SYSADMIN' , 'dolphin');
define('WS_LANGUAGE' , 'en');
define('WS_COLUMN' , 'disappear');
define('WS_JAVASCRIPT' , WS_SITELINK . 'js/');
define('APP_DIRECTORY', "/dolphin/");




//----------------------------------------------------------------------------------------------
//
//	meny
//

$wsMeny = ARRAY (
	'Hem' => '?m=tuna&amp;p=tunatalk',
	'Senaste inläggen' => '?m=tuna&amp;p=topics',
	'Skriv nytt inlägg' => '?m=tuna&amp;p=newtopic',
//	'Källkod' => '?p=view',
	'Dolphin' => '?p=home'
	);
	
define('WS_MENY', serialize($wsMeny));




?>