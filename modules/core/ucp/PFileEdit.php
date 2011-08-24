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
*    Description: form for editing file details stored in database
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
	$status = "Raderad";
	$trashOrRecover = "recover";
	$trashOrRecoverTitle = "Återställ";
	$editable = "disabled";
	$editButton = "disabled";
} ELSE {
	$status = "Normal";
	$trashOrRecover = "trash";
	$trashOrRecoverTitle = "Soptunna";
	$editable = "";
	$editButton = "";
}

$download = "?m={$gModule}&amp;p=download&amp;file={$uniqueName}";
$delete = "?m={$gModule}&amp;p=delete&amp;file={$uniqueName}";
$action = "?m={$gModule}&amp;p=editfilep&amp;file={$uniqueName}";

	$dbfiles = <<< EOD
	
	<h3>Uppgifter om filen</h3>
	<fieldset class='editfile'>
	<form id='form1' action='{$action}' method='post'>
	<div class='settingsWrapperUser'>
		<div class='settingsLeftUser'>
			<p class='settings'>Namn:</p>
		</div>
		<div class='settingsMiddleUser'>
			<input type='text' name='name' value='{$name}' autofocus  size='25' {$editable} class='settingsForm' />
		</div>
		<div class='settingsLeftUser'>
			<p class='settings'>Mime-type:</p>
		</div>
		<div class='settingsMiddleUser'>
			<input type='text' name='mimetype' value='{$mimetype}' {$editable} class='settingsForm'/>
		</div>
		<div class='clear'></div>
	</div>
	<div class='settingsWrapperUser'>
		<div class='settingsLeftUser'>
			<p class='settings'>Unikt namn:</p>
		</div>
		<div class='settingsMiddleUser'>
			<input type='text' name='uniqueName' value='{$uniqueName}' size='25' readonly class='settingsForm'/>
		</div>
		<div class='settingsLeftUser'>
			<p class='settings'>Storlek:</p>
		</div>
		<div class='settingsMiddleUser'>
			<input type='text' name='size' value='{$size}' disabled class='settingsForm'/>
		</div>
		<div class='clear'></div>
	</div>
	<div class='settingsWrapperUser'>
		<div class='settingsLeftUser'>
			<p class='settings'>Ägare:</p>
		</div>
		<div class='settingsMiddleUser'>
			<input type='text' name='owner' value='{$owner}' disabled size='25' class='settingsForm'/>
		</div>
		<div class='settingsLeftUser'>
			<p class='settings'>Status:</p>
		</div>
		<div class='settingsMiddleUser'>
			<input type='text' name='status' value='{$status}' disabled class='settingsForm'/>
		</div>
		<div class='clear'></div>
	</div>
	<div class='settingsWrapperUser'>
		<div class='settingsLeftUser'>
			<p class='settings'>Path:</p>
		</div>
		<div class='settingsMiddle'>
			<textarea cols='60' rows='2' disabled>{$path}</textarea>
		</div>
		<div class='settingsMiddle'>
			<button type='submit' name='submit' value='edit' {$editButton} >Ändra</button>
			<button type='submit' name='submit' value='{$trashOrRecover}'>{$trashOrRecoverTitle}</button>
			<button type='submit' name='submit' value='delete'>Ta bort helt</button>
		</div>
		<div class='clear'></div>
	
		
		
	</div>
	</form>
	</fieldset>
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
	<a href='{$download}' alt='download' />Nedladdningssida</a><br />
	
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