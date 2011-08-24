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
$iFilter->UserGroupStatus();

//---------------------------------------------------------------------------------------------
//
//	creating object
//

$pc = new CPageController();
$nav = new CNavigation();
$pc->LoadLanguage(__FILE__);
//---------------------------------------------------------------------------------------------
//
//    taking care of variables
//

$userId = $pc->GETIsSetOrSetDefault('id');

$action = "?m=core&amp;p=editaccountp";
$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : "";

//---------------------------------------------------------------------------------------------
//
//    preparing and performing query
//

$result = ARRAY();
$spGetAccountInfo = DBSP_PGetAccountInfo;

$db = new CDatabaseController();
$mysqli = $db->connectToDatabase();

$query = <<< EOD
	CALL {$spGetAccountInfo}({$userId});
EOD;
 $res = $db->performDirectMultiQuery($query);
$db->getAndStoreResults($result);
$row = $result[0]->fetch_object();

	$name = $row->account;
	$email = $row->email;
	$avatar = $row->avatar;
	$gravatar = $row->gravatar;
	$gravatarLink = $row->gravatarLink;

$result[0]->close();
$mysqli->close();
//---------------------------------------------------------------------------------------------
//
//    the content of the page
//


//preparing gravatar

if(USER_GRAVATAR) {

	if($gravatarLink == "" || $gravatarLink == NULL) {
		$gravatarLink = WS_IMAGES . "usergrey.png";
	}

	$gravatarForm = <<< EOD
		<div class='settingsWrapper'>
    	 	<h3>{$pc->lang['USER_GRAVATAR']}</h3>
    	 	<hr class='soft'>
    	 	<div class='settingsLeft'>
    	 		<img src='{$gravatarLink}' alt='Gravatar Image' height='80' />		
    	 	</div>
    	 	<div class='settingsMiddle'>
    	 		<input type='text' name='gravatar' size='35' value='{$gravatar}' />
    	 	</div>
    	 	<div class='settingsRight'>
    	 		<button type='submit' name='submit' class='submitbutton' id='gravatarbutton' value='changegravatar' onclick="this.value='changegravatar';">{$pc->lang['CHANGE_GRAVATAR']}</button>
    	 	</div>
    	 	<div class='clear'></div>
    	 </div>
EOD;

} else {
	$gravatarForm = "";
}

$subHeader = "<div class='adRightBlue'><a href='?m=core&amp;p=admincontrol' class='big'>{$pc->lang['CONTROL_PANEL']}</a></div><h1>{$pc->lang['THIS_HEADER']}</h1>";

$centerBody = <<< EOD
    	 
    	 
        <div class='formatAccountSettings'>

        <form action='{$action}' method='post'>
    	 <input type='hidden' name='userid' value='{$userId}' />
    	 <div class='settingsWrapper'>
    	 	<h3>{$pc->lang['USER_PASSWORD']}</h3>
    	 	<hr class='soft'>
    	 	<div class='settingsLeft'>
    	 		<p class='settings'>{$pc->lang['USER_NAME']}:</p>
    	 		<p class='settings'>{$pc->lang['USER_PASSWORD']}:</p>
    	 		<p class='settings'>{$pc->lang['USER_CONFIRM_PASSWORD']}:</p>
    	 	</div>
    	 	<div class='settingsMiddle'>
    	 		<input type='text' name='userName' size='35' value='{$name}' readonly class='settingsForm'/>
    	 		<input type='password' name='userPwd' size='35' class='settingsForm'/>
    	 		<input type='password' name='confPwd'  size='35' class='settingsForm'/>
    	 	</div>
    	 	<div class='settingsRight'>
    	 		<button type='submit' class='submitbutton' id='pwdbutton' name='submit' value="changepwd" onclick="this.value='changepwd';">{$pc->lang['CHANGE_PASSWORD']}</button>
    	 	</div>
    	 	<div class='clear'></div>
    	 </div>
    	 <div class='settingsWrapper'>
    	 	<h3>{$pc->lang['USER_EMAIL']}</h3>
    	 	<hr class='soft'>
    	 	<div class='settingsLeft'>
    	 		<p class='settings'>{$pc->lang['USER_EMAIL']}:</p>
    	 		
    	 	</div>
    	 	<div class='settingsMiddle'>
    	 		<input type='text' name='email' size='35' value='{$email}' class='settingsForm'/>
    	 	</div>
    	 	<div class='settingsRight'>
    	 		<button type='submit' name='submit' class='submitbutton' id='mailbutton' value='changemail' onclick="this.value='changemail';">{$pc->lang['CHANGE_EMAIL']}</button>
    	 	</div>
    	 	<div class='clear'></div>
    	 </div>
    	  <div class='settingsWrapper'>
    	 	<h3>{$pc->lang['USER_AVATAR']}</h3>
    	 	<hr class='soft'>
    	 	<div class='settingsLeft'>
    	 		<img src='{$avatar}' alt='Avatar Image' height='80'/>	
    	 	</div>
    	 	<div class='settingsMiddle'>
    	 		<input type='text' name='avatar' size='35' value='{$avatar}' />
    	 	</div>
    	 	<div class='settingsRight'>
    	 		<button type='submit' name='submit' class='submitbutton' id='avatarbutton' value='changeavatar' onclick="this.value='changeavatar';">{$pc->lang['CHANGE_AVATAR']}</button>
    	 	</div>
    	 	<div class='clear'></div>
    	 </div>
    	  {$gravatarForm}
         
        <div class='clear'></div>
    	</form>
    	</div>
    
EOD;

$leftBody = <<< EOD
EOD;

$rightBody = <<< EOD
	
EOD;

	

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Redigera konto";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>