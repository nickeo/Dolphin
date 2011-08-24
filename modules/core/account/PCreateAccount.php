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
//   Description: form for creating new account
//
//   Author: Niklas Odén
//
***********************************************************************************************/

//---------------------------------------------------------------------------------------------
//
//   Interception filter
//

if(!$frontDoor) 
	die('No direct access to pagecontroller allowed!');


//---------------------------------------------------------------------------------------------
//
//	 is self-register enabled?
//

if(USER_SELF_REGISTER != true) {
header('Location: ' . WS_SITELINK . "?m=core&p=403");
EXIT;
}

//---------------------------------------------------------------------------------------------
//
//	taking care of variables
//

global $gModule;

$redirect = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : '';

$showErrorMessage = "";
$loginRedirect = "";
$redir = isset($_GET['redirect']) ? $_GET['redirect'] : '';

$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : '';
unset($_SESSION['errorMessage']);

if($errorMessage != "") {

    $showErrorMessage = <<< EOD
        <div class='errorMessage'>
            <h3>Inloggningen misslyckades</h3>
            {$errorMessage}
        </div>
EOD;

}


// -------------------------------------------------------------------------------------------
//
// Prepare the CAPTCHA 
//
if(USE_RECAPTCHA == true) {
$captcha = new CCaptcha();
$recaptchaHtml = $captcha->GetRecaptchaHTML();

// customize captcha

$style = RECAPTCHA_STYLE;
$customizeCaptcha = "";
if($style != "") {
	$customizeCaptcha = $captcha->CustomizeCaptcha($style);
}

} else {
	$recaptchaHtml = "";
	$customizeCaptcha = "";
}
//---------------------------------------------------------------------------------------------
//
//    the content of the page
//

$subHeader = "<h1>Skapa nytt konto</h1>";

$centerBody = <<< EOD
    
    {$customizeCaptcha}
        <div class='formatLogin'>
        
        <form action='?m={$gModule}&amp;p=createap' method='post'>
        <input type='hidden' name='loginRedirect' value='{$redirect}' />
            
            <table>
            <tr>
            <td class='login'>Användare:</td>
            <td class='login'><input type='text' name='userName' size='30'/></td>
            </tr>
            <tr>
            <td class='login'>Lösenord:</td>
            <td class='login'><input type='password' name='userPwd' size='30'/></td>
            </tr>
            <tr>
            <td class='login'>Bekräfta lösenord:</td>
            <td class='login'><input type='password' name='confPwd'  size='30'/></td>
            </tr>
            <tr>
            	<td colspan='2' style='padding: 20px 12px 20px 8px;'>{$recaptchaHtml}</td>
            </tr>
            <tr>
            <td colspan='2' class='button'><button type='submit' class='submitbutton'>Skapa konto</button></td>
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
	<h3>Komplettera</h3>
	<p>När du skapat ett konto kan du uppdatera din profil med mer info.</p>
	{$showErrorMessage}
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