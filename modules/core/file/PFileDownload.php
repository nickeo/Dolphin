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
*    Description: download page
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
$nav = new CNavigation();

//---------------------------------------------------------------------------------------------
//
//	taking care of variables
//

$accountUser = $pc->SESSIONIsSetOrSetDefault('accountUser');
$idUser = $pc->SESSIONIsSetOrSetDefault('idUser');
$file = $pc->GETIsSetOrSetDefault('file');
$navSet = $pc->GETIsSetOrSetDefault('p');
$spGetFileDetails = DBSP_PGetFileDetails;

global $gModule;

// -------------------------------------------------------------------------------------------
//
// Prepare and perform query
//

$db = new CDatabaseController();
$mysqli = $db->connectToDatabase();
$result = ARRAY();

$query = <<< EOD
	CALL {$spGetFileDetails}('{$idUser}', '{$file}', @pSuccess );
	SELECT @pSuccess AS success;
EOD;

$result = $db->performMultiQueryAndStore($query);

$linkfile = "?m={$gModule}&amp;p=details&amp;uniquename=";

$row = $result[0]->fetch_object();

$name = $row->name;
$uniqueName = $row->uniqueName;
$mimetype = $row->mimetype;
$size = $row->size;
$path = $row->path;

$download = "?m={$gModule}&amp;p=downloadp&amp;file={$uniqueName}";

	$dbfiles = <<< EOD
	<h3>Uppgifter om filen</h3>
	<div style='width:500px;'>
	<table>
		<tr>
			<th class='showDetails'>Namn</th>
			<td class='showDetails'>{$name}</td>
		</tr>
		<tr>
			<th class='showDetails'>Unikt namn</th>
			<td class='showDetails'>{$row->uniqueName}</td>
		</tr>
		<tr>
			<th class='showDetails'>Typ</th>
			<td class='showDetails'>{$mimetype}</td>
		</tr>
		<tr>
			<th class='showDetails'>Storlek</th>
			<td class='showDetails'>{$size}</td>
		</tr>
		
		</table>
		</div>
EOD;

$row = $result[2]->fetch_object();
$success = $row->success;

if($success) {
	
}

$result[2]->close();
$result[0]->close();
$mysqli->close();
//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$subHeader = "<h1>Filarkiv</h1>";
$navigation = $nav->userControlNavigation($navSet);

$centerBody = <<< EOD
    	{$dbfiles}
    	<a href='{$download}' alt='download' class='buttonLink' >Ladda ner</a>
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
$title = "Skapa nytt konto";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>