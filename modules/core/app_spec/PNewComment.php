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
*    Description: adding a new comment
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


//---------------------------------------------------------------------------------------------
//
//    taking care of post and get variables
//

$messageId = isset($_GET['messageId']) ? $_GET['messageId'] : '';


//---------------------------------------------------------------------------------------------
//
//    the content of the page
//


$centerBody = <<< EOD
    
    <fieldset class='formatField'>
    <legend class='formatlegend'>Skriv kommentar</legend>
        <div style='float:left;'>
        <form action='?p=newcommentp' method='post'>
        <input type='hidden' name='messageId' value='{$messageId}' />
            <br/><br/>
            <table>
            <tr>
            <td style='border:0px;'>Signatur:</td>
            <td style='border:0px;'><input type='text' name='commentSignature'/></td>
            </tr>
            <tr>
            <td style='border:0px;'>E-post:</td>
            <td style='border:0px;'><input type='text' name='commentMail'/></td>
            </tr>
            <tr>
            <td style='border:0px;'>Titel:</td>
            <td style='border:0px;'><input type='text' name='commentTitle'/></td>
            </tr>
            <tr>
            <td style='border:0px;'>Kommentar:</td>
            <td style='border:0px;'><textarea  name='newComment'  rows='4' cols='50'></textarea></td>
            </tr>
            <tr>
            <td colspan='2' style='border:0px; text-align:right;'><button type='submit'>submit</button></td>
            </tr>
            </table>
    
        </form>
        </div>
        	<div class='right'>
        		<a href = 'javascript:history.go(-1)'>Tillbaka</a>
        	</div>
        <div class='clear'></div>
    </fieldset>
EOD;

$leftBody = "";

$rightBody = "";
//echo $body;
	

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//

require_once('src/CHTMLPage.php');
$page = new CHTMLPage($layout);

$page->printHTMLHeader(WS_TITLE);
$page->printPageHeader();
$page->printPageBody($leftBody, $centerBody, $rightBody);
$page->printPageFooter();

?>