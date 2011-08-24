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
*	Description: 404 page
*
************************************************************************************************/

	$imageLink = WS_SITELINK . "images/404.png";
	$message = <<< EOD

		<!Doctype html>
		<head><title>404-PAGE NOT FOUND</title></head>
		<body style='background-color:#000000; text-align:center; margin: 0px; padding-top:50px;'>
		
		<img src='{$imageLink}' alt=404 />
		<h3 style='font-size:50px; color:#666666; font-family:arial, sans-serif;'>Page not found. Sorry!</h3>
		<a href='?m=core&amp;p=home' style='color:#3259c2; font-size:14px; font-family: helvetica, arial, verdana sans-serif; text-decoration:none;'>Find another one</a>
		</body>
EOD;

echo $message;
?>