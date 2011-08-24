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
*	Description:	Creating form for attaching file to article. This page is accessed through
*					jQuery and Ajax
*
*
**********************************************************************************************/

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

//--------------------------------------------------------------------------------------------
//
//	taking care of variables
//

$idUser = $pc->SessionIsSetOrSetDefault('idUser');
$idArticle = $pc->GetIsSetOrSetDefault('idart');
$updateOrDelete = $pc->GetIsSetOrSetDefault('delete');
$spListFiles = DBSP_PListFiles;
$spAttachableFiles = DBSP_PAttachableFiles;
$spAttachedFiles = DBSP_PAttachedFiles;
global $gModule;
$action = "?m=core&amp;p=attachfilep";
$result = Array();

//--------------------------------------------------------------------------------------------
//
//	preparing and performing query
//

if($updateOrDelete == true) {
	$attachOrDelete = "Ta bort";
	$query = <<< EOD
	CALL {$spAttachedFiles}('{$idUser}', '{$idArticle}');
EOD;
} else {
	$attachOrDelete = "Bifoga";
	$query = <<< EOD
	CALL {$spAttachableFiles}('{$idUser}', '{$idArticle}');
EOD;
}

$mysqli = $db->ConnectToDatabase();
$result = $db->performMultiQueryAndStore($query);


// preparing form

$fileForm = <<< EOD
	<form id='file_form' name='file_form' action='{$action}' method='post'>
	<select name='idFile' id='idFile'>
	
EOD;

while($row = $result[0]->fetch_object()) {

	$idFile = $row->idFile;
	$name = $row->name;
	
	$fileForm .= <<< EOD
		<option value="{$idFile}" name='filename'>{$name}</option>
EOD;

}

$fileForm .= <<< EOD
			</select>
			<input type='hidden' id='idArticle' name='idArticle' value='{$idArticle}' />
			<input type='hidden' id='updateOrDelete' name='updateOrDelete' value='{$updateOrDelete}' />
			<button type='submit' id='submitFile' name='submitFile'>{$attachOrDelete}</button>
		</form>
EOD;

//--------------------------------------------------------------------------------------------
//
//	echoing form, creating html callback
//

echo $fileForm;

//--------------------------------------------------------------------------------------------
//
//
//

?>