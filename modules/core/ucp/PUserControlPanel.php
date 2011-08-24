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
*    Description: User Control Panel
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
//$iFilter->UserGroupStatus();

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
$navSet = $pc->GetIsSetOrSetDefault('p');


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
$navigation = $nav->userControlNavigation($navSet);
$subHeader = "<h1>Användaruppgifter</h1>";

// preparing gravatar

if(USER_GRAVATAR) {
	$gravatarInfo = <<< EOD
			<h3 class='listLeft' >Gravatar</h3>
       		<img src='{$gravatarLink}' alt='avatar' height='80'class='listRight' />
       		<div class='clear'></div>
       		<hr class='greyLine' />
EOD;
} else {
	$gravatarInfo = "";
}

$centerBody = <<< EOD
    	 
    	 
        <div class='formatSettings'>
        	<h3 class='listLeft'>Namn</h3>
        	<p class='listRight'>{$name}</p>
        	<div class='clear'></div>
        	<hr class='greyLine' />
       		<h3 class='listLeft' >E-mail</h3>
       		<p class='listRight'>{$email}</p>
       		<div class='clear'></div>
       		<hr class='greyLine' />
       		<h3 class='listLeft' >Gruppstatus</h3>
       		<p class='listRight'>{$groupId}</p>
       		<div class='clear'></div>
       		<hr class='greyLine' />
       		<h3 class='listLeft' >Avatar</h3>
       		<img src='{$avatar}' alt='avatar' height='80'class='listRight' />
       		<div class='clear'></div>
       		<hr class='greyLine' />
       		{$gravatarInfo}
        </div>
      
       
        <div class='clear'></div>
    
    <br />
    <br />
EOD;

$leftBody = <<< EOD
EOD;

$rightBody = <<< EOD
	{$navigation}
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