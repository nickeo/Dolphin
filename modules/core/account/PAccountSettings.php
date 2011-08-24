<?php
/**********************************************************************************************
//
//	Dolphin , software to build webbapplications.
//	Copyright (C) 2011 Niklas Odén (niklasoden@hotmail.com)
//
// 	This file is part of Dolphin.
//
// 	Dolphin is free software: you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation, either version 3 of the License, or
// 	(at your option) any later version.
//
// 	Dolphin is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// 	GNU General Public License for more details.
//
// 	You should have received a copy of the GNU General Public License
// 	along with Dolphin. If not, see <http://www.gnu.org/licenses/>.
//
//
//    Description: presenting login form
//
//    Author: Niklas Odén
//
***********************************************************************************************/

//---------------------------------------------------------------------------------------------
//
//    error reporting on
//
error_reporting(E_ALL);

//---------------------------------------------------------------------------------------------
//
//	creating object
//
//$redirect = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : '';

//$pc = newCPageController;
// $redirect = $pc->SESSIONIsSetOrSetDefault('history2');

$pc = new CPageController();
$nav = new CNavigation();
//---------------------------------------------------------------------------------------------
//
//    taking care of variables
//

$userId = $_SESSION['idUser'];

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
$navigation = $nav->userControlNavigation();
$subHeader = "<h1>Redigera konto</h1>";

$centerBody = <<< EOD
    	 
    	 
        <div class='formatSettings'>
        	
        <form action='{$action}' method='post'>
    	 <input type='hidden' name='loginRedirect' value='' />
            <h3>Lösenord</h3>
            <hr style='border-style:solid; border-width:1px 0 0 0; border-color:#dddddd; margin: 0 0 10px 0;' />
            <table>
            <tr>
            <td class='login'>Namn:</td>
            <td class='login'><input type='text' name='userName' size='35' value='{$name}' readonly /></td>
            </tr>
            <tr>
            <td class='login'>Lösenord:</td>
            <td class='login'><input type='password' name='userPwd' size='35'/></td>
            </tr>
            <tr>
            <td class='login'>Bekräfta lösenord:</td>
            <td class='login'><input type='password' name='confPwd'  size='35'/></td>
            </tr>
            <tr>
            <td colspan='2' class='button'><button type='submit' class='submitbutton' id='pwdbutton' name='submit' value="changepwd" >Ändra lösenord</button></td>
            </tr>
            </table>
    		</form>
        
       
        </div>
        <div class='formatSettings'>
        	<form action='?p=accountsp' method='post'>
    	 	<input type='hidden' name='loginRedirect' value='' />
        	<h3>Mejl</h3>
        	<hr style='border-style:solid; border-width:1px 0 0 0; border-color:#dddddd; margin: 0 0 10px 0;' />
            <table>
            <tr>
            <td class='login'>Epost:</td>
            <td class='login'><input type='text' name='email' size='35' value='{$email}' /></td>
            </tr>
    
            <tr>
            <td colspan='2' class='button'><button type='submit' name='submit' class='submitbutton' id='mailbutton' value='changemail'>Ändra mejladress</button></td>
            </tr>
            </table>
            </form>
        </div>
        <div class='formatSettings'>
        <form action='?p=accountsp' method='post'>
    	 <input type='hidden' name='loginRedirect' value='' />
        	<h3>Avatar</h3>
        	<hr style='border-style:solid; border-width:1px 0 0 0; border-color:#dddddd; margin: 0 0 10px 0;' />
            <table>
            <tr>
            <td class='login'>Avatar:</td>
            <td class='login'><input type='text' name='avatar' size='35' value='{$avatar}' /></td>
            <td rowspan='2' style='vertical-align:bottom; padding-left:30px;'><img src='{$avatar}' alt='Gravatar Image' height='80'/></td>
            </tr>
    
            <tr>
            <td colspan='2' class='button'><button type='submit' name='submit' class='submitbutton' id='avatarbutton' value='changeavatar' >Ändra avatar</button></td>
            </tr>
            </table>
            </form>
        </div>
        <div class='formatSettings'>
        <form action='?p=accountsp' method='post'>
    	 <input type='hidden' name='loginRedirect' value='' />
        	<h3>Gravatar</h3>
        	<hr style='border-style:solid; border-width:1px 0 0 0; border-color:#dddddd; margin: 0 0 10px 0;' />
            <table>
            <tr>
            <td class='login'>Gravatar:</td>
            <td class='login'><input type='text' name='gravatar' size='35' value='{$gravatar}' /></td>
            <td rowspan='2' style='vertical-align:bottom; padding-left:30px;'><img src='{$gravatarLink}' alt='Gravatar Image' /></td>
            </tr>
    
            <tr>
            <td colspan='2' class='button'><button type='submit' name='submit' class='submitbutton' id='gravatarbutton' value='changegravatar' >Ändra gravatar</button></td>
            
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