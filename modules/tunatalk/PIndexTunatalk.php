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
*    Description: homepage for module TunaTalk
*
*    Author: Niklas Odén
*
***********************************************************************************************/

//---------------------------------------------------------------------------------------------
//
// pagecontroller
//

$pc = new CPageController();


//---------------------------------------------------------------------------------------------
//	interceptionfilter
//
//

if(!$frontDoor) 
	die('No direct access to pagecontroller allowed!');

$iFilter = new CInterceptionFilter();
$iFilter->FrontControllerIsVisited();
//$iFilter->UserLoginStatus();

//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$subHeader = <<< EOD
    
    <h1 class='tuna'>TunaTalk</h1>
    <div class='tunaindex'>
    	
    </div>
   
EOD;

$leftBody = "";

$centerBody = <<< EOD
	
	<div style='padding: 40px 0 0 0; width:480px; float:left;'>
		<p>Tunatalk är ett forum inom ramen för Dolphin. TunaTalk innehåller en enkel
		texteditor, Mark it up, som bygger på javascript. Det finns funktionalitet för
		att skapa ett ämne, kommentera det samt möjlighet att editera och ta bort poster.<br /><br />
		Det finns än så länge ingen möjlighet att kommentera en kommentar i ny tråd (trådade kommentarer).<br /> </p>
	</div>
	
	<div class='tunaImage'></div>
	<div class='clear'></div>
	
EOD;

$rightBody = "";

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