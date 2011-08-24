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
*    Description: displaying available modules
*
*    Author: Niklas Odén
*
***********************************************************************************************/

//---------------------------------------------------------------------------------------------
//
//   Interception filter
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
    <h1>Moduler</h1>
    
EOD;

$leftBody = "";

$centerBody = <<< EOD
	<div class='articleFrameHead'>
		<p>I dagsläget finns endast tre moduler. Kärnmodulen som innehåller applikationens grundläggande funktioner som
		exempelvis installation av databas och inloggning. Därtill finns två valbara moduler. Redovisningsmodulen som
		i princip bara visar redovisningstexter och applikationer från tidigare kurser samt en forummodul: TunaTalk.</p>
	</div>
	
EOD;

$rightBody = <<< EOD
	<h3>Valbara moduler</h3>
	<hr class='soft'/><br />
	<a href='?m=tuna&amp;p=tunatalk' class='comment'>TunaTalk</a><br />
	<a href='?m=examin&amp;p=redovis' class='comment'>Redovisning</a>
EOD;

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Home";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>