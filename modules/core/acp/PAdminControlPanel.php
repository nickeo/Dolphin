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
*    Description: Admin Control Panel
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
$iFilter->UserLoginStatus();
$iFilter->UserGroupStatus();

//---------------------------------------------------------------------------------------------
//
//	creating objects
//

$pc = new CPageController();
$db = new CDatabaseController();
$nav = new CNavigation();
$if = new CInterceptionFilter();

//---------------------------------------------------------------------------------------------
//
//    taking care of variables
//

$userId = $_SESSION['idUser'];
$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : "";
$spGetAccountInfo = DBSP_PGetAccountInfo;
$result = Array();
$navSet = $pc->GETIsSetOrSetDefault('p');

global $gModule;

//---------------------------------------------------------------------------------------------
//
//    preparing and performing query
//

$mysqli = $db->connectToDatabase();

$query = <<< EOD
	CALL {$spGetAccountInfo}({$userId});
EOD;
$result = $db->performMultiQueryAndStore($query);
$row = $result[0]->fetch_object();

	$name = $row->account;
	$email = $row->email;
	$avatar = $row->avatar;
	$gravatar = $row->gravatar;
	$gravatarLink = $row->gravatarLink;
	$groupId = $row->groupId;

$result[0]->close();
$mysqli->close();
//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$action = "?p=accountsp";
$subHeader = "<h1>Kontrollpanel Admin</h1>";

$centerBody = <<< EOD
    	 
    	 <div style='width:900px;'>
        <div class='formatAdminBlue'>
        	<h3>Filhantering</h3>
        	<br/><br/><br/>
        	<a href='?m={$gModule}&amp;p=listfiles' class='adminLink'>- Filarkiv. Se/editera/radera/återställ</a>
        	
        </div>
      	<div class='formatAdminBlue2'>
        	<h3>Användare</h3>
        	<br/><br/><br/>
        	<a href='?m={$gModule}&amp;p=listaccounts' class='adminLinkBlue'>- Hantera existerande konton</a><br />
        	<a href='?m={$gModule}&amp;p=createaccount' class='adminLinkBlue'>- Lägg till nya konton</a>
        	
        </div>
        <div class='formatAdminTurquoise'>
        	<h3>Databashantering</h3>
        	<br/><br/><br/>
        	<a href='?m={$gModule}&amp;p=install' class='adminLinkTurquoise'>- Installation/ominstallation</a>
        </div>
       
        <div class='clear'></div>
    	</div>
    <br />
    <br />
EOD;

$leftBody = <<< EOD
EOD;

$rightBody = <<< EOD

EOD;

	

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Redigera konto";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>