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
*   Description: Showing all files in the archive
*
*	1. files in archive, 2. files in trashcan, 3. actual files on disc
*
*   Author: Niklas Odén
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
$navSet = $pc->GETIsSetOrSetDefault('p');
$spListFiles = DBSP_PListFiles;
$spListTrash = DBSP_PListTrash;

global $gModule;
$action = "?m={$gModule}&amp;p=editfilep";

// -------------------------------------------------------------------------------------------
//
// Prepare and perform query
//

$dbfiles = <<< EOD
	<h3>Filer listade i databasen</h3>
	<table style='width:660px;'>
		<tr>
			<th class='listFilesUser'>Namn</th>
			<th class='listFilesUser'>Unikt namn</th>
			<th class='listFilesUser'>Storlek</th>
			<th class='listFilesUser'>Typ</th>
			<th class='listFilesUser'>Skapad</th>
			
		</tr>
EOD;

$dbTrash = <<< EOD
	<h3>Filer i papperskorgen</h3>
	
	<table style='width:660px;'>
		<tr>
			<th class='listFilesUser'>Namn</th>
			<th class='listFilesUser'>Unikt namn</th>
			<th class='listFilesUser'>Storlek</th>
			<th class='listFilesUser'>Typ</th>
			<th class='listFilesUser'>Borttagen</th>
					
		</tr>	
EOD;

$db = new CDatabaseController();
$mysqli = $db->connectToDatabase();
$result = ARRAY();

$query = <<< EOD
	CALL {$spListFiles}('{$idUser}');
	CALL {$spListTrash}('{$idUser}');
EOD;

$result = $db->performMultiQueryAndStore($query);

$linkfile = "?m={$gModule}&amp;p=details&amp;uniquename=";

while($row = $result[0]->fetch_object()) {
	$dbfiles .= <<< EOD
		<tr>
			<td class='filesList'><a href='{$linkfile}{$row->uniqueName}'>{$row->name}</a></td>
			<td class='filesList'>{$row->uniqueName}</td>
			<td class='filesList'>{$row->size}</td>
			<td class='filesList'>{$row->mimetype}</td>
			<td class='filesList'>{$row->created}</td>
		</tr>
EOD;
}

$dbfiles .= <<< EOD
	<tr class='filling'></tr>
	<tr class='filling'></tr>
	</table>
EOD;

while($row = $result[2]->fetch_object()) {
	$dbTrash .= <<< EOD
		<tr>
			<td class='filesList'><a href='{$linkfile}{$row->uniqueName}' >{$row->name}</a></td>
			<td class='filesList'>{$row->uniqueName}</td>
			<td class='filesList'>{$row->size}</td>
			<td class='filesList'>{$row->mimetype}</td>
			<td class='filesList'>{$row->deleted}</td>
			
		</tr>
		
EOD;
}

$dbTrash .= <<< EOD
	<tr class='filling'></tr>
	<tr class='filling'></tr>
	</table>
	
EOD;


if($result[2]->num_rows == 0) {
	$dbTrash = "Det finns inga filer i papperskorgen";
}

// -------------------------------------------------------------------------------------------
//
// Open and read a directory, show its content
//
$dir = FILE_ARCHIVE_PATH . DIRECTORY_SEPARATOR . $accountUser;

$list = Array();
if(is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if($file != '.' && $file != '..') {
                $list[$file] = "{$file}";
            }
        }
    closedir($dh);
    }
}

ksort($list);

$archive = <<< EOD
	<h3>Filer på disk</h3>
	<table><tr><th class='listFilesUser'>Unikt namn</th></tr>
EOD;
foreach($list as $val => $key) {
    $archive .= "<tr><td>{$key}</td></tr>";
}
$archive .= '</table>';

//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$subHeader = "<h1>Filarkiv</h1>";
$navigation = $nav->userControlNavigation($navSet);
$centerBody = <<< EOD
    	{$dbfiles}
    	{$dbTrash}
      	{$archive}
EOD;
$leftBody = <<< EOD
EOD;

$rightBody = <<< EOD
	{$navigation}
EOD;

$result[0]->close();
$result[2]->close();
$mysqli->close();

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