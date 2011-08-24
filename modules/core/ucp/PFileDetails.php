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
*    Description: showing  file information
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
$uniqueName = $pc->GETIsSetOrSetDefault('uniquename');
$navSet = $pc->GETIsSetOrSetDefault('p');
$spGetFileDetails = DBSP_PGetFileDetails;
$fCheckUserIsAdmin = DBF_FCheckUserIsAdmin;
global $gModule;

// -------------------------------------------------------------------------------------------
//
// Prepare and perform query
//

$db = new CDatabaseController();
$mysqli = $db->connectToDatabase();
$result = ARRAY();

$query = <<< EOD
	CALL {$spGetFileDetails}('{$idUser}', '{$uniqueName}', @pSuccess );
	SELECT @pSuccess AS success;
	
EOD;

$result = $db->performMultiQueryAndStore($query);

$linkfile = "?m={$gModule}&amp;p=details&amp;uniquename=";

$row = $result[0]->fetch_object();

$fileId = $row->fileId;

$name = $row->name;
$uniqueName = $row->uniqueName;
$mimetype = $row->mimetype;
$size = $row->size;
$path = $row->path;
$owner = $row->owner;
$created = $row->created;
$modified = $row->modified;
$deleted = $row->deleted;

if($deleted != NULL) {
	$status="Raderad";
} else {
	$status = "Normal";
}
$download = "?m={$gModule}&amp;p=downloadp&amp;file={$uniqueName}";
$delete = "?m={$gModule}&amp;p=delete&amp;file={$uniqueName}";
$edit = "?m={$gModule}&amp;p=editfile&amp;uniquename={$uniqueName}";

	$dbfiles = <<< EOD
	<h3>Uppgifter om filen</h3>
	<div style='width:660px;'>
	<table>
		<tr>
			<th class='showDetails'>Namn</th>
			<td class='showDetails'>{$name}</td>
		</tr>
		<tr>
			<th class='showDetails'>Unikt namn</th>
			<td class='showDetails'>{$uniqueName}</td>
		</tr>
		<tr>
			<th class='showDetails'>Typ</th>
			<td class='showDetails'>{$mimetype}</td>
		</tr>
		<tr>
			<th class='showDetails'>Storlek</th>
			<td class='showDetails'>{$size}</td>
		</tr>
		<tr>
			<th class='showDetails'>Path</th>
			<td class='showDetails' style='width:500px; display:block;'>{$path}</td>
		</tr>
		<tr>
			<th class='showDetails'>Ägare</th>
			<td class='showDetails'>{$owner}</td>
		</tr>
		<tr>
			<th class='showDetails'>Skapad</th>
			<td class='showDetails'>{$created}</td>
		</tr>
		<tr>
			<th class='showDetails'>Ändrad</th>
			<td class='showDetails'>{$modified}</td>
		</tr>
		<tr>
			<th class='showDetails'>Status</th>
			<td class='showDetails'>{$status}</td>
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
    	
    	
EOD;
$leftBody = <<< EOD
EOD;

$rightBody = <<< EOD
	{$navigation}
	<hr class='soft'><br />
	<a href='{$download}' alt='download' class='buttonLink' >Ladda ner</a>
	<a href='{$edit}' alt='download' class='buttonLink' />Editera fil</a><br />
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