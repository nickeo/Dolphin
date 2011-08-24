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
*    Description: form for new message
*
*    Author: Niklas Odén
*
***********************************************************************************************/



$pc = new CPageController();

//---------------------------------------------------------------------------------------------
//	interceptionfilter
//
//

$iFilter = new CInterceptionFilter();
$iFilter->FrontControllerIsVisited();
$iFilter->UserLoginStatus();


//---------------------------------------------------------------------------------------------
//
//	taking care of get variables
//

$articleId = isset($_GET['articleId']) ? $_GET['articleId'] : 0;

if(isset($_GET['articleTitle'])) {

	
	$articleTitle = isset($_GET['articleTitle']) ? $_GET['articleTitle'] : '';

	$headline = "Skriv ett svar";
	$title= "Aktuellt ämne: \"" . $articleTitle ."\"";
	$theTitle = <<< EOD
			<td style='border:0px; padding: 0px 0px 20px 0px;'><h3>{$title}</h3></td>
            <td style='border:0px;'></td>
EOD;

} else {

	$headline = "Ny artikel";
	
	$theTitle = <<< EOD
			<td style='border:0px; padding: 0px 0px 20px 0px;'><label for='articleTitle'>Titel</label></td>
            <td style='border:0px;'><input type='text' name='articleTitle' size=50; class='articleForm'/></td>
EOD;
}

//---------------------------------------------------------------------------------------------
//
//   creating necessary objects
//

$artNav = new CNavigation();
$articleNav = $artNav->CreateLinksToArticles();
$pc = new CPageController();

//---------------------------------------------------------------------------------------------
//
//    the content of the page
//<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>

$javaPath = WS_JAVASCRIPT;
$centerBody = <<< EOD
  
    <script type="text/javascript">
  		$(document).ready(function() {
  			$(".markItUp").markItUp(mySettings);
  			});
  	</script>

    
    <fieldset class='formatField'>
    <legend class='formatlegend'></legend>
        <div style='float:left;'>
        <form action='?m=tuna&amp;p=newtopicp' method='POST'>
        <input type='hidden' name='parentId' value='{$articleId}'>
            <br/><br/>
            <table>
            <tr>
            {$theTitle}
            </tr>
            <tr>
            
            <td style='border:0px; padding: 0 0 10px 0;'colspan='2'><textarea  name='articleText'  rows='22' cols='65' class="markItUp"></textarea></td>
            </tr>
            <tr>
            <td colspan='2' style='border:0px; text-align:right;'><button type='submit'>Posta</button></td>
            </tr>
            </table>
    
        </form>
        </div>
        <div class='right'>
        	
        </div>
        <div class='clear'></div>
    </fieldset>
EOD;

$leftBody = "";
$rightBody = <<< EOD
	{$articleNav}

EOD;
$subHeader = <<< EOD
	<h1>{$headline}</h1>
EOD;
	

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Ny artikel";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>