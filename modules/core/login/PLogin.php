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
//$iFilter->UserLoginStatus();
//$iFilter->UserGroupStatus();

//---------------------------------------------------------------------------------------------
//
//	creating object
//

$redirect = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : '';

$pc = new CPageController();
// $redirect = $pc->SESSIONIsSetOrSetDefault('history2');



//---------------------------------------------------------------------------------------------
//
//    taking care of post and get variables
//

$showErrorMessage = "";
$loginRedirect = "";
$redir = isset($_GET['redirect']) ? $_GET['redirect'] : '';
$expire = $pc->GETIsSetOrSetDefault('expire');
$errorMessage = $pc->SESSIONIsSetOrSetDefault('errorMessage');
unset($_SESSION['errorMessage']);

if($expire == "expire") {
	$errorMessage = "Session expired due to inactivity";
}

if($errorMessage != "") {

    $showErrorMessage = <<< EOD
        <div class='errorMessage'>
            {$errorMessage}
        </div>
EOD;

}

global $gModule;

//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$subHeader = "";

$centerBody = <<< EOD
    {$showErrorMessage}
        <div class='formatLogin'>
        
        <form action='?m={$gModule}&amp;p=loginp' method='post'>
        <input type='hidden' name='loginRedirect' value='{$redirect}' />
            
            <table>
            <tr>
            <td class='login'>Användare:</td>
            <td class='login'><input type='text' name='userName'/></td>
            </tr>
            <tr>
            <td class='login'>Lösenord:</td>
            <td class='login'><input type='password' name='userPwd' /></td>
            </tr>
            <tr>
            <td colspan='2' class='button'><button type='submit'>logga in</button></td>
            </tr>
            </table>
    
        </form>
       
        </div>
        
        <div class='clear'></div>
    
    <br />
    <br />
EOD;

$leftBody = <<< EOD
	
EOD;

$rightBody = "";

	

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Login";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>