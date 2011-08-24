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
*	Description: 403 page
*
*
*************************************************************************************************/
	$imageLink = WS_SITELINK . "images/404.png";
	$message = <<< EOD

		<!Doctype html>
		<head><title>403-FORBIDDEN</title></head>
		<body style='background-color:#000000; overflow:hidden; margin:0px; padding:0px;'>
		<div style='width:102%; margin:-30px;'>
		<h1 style='font-size:48px; color:#ffffff; font-family: arial, verdana, helvetica, sans-serif; line-height:45px; letter-spacing:-4px;'>
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			<a href='?m=core&amp;p=home' style='color:red; text-decoration:none;'>TAKE ME AWAY!</a>
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			403 FORBIDDEN - YOU DON'T HAVE PERMISSION TO ACCESS THIS PAGE!
			
		
		</h1>
		</div>
		</body>
EOD;

echo $message;
?>