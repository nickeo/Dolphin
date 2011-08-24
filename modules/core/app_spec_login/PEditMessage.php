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
*    Description: editing message
*
*    Author: Niklas Odén
*
***********************************************************************************************/

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
$idUser = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : '';

$spPDisplayArticle = DBSP_PDisplayArticle;

//---------------------------------------------------------------------------------------------
//
//   creating necessary objects
//

$pc = new CPageController();
$artNav = new CNavigation();
$articleNav = $artNav->articleNavigation();
$db = new CDatabaseController();

// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//

$tblArticle = DBT_Article;

$query = "call {$spPDisplayArticle}(@pGrantRights, {$articleId}, {$idUser});";

$mysqli = $db->connectToDatabase();
$res = $db->performDirectQuery($query);

$row = $res->fetch_object();

$title = $row->articleTitle;
$article = $row->articleText;


//---------------------------------------------------------------------------------------------
//
//    the content of the page
//


$centerBody = <<< EOD
    
    <fieldset class='formatField'>
    <legend class='formatlegend'></legend>
        <div style='float:left;'>
        <form action='?p=editmessagep' method='POST'>
            <input type='hidden' name='articleId' value='{$articleId}' />
            <br/>
            <table>
            <tr>
            <td style='border:0px;'><label for='articleTitle'>Titel</label></td>
            <td style='border:0px;'><input type='text' name='articleTitle' value='{$title}' size='50' style='font-weight:bold;' class='articleForm'/></td>
            </tr>
            <tr style='height:20px;'>
            <td style='border:0px;'></td>
            <td style='border:0px;'></td>
            </tr>
            <tr>
            <td style='border:0px; padding:0 0 10px 0;' colspan='2'><textarea  name='articleText'  rows='22' cols='65' class='articleForm'>{$article}</textarea></td>
            </tr>
            <tr>
            <td colspan='2' style='border:0px; text-align:right;'><input type='submit' value='editera' name='editArticle' /><input type='submit' value='Ta bort' name='editArticle' /></td>
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
	<h1>Redigera artikel</h1>
	
EOD;
	

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Editera artikel";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>