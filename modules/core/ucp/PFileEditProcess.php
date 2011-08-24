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
$uniqueName = $pc->POSTIsSetOrSetDefault('uniqueName');
$spUpdateOrDeleteFile = DBSP_PUpdateOrDeleteFile;

$name = $pc->POSTIsSetOrSetDefault('name');
$mimetype = $pc->POSTIsSetOrSetDefault('mimetype');
$UpdateOrDelete = $pc->POSTIsSetOrSetDefault('submit');

$message = ($UpdateOrDelete == "delete") ? "borttagen" : "uppdaterad";

$archivePath = FILE_ARCHIVE_PATH . DIRECTORY_SEPARATOR . $accountUser . DIRECTORY_SEPARATOR;
global $gModule;

$navSet = $pc->GetIsSetOrSetDefault('p');
$navigation = $nav->userControlNavigation($navSet);

// -------------------------------------------------------------------------------------------
//
// Prepare and perform query
//

$db = new CDatabaseController();
$mysqli = $db->connectToDatabase();
$result = ARRAY();



$query = <<< EOD
	CALL {$spUpdateOrDeleteFile}('{$idUser}', '{$uniqueName}', '{$name}', '{$mimetype}', '{$UpdateOrDelete}', @pSuccess );
	SELECT @pSuccess AS success;
EOD;

$result = $db->performMultiQueryAndStore($query);

$linkfile = "?m={$gModule}&amp;p=details&amp;uniquename=";


$row = $result[1]->fetch_object();
$success = $row->success;

if($success == 0){

	$dbfiles = <<< EOD
	
	<h3>Uppgifter om filen</h3>
	<h3>Namn: {$name}</h3>
	<h3>Unikt namn: {$uniqueName}</h3>
	<h3>Mimetype: {$mimetype}</h3>
	<h3>Uppgift: Filen {$message}</h3>
	
	
EOD;

if($UpdateOrDelete == 'delete') {
		$deletefile = $archivePath . $uniqueName;
		if(is_writeable($deletefile)) {
			$successMessage = "<h1>Filen kan skrivas. </h1>" . substr(sprintf('%o', fileperms($deletefile)), -4);
			if(unlink($deletefile)) {
				$successMessage .= "<h3>Filen: {$deletefile} raderad.</h3>";
				};
		} else {
			$successMessage = "<h3>Filen existerar inte</h3>";
		}

}
}
$mysqli->close();
//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$subHeader = "<h1>Filarkiv</h1>";




$centerBody = <<< EOD
    	{$dbfiles}
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
$title = "Filarkiv";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>