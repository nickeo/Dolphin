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
*    Description: foogler blogg
*
*    Author: Niklas Odén
*
***********************************************************************************************/

//---------------------------------------------------------------------------------------------
//	interceptionfilter
//
//

if(!$frontDoor) 
	die('No direct access to pagecontroller allowed!');

$iFilter = new CInterceptionFilter();
$iFilter->FrontControllerIsVisited();
//$iFilter->UserLoginStatus();
//$iFilter->UserGroupStatus();

//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$subHeader = <<< EOD
    <div class='subHeaderShow'>
    <h1>SubHeader</h1>
    <p>Den här ytan kan exempelvis användas till någon lång rubrik eller en bred bild.</p>
    </div>
    
EOD;

$leftBody = <<< EOD
	<div class='columnShow'>
		<h3>Underrubrik</h3>
		<p>Vänsterkolumn</p>
	</div>
EOD;

$centerBody = <<< EOD
	<div class='centerShow'>
	<h3>Huvudtexten/bilden</h3>
	<p>Central text/bild</p>
	</div>
EOD;

$rightBody = <<< EOD
	<div class='columnShow'>
		<h3>Länkar/info</h3>
		<p>länk</p>
	</div>
EOD;

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Template";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>