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
*********************************************************************************************/


error_reporting(E_ALL);
require_once(TP_SQLPATH . 'config.php');

// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//

// -------------------------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//




$queryCode = "install";
$queryCodeArticle = "articleTable";
$queryCodeProcedures = "installProcedures";
$queryCodeTemporary = "tempInstall";
$queryCodeTunaTalk = "tunatalk";
$queryCodeFilesArchive = "filesArchive";

$db = new CDatabaseController();

$mysqli = $db->connectToDatabase();
$res = $db->performMultiQuery($queryCode);
$query = $db->loadQuery($queryCode);
$mysqli->close();
/*
$mysqli = $db->connectToDatabase();
$res = $db->performMultiQuery($queryCodeArticle);
$query2 = $db->loadQuery($queryCodeArticle);
$mysqli->close();

$mysqli = $db->connectToDatabase();
$res = $db->performMultiQuery($queryCodeProcedures);
$query3 = $db->loadQuery($queryCodeProcedures);
$mysqli->close();

$mysqli = $db->connectToDatabase();
$res = $db->performMultiQuery($queryCodeTemporary);
$query4 = $db->loadQuery($queryCodeTemporary);
$mysqli->close();

$mysqli = $db->connectToDatabase();
$res = $db->performMultiQuery($queryCodeTunaTalk);
$query5 = $db->loadQuery($queryCodeTunaTalk);
$mysqli->close();

$mysqli = $db->connectToDatabase();
$res = $db->performMultiQuery($queryCodeFilesArchive);
$query6 = $db->loadQuery($queryCodeFilesArchive);
$mysqli->close();
*/
//$query =  "<p class='alert'>Disabled</p>";
$query2 = "<p class='alert'>Disabled</p>";
$query3 = "<p class='alert'>Disabled</p>";
$query4 = "<p class='alert'>Disabled</p>";
$query5 = "<p class='alert'>Disabled</p>";
$query6 = "<p class='alert'>Disabled</p>";

//--------------------------------------------------------------------------------------------
//<p>Statements that succeeded: {$no}</p>
//<p>Error code: {$mysqli->errno} ({$mysqli->error})</p>
//	contents
//<pre>{$query}<br />{$query2}</pre>

$centerBody = <<< EOD
<h1>Databas installerad</h1>
<br />
<p>
SQL Queryn uppdelad i fem delar. Endast en del installeras med nuvarande inställning.

<div>
<br />
<h2>Installerar tabeller för användare</h2>
<br />
<pre>{$query}</pre>
<br /><br />
<h2>Installerar tabeller för artiklar och statistik</h2>
<br />
<pre>{$query2}</pre>
<br /><br />
<h2>Installerar procedurer, funktioner och triggers</h2>
<br />
<pre>{$query3}</pre>
<br /><br />
<h2>Installerar temporära hjälpprocedurer</h2>
<br />
<pre>{$query4}</pre>
<br /><br />
<h2>Installerar tabeller och procedurer för modulen TunaTalk</h2>
<br />
<pre>{$query5}</pre>
<br /><br />
<h2>Installerar tabeller och procedurer för modulen Archive</h2>
<br />
<pre>{$query6}</pre>
</div>
</p>
<br />
<br />
EOD;

$subHeader = "";
$leftBody = "";

$rightBody = "";

//---------------------------------------------------------------------------------------------
//
//	printing out the page
//
$title = "Hem";
$layout = "";

require_once('src/CHTMLPage.php');
$page = new CHTMLPage();

$page->PrintPage($title, $subHeader, $leftBody, $centerBody, $rightBody);

?>