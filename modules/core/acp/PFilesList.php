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
*    Description: listing files
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
$nav = new CNavigation();

//---------------------------------------------------------------------------------------------
//
//	taking care of variables
//

$accountUser = $pc->SessionIsSetOrSetDefault('accountUser');
$idUser = $pc->SessionIsSetOrSetDefault('idUser');
$uniqueName = $pc->GETIsSetOrSetDefault('uniquename');
$spPAdminListFiles = DBSP_PAdminListFiles;
$fCheckUserIsAdmin = DBF_FCheckUserIsAdmin;
global $gModule;

$link = "?m={$gModule}&amp;p=fileedit";
$filesInTrashbin = 0;

// -------------------------------------------------------------------------------------------
//
// Prepare and perform query
//

$db = new CDatabaseController();
$mysqli = $db->connectToDatabase();
$result = ARRAY();

$query = <<< EOD
	CALL {$spPAdminListFiles}(@pTotalSize, @pTotalFiles);
	SELECT @pTotalSize AS totalSize;
	SELECT @pTotalFiles AS totalFiles;
	
EOD;

$result = $db->performMultiQueryAndStore($query);

$linkfile = "?m={$gModule}&amp;p=details&amp;uniquename=";


//$download = "?m={$gModule}&amp;p=download&amp;file={$uniqueName}";
//$delete = "?m={$gModule}&amp;p=delete&amp;file={$uniqueName}";
//$edit = "?m={$gModule}&amp;p=editfile&amp;uniquename={$uniqueName}";

$dbfiles = <<< EOD
	<table style='width:900px; margin-bottom:40px;'>
		<tr>
			<th class='listFilesAdmin'>Namn</th>
			<th class='listFilesAdmin'>Unikt namn</th>
			<th class='listFilesAdmin'>Typ</th>
			<th class='listFilesAdmin'>Kilobyte</th>
			<th class='listFilesAdmin'>Skapad</th>
			<th class='listFilesAdmin'>Ägare</th>
		</tr>
EOD;

$dbTrash = <<< EOD
	<h3>Filer i papperskorgen</h3>
	
	<table style='width:900px; '>
		<tr>
			<th class='listFilesAdmin'>Namn</th>
			<th class='listFilesAdmin'>Unikt namn</th>
			<th class='listFilesAdmin'>Typ</th>
			<th class='listFilesAdmin'>Kilobyte</th>
			<th class='listFilesAdmin'>Borttagen</th>
			<th class='listFilesAdmin'>Ägare</th>
			
		</tr>
EOD;


while($row = $result[0]->fetch_object()) {
		
	$fileId = $row->idFile;
	$name = $row->name;
	$uniqueName = $row->uniqueName;
	$mimetype = $row->mimetype;
	$size = $row->size;
	$path = $row->path;
	$user = $row->user;
	$created = $row->created;
	$modified = $row->modified;
	$deleted = $row->deleted;
	$userId = $row->idUser;
	$size = $size/1000;
	
	$link = "?m={$gModule}&amp;p=fileedit&amp;id={$fileId}&amp;uniquename={$uniqueName}";
	
	if($deleted == NULL) {
			
		$dbfiles .= <<< EOD
		<tr>
			<td class='listFiles' title='{$path}'><a href='{$link}' class='admin'>{$name}</a></td>
			<td class='listFiles'>{$uniqueName}</td>
			<td class='listFiles'>{$mimetype}</td>
			<td class='listFiles'>{$size}</td>
			<td class='listFiles'>{$created}</td>
			<td class='listFiles'>{$user}</td>
		
		</tr>
EOD;
	} else {
		
		$dbTrash .= <<< EOD
		<tr>
			<td class='listFiles' title='{$path}'><a href='{$link}' class='admin'>{$name}</a></td>
			<td class='listFiles'>{$uniqueName}</td>
			<td class='listFiles'>{$mimetype}</td>
			<td class='listFiles'>{$size}</td>
			<td class='listFiles'>{$deleted}</td>
			<td class='listFiles'>{$user}</td>
			
		</tr>
		
EOD;

		$filesInTrashbin++;
	}
}

$dbfiles .= <<< EOD
		</table>
		
EOD;

$dbTrash .= <<< EOD
		</table>
		</form>
EOD;

$row = $result[2]->fetch_object();
$totalSize = $row->totalSize;
if($totalSize > 1000000) {
	$totalSize = $totalSize/1000000;
	$sizeUnit = "Mb";
} else {
	$totalSize = $totalSize/1000;
	$sizeUnit = "kb";
}
$row = $result[3]->fetch_object();
$totalFiles = $row->totalFiles;

$totalInfo = "Antal filer: " . $totalFiles . "&nbsp;&nbsp;&nbsp;Sammanlagd filstorlek: " . $totalSize . " " . $sizeUnit;

if($filesInTrashbin == 0) {
	$dbTrash = "Inga filer i papperskorgen";
}

$result[2]->close();
$result[0]->close();
$mysqli->close();
//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$subHeader = "<div class='adRight'><a href='?m=core&amp;p=admincontrol' class='big'>Kontrollpanel</a></div><h1>Filarkiv</h1>";


$centerBody = <<< EOD
    	{$dbfiles}
    	
    	{$dbTrash}
    	<br /><br />
    	<p>{$totalInfo}</p>
EOD;
$leftBody = <<< EOD
EOD;

$rightBody = <<< EOD
	
	
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