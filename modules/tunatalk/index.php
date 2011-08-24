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
*	Module frontcontroller handling TunaTalk-forum for Dolphin
*
*
*******************************************************************************************************/

global $gPage;

// -------------------------------------------------------------------------------------------
//
// Redirect to the choosen pagecontroller.
//
$currentDir = dirname(__FILE__) . '/';

switch($gPage) {

    case 'tunatalk' :        require_once(TP_TUNATALKPATH . 'PIndexTunatalk.php'); break;
    
    case 'template' :        require_once(TP_PAGESPATH . 'home/PTemplate.php'); break;
    
    case 'login' :     require_once(TP_PAGESPATH . 'login/PLogin.php'); break;
    
    case 'loginp' :    require_once(TP_PAGESPATH . 'login/PLoginProcess.php'); break;
    
    case 'logout' :        require_once(TP_PAGESPATH . 'login/PLogout.php'); break;
    
    case 'view' :        require_once(TP_PAGESPATH . 'viewfiles/PListDirectory.php'); break;
    
    case 'showtopic' :        require_once(TP_TUNATALKPATH . 'PShowTopic.php'); break;
    
    case 'topics' :        require_once(TP_TUNATALKPATH . 'PTopics.php'); break;
    
    case 'newtopic' :        require_once(TP_TUNATALKPATH . 'PEditMessage.php'); break;
    
    //case 'newtopic' :        require_once(TP_TUNATALKPATH . 'PDebug.php'); break;
    
    case 'newtopicp' :        require_once(TP_TUNATALKPATH . 'PNewMessageProcess.php'); break;
    
    case 'edittopic' :        require_once(TP_TUNATALKPATH . 'PEditMessage.php'); break;
    
    case 'deletep' :        require_once(TP_TUNATALKPATH . 'PDeleteMessageProcess.php'); break;
   
    default :                 require_once(TP_TUNATALKPATH . 'PIndexTunatalk.php'); break;
}


?>