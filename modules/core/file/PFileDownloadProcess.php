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

//---------------------------------------------------------------------------------------------
//
//	taking care of variables
//

$accountUser = $pc->SESSIONIsSetOrSetDefault('accountUser');
$idUser = $pc->SESSIONIsSetOrSetDefault('idUser');
$file = $pc->GETIsSetOrSetDefault('file');
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

	

$row = $result[2]->fetch_object();
$success = $row->success;

if($success) {
	
}

$result[2]->close();
$result[0]->close();
$mysqli->close();



// The file must exist, else redirect to 404
if(!is_readable($path)) {
	die("File does not exist!");
}


// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
header("Content-type: {$mimetype}");
header("Content-Disposition: attachment; filename=\"{$name}\"");
readfile($path);
exit;


?>