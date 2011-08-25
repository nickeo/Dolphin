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
*	Description:	Attaching file to article. This page is accessed through
*					jQuery and Ajax.
*
*
**********************************************************************************************/

//---------------------------------------------------------------------------------------------
//
//   Interception filter
//

if(!$frontDoor) 
	die('No direct access to pagecontroller allowed!');


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
$idArticle = $pc->PostIsSetOrSetDefault('idArticle');
$idFile = $pc->PostIsSetOrSetDefault('idFile');


$delete = $pc->PostIsSetOrSetDefault('updateOrDelete');

$tblAttachment = DBT_Attachment;
$spAttachFile = DBSP_PAttachFile;
$spGetAttachment = DBSP_PGetAttachment;
global $gModule;
$action = "?m=files&amp;p=attachfilep";
$result = Array();
$attachedFile = "";
//--------------------------------------------------------------------------------------------
//
//	preparing and performing query
//

if($delete == true) {
	$insertOrDelete = "Delete FROM {$tblAttachment} WHERE Attachment_idFile = '{$idFile}' AND Attachment_idTopic = '{$idArticle}' limit 1;";
} else {
	$insertOrDelete = "CALL {$spAttachFile}('{$idArticle}', '{$idFile}');";
}


$query = <<< EOD
	{$insertOrDelete}
	CALL {$spGetAttachment}('{$idArticle}');
EOD;
$mysqli = $db->ConnectToDatabase();
$result = $db->performMultiQueryAndStore($query);

if($result[1]->num_rows > 0) {

$attachedFile = <<< EOD
	<br />
	<h4>Bifogade filer:</h4>
EOD;
	while($row = $result[1]->fetch_object())
	{
		$name = $row->name;
		$uniqueName = $row->uniqueName;
		$path = $row->path;
		$userId = $row->userId;
		$file = $row->fileId;
		$attachedFile .= <<< EOD
			<p>{$name}</p>
EOD;

	}
	
}
	$result[1]->close();

$message = "Fil bifogad";


$mysqli->close();

//--------------------------------------------------------------------------------------------
//
//	echoing form, creating html callback
//

echo $attachedFile;



//--------------------------------------------------------------------------------------------
//
//
//	

?>