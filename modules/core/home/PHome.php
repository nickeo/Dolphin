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
*    Description: dolphin - home
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
//	creating objects
//

$pc = new CPageController();
$nav = new CNavigation();

//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$subHeadline ="Undermeny";

$subNavigation = ARRAY (
	'Länk 1' => '?p=home',
	'Länk 2' => '?p=hom',
	'Länk 3' => '?p=home',
	'Länk 4' => '?p=home'
	);

$navigation = $nav->SubNavigation($subHeadline, $subNavigation);




$subHeader = <<< EOD
    
    
EOD;

$leftBody = "";

$centerBody = <<< EOD
	<div style='width:600px;'>
	<h1>Välkommen till Dolphin!</h1>
	<p>Dolphin är en webbtemplate som håller på att utvecklas. Den är en produkt av mina försök att lära
	mig främst php men även javascript/ajax. Dolphin är helt fritt att använda men det här är än så länge bara
	en testversion så inga garantier lämnas. För mer information om features och hjälp med installationen läs read_me.txt
    </p>
	</div>
	
EOD;

$rightBody = "<div class='rightNav'>{$navigation}</div>";

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