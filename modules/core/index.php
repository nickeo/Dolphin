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
*	Module frontcontroller handling core-pages for Dolphin
*
*
* **********************************************************************************************/

global $gPage;

// -------------------------------------------------------------------------------------------
//
// Redirect to the choosen pagecontroller.
//
$currentDir = dirname(__FILE__) . '/';

switch($gPage) {
	
	//----------------------------------------------------------------------------------------
	//
	//	home
	
    case 'home' :        require_once(TP_PAGESPATH . 'home/PHome.php'); break;
    
    case 'template' :        require_once(TP_PAGESPATH . 'home/PTemplate.php'); break;
    
    case 'cleanpage' :        require_once(TP_PAGESPATH . 'home/PPage.php'); break;
    
    case 'features' :        require_once(TP_PAGESPATH . 'home/PDolphinFeatures.php'); break;
    
    case 'installdolphin' :        require_once(TP_PAGESPATH . 'home/PDownloadDolphin.php'); break;
    
    //----------------------------------------------------------------------------------------
	//
	//	login/logout
    
    case 'login' :     require_once(TP_MODULEPATH . 'core/login/PLogin.php'); break;
    
    case 'loginp' :    require_once(TP_MODULEPATH . 'core/login/PLoginProcess.php'); break;
    
    case 'logout' :        require_once(TP_MODULEPATH . 'core/login/PLogout.php'); break;
    
    //----------------------------------------------------------------------------------------
	//
	//	accounts
    
    case 'createa' :     require_once(TP_MODULEPATH . 'core/account/PCreateAccount.php'); break;
    
    case 'createap' :     require_once(TP_MODULEPATH . 'core/account/PCreateAccountProcess.php'); break;
    
    //----------------------------------------------------------------------------------------
	//
	//	articles
    
    case 'newmessage' :        require_once(TP_PAGESPATH . 'app_spec_login/PNewMessage.php'); break;
    
    case 'newmessagep' :        require_once(TP_PAGESPATH . 'app_spec_login/PNewMessageProcess.php'); break;
    
    case 'editmessage' :        require_once(TP_PAGESPATH . 'app_spec_login/PEditMessage.php'); break;
    
    case 'editmessagep' :        require_once(TP_PAGESPATH . 'app_spec_login/PEditMessageProcess.php'); break;
    
    case 'listarticles' :        require_once(TP_PAGESPATH . 'app_spec_login/PListArticles.php'); break;
    
    case 'showmessage' :        require_once(TP_PAGESPATH . 'app_spec_login/PShowMessage.php'); break;
    
    case 'deletemessage' :        require_once(TP_PAGESPATH . 'app_spec_login/PDeleteMessageProcess.php'); break;
    
    case 'deletecomment' :        require_once(TP_PAGESPATH . 'app_spec_login/PDeleteCommentProcess.php'); break;
    
    case 'newcomment' :        require_once(TP_PAGESPATH . 'app_spec/PNewComment.php'); break;
    
    case 'newcommentp' :        require_once(TP_PAGESPATH . 'app_spec/PNewCommentProcess.php'); break;
    
    case 'message' :        require_once(TP_PAGESPATH . 'app_spec/PShowMessageComment.php'); break;
    
    case 'view' :        require_once(TP_PAGESPATH . 'viewfiles/PListDirectory.php'); break;
    
    case 'oldlinks' :        require_once(TP_EXAMINPATH . 'oldlinks.php'); break;
    
    case 'me' :        require_once(TP_MODULEPATH . 'me/me.php'); break;
    
    case 'showmodules' :        require_once(TP_MODULEPATH . 'core/app_spec/PShowModules.php'); break;
    

	//---------------------------------------------------------------------------
	//
	//	user control panel
	
	case 'usercontrol' :     require_once(TP_MODULEPATH . 'core/ucp/PUserControlPanel.php'); break;
	
	case 'accounts' :     require_once(TP_MODULEPATH . 'core/ucp/PAccountSettings.php'); break;
    
    case 'accountsp' :     require_once(TP_MODULEPATH . 'core/ucp/PAccountSettingsProcess.php'); break;
    
    case 'archive' :        require_once(TP_MODULEPATH . 'core/ucp/PFileArchive.php'); break;
    
    case 'details' :        require_once(TP_MODULEPATH . 'core/ucp/PFileDetails.php'); break;
    
    case 'editfile' :        require_once(TP_MODULEPATH . 'core/ucp/PFileEdit.php'); break;
    
    case 'editfilep' :        require_once(TP_MODULEPATH . 'core/ucp/PFileEditProcess.php'); break;
    
    case 'upload' :        require_once(TP_MODULEPATH . 'core/ucp/PFileUpload.php'); break;
    
    case 'uploadp' :        require_once(TP_MODULEPATH . 'core/ucp/PFileUploadProcess.php'); break;
    
    //---------------------------------------------------------------------------
	//
	//	admin control panel
    
    case 'admincontrol' :     require_once(TP_MODULEPATH . 'core/acp/PAdminControlPanel.php'); break;
    
    case 'listfiles' :     require_once(TP_MODULEPATH . 'core/acp/PFilesList.php'); break;
    
    case 'fileedit' :     require_once(TP_MODULEPATH . 'core/acp/PAdminFileEdit.php'); break;
    
    case 'fileeditp' :     require_once(TP_MODULEPATH . 'core/acp/PAdminFileEditProcess.php'); break;
    
    case 'listaccounts' :     require_once(TP_MODULEPATH . 'core/acp/PAccountList.php'); break;
    
    case 'createaccount' :     require_once(TP_MODULEPATH . 'core/acp/PAdminCreateAccount.php'); break;
    
    case 'createaccountp' :     require_once(TP_MODULEPATH . 'core/acp/PAdminCreateAccountProcess.php'); break;
    
    case 'editaccount' :     require_once(TP_MODULEPATH . 'core/acp/PAdminAccountSettings.php'); break;
    
    case 'editaccountp' :     require_once(TP_MODULEPATH . 'core/acp/PAdminAccountSettingsProcess.php'); break;
    
    case 'install' :     require_once(TP_MODULEPATH . 'core/acp/PAdminInstall.php'); break;
    
    case 'installp' :     require_once(TP_MODULEPATH . 'core/acp/PAdminInstallProcess.php'); break;
    
    //---------------------------------------------------------------------------
	//
	//	file
	
	case 'download' :        require_once(TP_MODULEPATH . 'core/file/PFileDownload.php'); break;
    
    case 'downloadp' :        require_once(TP_MODULEPATH . 'core/file/PFileDownloadProcess.php'); break;
    
    case 'attachfile' :        require_once(TP_MODULEPATH . 'core/file/PFileAttach.php'); break;
    
    case 'attachfilep' :        require_once(TP_MODULEPATH . 'core/file/PFileAttachProcess.php'); break;
    
    //----------------------------------------------------------------------------------------
	//
	//	errorMessage
	
	case '403' :        require_once(TP_MODULEPATH . 'core/home/P403.php'); break;
	
	case '404' :        require_once(TP_MODULEPATH . 'core/home/P404.php'); break;
	
    default :                 require_once(TP_PAGESPATH . 'home/P404.php'); break;
}


?>