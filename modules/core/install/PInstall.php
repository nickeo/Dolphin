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
*    Description: installing information
*
*    Author: Niklas Odén
*
***********************************************************************************************/

//---------------------------------------------------------------------------------------------
//	interceptionfilter
//
//
/*
$iFilter = new CInterceptionFilter();
$iFilter->FrontControllerIsVisited();
$iFilter->UserLoginStatus();
*/
//---------------------------------------------------------------------------------------------
//
//    error reporting on
//
error_reporting(E_ALL);

require_once(TP_SOURCEPATH . 'FCheckLayout.php');

//---------------------------------------------------------------------------------------------
//
//    taking care of post and get variables
//

global $gModule;

$action = "?m={$gModule}&amp;p=installp";

$showErrorMessage = "";
$loginRedirect = "";
$redir = "";

$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : '';
unset($_SESSION['errorMessage']);

if($errorMessage != '') {

    $showErrorMessage = <<< EOD
        <div class='errorMessage'>
            {$errorMessage}
        </div>
EOD;

}



//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$subHeader = <<< EOD
	<h1>Installation av databas</h1>
EOD;

$centerBody = <<< EOD
   
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
    	 		<button type='submit' class='submitbutton' id='pwdbutton' name='submit' value="changepwd" >Ändra lösenord</button>
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
    	 		<button type='submit' name='submit' class='submitbutton' id='mailbutton' value='changemail'>Ändra mejladress</button>
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
    	 		<button type='submit' name='submit' class='submitbutton' id='avatarbutton' value='changeavatar' >Ändra avatar</button>
    	 	</div>
    	 	<div class='clear'></div>
    	 </div>
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
    	 		<button type='submit' name='submit' class='submitbutton' id='gravatarbutton' value='changegravatar' >Ändra gravatar</button>
    	 	</div>
    	 	<div class='clear'></div>
    	 </div>
         
        <div class='clear'></div>
    	</form>
    
EOD;

$leftBody = "";

$rightBody = "<a href='?p=installp'>Installera</a>";
//echo $body;
	

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Installera";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>