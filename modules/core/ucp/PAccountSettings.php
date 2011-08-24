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
*    Description: handling account settings
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
//	creating object
//

$pc = new CPageController();
$nav = new CNavigation();

//---------------------------------------------------------------------------------------------
//
//    taking care of variables
//

$userId = $pc->SESSIONIsSetOrSetDefault('idUser');
$navSet = $pc->GETIsSetOrSetDefault('p');
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

$action = "?p=accountsp";
$navigation = $nav->userControlNavigation($navSet);
$subHeader = "<h1>Redigera konto</h1><br /><br/>";

// preparing gravatar

if(USER_GRAVATAR) {
	$gravatarForm = <<< EOD
	<div class='settingsWrapperUser'>
    	 	<h3>Gravatar</h3>
    	 	<hr class='soft'>
    	 	<div class='settingsLeft'>
    	 		<img src='{$gravatarLink}' alt='Gravatar Image' />		
    	 	</div>
    	 	<div class='settingsMiddle'>
    	 		<input type='text' name='gravatar' size='35' value='{$gravatar}' />
    	 	</div>
    	 	<div class='settingsRight'>
    	 		<button type='submit' name='submit' class='submitbutton' id='gravatarbutton' value='changegravatar' onclick="this.value='changegravatar';">Ändra gravatar</button>
    	 	</div>
    	 	<div class='clear'></div>
    	 </div>
EOD;
} else {
	$gravatarForm = "";
}
$centerBody = <<< EOD

        <div class='formatAccountSettings'>

        <form action='{$action}' method='post'>
    	 <input type='hidden' name='userid' value='{$userId}' />
    	 <div class='settingsWrapperUser'>
    	 	<h3>Lösenord</h3>
    	 	<hr class='soft'/>
    	 	<div class='settingsLeft'>
    	 		<p class='settings'>Namn:</p>
    	 		<p class='settings'>Lösenord:</p>
    	 		<p class='settings'>Bekräfta lösenord:</p>
    	 	</div>
    	 	<div class='settingsMiddle'>
    	 		<input type='text' name='userName' size='35' value='{$name}' readonly class='settingsForm'/>
    	 		<input type='password' name='userPwd' size='35' class='settingsForm'/>
    	 		<input type='password' name='confPwd'  size='35' class='settingsForm'/>
    	 	</div>
    	 	<div class='settingsRight'>
    	 		<button type='submit' class='submitbutton' id='pwdbutton' name='submit' value="changepwd" onclick="this.value='changepwd';">Ändra lösenord</button>
    	 	</div>
    	 	<div class='clear'></div>
    	 </div>
    	 <div class='settingsWrapperUser'>
    	 	<h3>Epost</h3>
    	 	<hr class='soft'>
    	 	<div class='settingsLeft'>
    	 		<p class='settings'>Epost:</p>
    	 		
    	 	</div>
    	 	<div class='settingsMiddle'>
    	 		<input type='text' name='email' size='35' value='{$email}' class='settingsForm'/>
    	 	</div>
    	 	<div class='settingsRight'>
    	 		<button type='submit' name='submit' class='submitbutton' id='mailbutton' value='changemail' onclick="this.value='changemail';">Ändra mejladress</button>
    	 	</div>
    	 	<div class='clear'></div>
    	 </div>
    	  <div class='settingsWrapperUser'>
    	 	<h3>Avatar</h3>
    	 	<hr class='soft'>
    	 	<div class='settingsLeft'>
    	 		<img src='{$avatar}' alt='Avatar Image' height='80'/>	
    	 	</div>
    	 	<div class='settingsMiddle'>
    	 		<input type='text' name='avatar' size='35' value='{$avatar}' />
    	 	</div>
    	 	<div class='settingsRight'>
    	 		<button type='submit' name='submit' class='submitbutton' id='avatarbutton' value='changeavatar' onclick="this.value='changeavatar';">Ändra avatar</button>
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
	{$navigation}
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