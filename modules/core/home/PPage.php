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
*    Description: Clean page - to be used as template
*
*    Author: Niklas Odén
*
***********************************************************************************************/

//---------------------------------------------------------------------------------------------
//	
//	pagecontroller
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
//$iFilter->UserGroupStatus();

//---------------------------------------------------------------------------------------------
//
//    creating objects
//


//$db = new CDatabaseController();
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


// --------------------  content to be put in the <head> section
$htmlHead = "";

// ------------------- javascript content
$js = "";

//-------------------- Area under the header 100% width (if headline/picture wider than the columns is needed)
$subHeader = "";

// ------------------- Left column - float left
$leftBody = "";

//-------------------- Center column
$centerBody = "<h1>Rubrik</h1><p>Brödtexten kommer här</p>";

//-------------------- Right column - float right
$rightBody = "<div class='rightNav'>{$navigation}</div>";


//---------------------------------------------------------------------------------------------
//
//	printing out the page
//

//---------------------- title of the page
$title = "";

//---------------------- if stylesheet other than default is needed
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody, $htmlHead, $js);

?>