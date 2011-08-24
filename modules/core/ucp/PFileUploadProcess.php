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
*    Description: presenting login form
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
//    creating objects
//

$pc = new CPageController();
$nav = new CNavigation();

//---------------------------------------------------------------------------------------------
//
//	taking care of variables
//

$navSet = $pc->GETIsSetOrSetDefault('p');
$accountUser = $pc->SESSIONIsSetOrSetDefault('accountUser');
$submitAction = $pc->POSTisSetOrSetDefault('dosubmit');
$idUser = $_SESSION['idUser'];
$accountUser = $_SESSION['accountUser'];
$deletefile = "";
$successMessage = <<< EOD
	<br /><br />
					<h2>Uppladdningen lyckades!</h2>
					<br /><br />

EOD;
$failureMessage = "";

//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$subHeader = "<h1>Uppladdning</h1>";
$navigation = $nav->userControlNavigation($navSet);

$archivePath = FILE_ARCHIVE_PATH . DIRECTORY_SEPARATOR . $accountUser . DIRECTORY_SEPARATOR;
if(!is_dir($archivePath)) {
	mkdir($archivePath);
}

$target = $archivePath;

if($submitAction == 'ajaxupload') {

	if($_FILES['file']['error'] == 0) {

		if(isset($_FILES['file'])) {
			
			$temp_dir = $_FILES['file']['tmp_name'];
			$file_name = basename($_FILES['file']['name']);
			$size = $_FILES['file']['size'];
			$mimetype = $_FILES['file']['type'];
			
			// set unique name
			$uniqueName = strtolower($accountUser) . time();
			
			$path = $target . $uniqueName;

			if(move_uploaded_file($temp_dir, $target . $uniqueName)) {
			
			$tblFile = DBT_File;
			$spInsertFile = DBSP_PInsertFile;
			$db = new CDatabaseController();
			$mysqli = $db->ConnectToDatabase();
			
			$query = <<< EOD
				CALL {$spInsertFile}('{$idUser}', '{$file_name}', '{$uniqueName}', '{$path}', '{$size}', '{$mimetype}');
EOD;
			$db->performDirectMultiQuery($query);
			$mysqli->close();
			
			
			$successMessage .= <<< EOD
				<p style='font-size:14px;'>Filen: <i>{$file_name} </i> uppladdad. Unikt namn: {$uniqueName}</p>
				<br /><br />
				
				<br /><br />
				<div class='clear'></div>
EOD;
			$uploadStatus = true;
			}
		} 	
	
		} else {
			$error = $_FILES['file']['error'];
			echo $error;
	
	}

} else if($submitAction == 'singlefile') {
	
	if($_FILES['file']['error'] == 0) {

		if(isset($_FILES['file'])) {
			
			$temp_dir = $_FILES['file']['tmp_name'];
			$file_name = basename($_FILES['file']['name']);
			$size = $_FILES['file']['size'];
			$mimetype = $_FILES['file']['type'];
			
			// set unique name
			$uniqueName = strtolower($accountUser) . time();
			
			$path = $target . $uniqueName;

			if(move_uploaded_file($temp_dir, $target . $uniqueName)) {
			$successMessage .= <<< EOD
		
					
				<p style='font-size:14px;'>Filen: <i>{$file_name} </i> uppladdad. Unikt namn: {$uniqueName}</p>
				<br /><br />
				<a href='?m=files&amp;p=upload'><button class='custombutton'>Ladda upp fler filer</button></a>
				<br /><br /><br /><br />
				<div class='clear'></div>
EOD;
			$tblFile = DBT_File;
			$spInsertFile = DBSP_PInsertFile;
			$db = new CDatabaseController();
			$mysqli = $db->ConnectToDatabase();
			
			$query = <<< EOD
				CALL {$spInsertFile}('{$idUser}', '{$file_name}', '{$uniqueName}', '{$path}', '{$size}', '{$mimetype}');
EOD;
			$db->performDirectMultiQuery($query);
			$mysqli->close();
			}
		} 	
	
		} else {
			$error = $_FILES['file']['error'];
			echo $error;
	
	}
} else if($submitAction == 'multiplefiles') {
			$nr = 1;
			$db = new CDatabaseController();
			$mysqli = $db->ConnectToDatabase();
			$tblFile = DBT_File;
			$spInsertFile = DBSP_PInsertFile;		
			
	foreach($_FILES["file"]["name"] as $key =>$name) {
		if($name != "") {
			$temp_dir = $_FILES["file"]["tmp_name"][$key];
			$file_name = basename($_FILES["file"]["name"][$key]);
			$size = $_FILES['file']['size'][$key];
			$mimetype = $_FILES['file']['type'][$key];
			// set unique name
			$uniqueName = strtolower($accountUser) . time() . $nr;
			$path = $target . $uniqueName;
			
			if(move_uploaded_file($temp_dir, $target . $uniqueName)) {
				$successMessage .= <<< EOD
					<p style='font-size:14px;'>Filen: <i>{$_FILES["file"]["name"][$key]} </i> uppladdad.</p>
EOD;
				$query = <<< EOD
				CALL {$spInsertFile}('{$idUser}', '{$file_name}', '{$uniqueName}', '{$path}', '{$size}', '{$mimetype}');
EOD;
				$db->performDirectMultiQuery($query);
				$nr++;
			} else {
				$failureMessage .= <<< EOD
					<p>Filen: {$_FILES["file"]["name"][$key]} kunde inte laddas upp.</p>
					{$_FILES["file"]["error"][$key]}
EOD;
			}
		}
	}
	
	
	
	/*$deletefile = $archivePath . "dolphin13138338801";
	if(is_writeable($deletefile)) {
		$successMessage = "<h1>Filen kan skrivas. </h1>" . substr(sprintf('%o', fileperms($deletefile)), -4);
		if(unlink($deletefile)) {
		$successMessage .= "<h3>Filen: {$deletefile} raderad.</h3>";
	};
	} else {
		$successMessage = "<h3>Filen existerar inte</h3>";
	}*/
	
	
	
} else {
	$successMessage = "<h3>Inget hände</h3>";
}


$maxFileSize = MAX_FILE_SIZE;

$centerBody = <<< EOD
    {$successMessage}
        {$deletefile}
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

if($submitAction == 'ajaxupload') {
	echo $successMessage;
} else {
$title = "Skapa nytt konto";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

}

?>