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
//    Interception filter
//

if(!$frontDoor) 
	die('No direct access to pagecontroller allowed!!!');

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

$pc->LoadLanguage(__FILE__);

//---------------------------------------------------------------------------------------------
//
//	taking care of variables
//

$accountUser = $pc->SESSIONIsSetOrSetDefault('accountUser');
$idUser = $pc->SESSIONIsSetOrSetDefault('idUser');
$uniqueName = $pc->GETIsSetOrSetDefault('uniquename');

$spPAdminListAccounts = DBSP_PAdminListAccounts;
$fCheckUserIsAdmin = DBF_FCheckUserIsAdmin;

$link = "?m=core&amp;p=editaccount&amp;id=";
global $gModule;

// -------------------------------------------------------------------------------------------
//
// Prepare and perform query
//

$db = new CDatabaseController();
$mysqli = $db->connectToDatabase();
$result = ARRAY();

$query = <<< EOD
	CALL {$spPAdminListAccounts}();
	
EOD;

$result = $db->performMultiQueryAndStore($query);

$linkfile = "?m={$gModule}&amp;p=details&amp;uniquename=";


$gravatarHeader = (USER_GRAVATAR) ? "<th class='listFilesAdmin2'>{$pc->lang['GRAVATAR']}</th>" : "";

	$dbAccounts = <<< EOD
	
	<div >
	<table style='width:900px;'>
		<tr>
			<th class='listFilesAdmin2'>{$pc->lang['ID']}</th>
			<th class='listFilesAdmin2'>{$pc->lang['NAME']}</th>
			<th class='listFilesAdmin2'>{$pc->lang['EMAIL']}</th>
			<th class='listFilesAdmin2'>{$pc->lang['AVATAR']}</th>
			{$gravatarHeader}
			<th class='listFilesAdmin2'>{$pc->lang['STATUS']}</th>
		</tr>
EOD;

while($row = $result[0]->fetch_object()) {
		
	$id = $row->id;
	$account = $row->account;
	$email = $row->email;
	$avatar = $row->avatar;
	$gravatar = $row->gravatar;
	$groupId = $row->groupId;
	
	$fileAvatar = basename($avatar);
	$gravatarInfo = (USER_GRAVATAR) ? "<td class='listFiles'>{$gravatar}</td>" : "";
	
		$dbAccounts .= <<< EOD
		<tr>
			<td class='listFiles' title=''><a href='{$link}{$id}' class='adminBlue'>{$id}</a></td>
			<td class='listFiles'><a href='{$link}{$id}' class='adminBlue'>{$account}</a></td>
			<td class='listFiles'>{$email}</td>
			<td class='listFiles'>{$fileAvatar}</td>
			{$gravatarInfo}
			<td class='listFiles'>{$groupId}</td>
			
		
		</tr>
EOD;
}

$dbAccounts .= <<< EOD
		</table>
		</div>
EOD;



$result[0]->close();
$mysqli->close();
//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$subHeader = "<div class='adRightBlue'><a href='?m=core&amp;p=admincontrol' class='big'>{$pc->lang['CONTROL_PANEL']}</a></div><h1>{$pc->lang['ACCOUNT']}</h1>";

$centerBody = <<< EOD
    	{$dbAccounts}
    	<br />
    	
EOD;
$leftBody = <<< EOD
EOD;

$rightBody = <<< EOD
	
	
EOD;

	

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "{$pc->lang['THIS_TITLE']}";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>