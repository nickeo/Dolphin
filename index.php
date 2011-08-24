<?php
/**********************************************************************************************
//
//	Dolphin , software to build webbapplications.
//	Copyright (C) 2011 Niklas Odén (niklasoden@hotmail.com)
//
// 	This file is part of Dolphin.
//
// 	Dolphin is free software: you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation, either version 3 of the License, or
// 	(at your option) any later version.
//
// 	Dolphin is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// 	GNU General Public License for more details.
//
// 	You should have received a copy of the GNU General Public License
// 	along with Dolphin. If not, see <http://www.gnu.org/licenses/>.
//
//
//	Description: Frontcontroller for Dolphin
//
//  Author: Niklas Odén, (some parts are written by Mikael Roos, BTH, and modified by Niklas Odén)
//
//
/***********************************************************************************************/

session_start();
error_reporting(-1);

require_once('config-global.php');


function __autoload($class_name) {
    require_once(TP_SOURCEPATH . $class_name . '.php');
}

$frontDoor = true;

// -------------------------------------------------------------------------------------------
//
// Redirect to the choosen modulecontroller (if a module is defined). 
// 
//

global $gModulesAvailable; // Set in config-global.php

//
// Get the requested page- and module id.
//
$gModule     = isset($_GET['m']) ? $_GET['m'] : 'core';
$gPage = isset($_GET['p']) ? $_GET['p'] : 'home' ;

//
// Check if the choosen module is available, if not show 404
//
if(!array_key_exists($gModule, $gModulesAvailable)) {
    require_once('config.php');
    require_once(TP_PAGESPATH . 'home/P404.php');
    exit;
}

//
// Load the module config-page, if it exists. Else load default config.php
//
$configFile = $gModulesAvailable["{$gModule}"] . '/config.php';

if(is_readable($configFile)) {
    require_once($configFile);
} else {
    require_once('config.php');
}

//
// Redirect to module controller.
//
$moduleController = $gModulesAvailable["{$gModule}"] . '/index.php';

if(is_readable($moduleController)) {
    require_once($moduleController);
} else {
    require_once(TP_PAGESPATH . 'home/P404.php');
}


?>