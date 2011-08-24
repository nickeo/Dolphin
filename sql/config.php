<?php
/*********************************************************************************************
*
*	Description: defining sitespecifik variables and constants
*
*	Author: Niklas Odén
*
***********************************************************************************************/


//----------------------------------------------------------------------------------------------
//
//	
//
//	
//

define('DBT_User', DB_PREFIX . "User");
define('DBT_Group', DB_PREFIX . "Group");
define('DBT_GroupMember', DB_PREFIX . "GroupMember");

define('DBT_Article', DB_PREFIX . "Article");
define('DBT_Statistics', DB_PREFIX . "Statistics");
define('DBT_Topic', DB_PREFIX . "Topic");
define('DBT_Top2Pos', DB_PREFIX . "Top2Pos");
define('DBT_Post', DB_PREFIX . "Post");
define('DBT_Information', DB_PREFIX . "Information");

define('DBT_File', DB_PREFIX . "File");
define('DBT_Attachment', DB_PREFIX . "Attachment");
define('DBSP_PInsertFile', DB_PREFIX . "PInsertFile");
define('DBSP_PListFiles', DB_PREFIX . "PListFiles");
define('DBSP_PGetFileDetails', DB_PREFIX . "PGetFileDetails");
define('DBSP_PUpdateOrDeleteFile', DB_PREFIX . "PUpdateOrDeleteFile");
define('DBSP_PAttachFile', DB_PREFIX . "PAttachFile");
define('DBSP_PListTrash', DB_PREFIX . "PListTrash");
define('DBSP_PGetTopicAndAttachment', DB_PREFIX . "PGetTopicAndAttachment");
define('DBSP_PGetTopic', DB_PREFIX . "PGetTopic");
define('DBSP_PGetAttachment', DB_PREFIX . "PGetAttachment");
define('DBSP_PAttachableFiles', DB_PREFIX . "PAttachableFiles");
define('DBSP_PAttachedFiles', DB_PREFIX . "PAttachedFiles");


define('DBSP_PCreateOrUpdateTopic', DB_PREFIX . "PCreateOrUpdateTopic");
define('DBSP_PShowTopics', DB_PREFIX . "PShowTopics");
define('DBSP_PDeletePost', DB_PREFIX . "PDeletePost");
define('DBSP_PDisplayTopic', DB_PREFIX . "PDisplayTopic");
define('DBSP_PDisplayPosts', DB_PREFIX . "PDisplayPosts");
define('DBSP_PDisplayTopicAndPosts', DB_PREFIX . "PDisplayTopicAndPosts");

define('DBSP_PCreateAccount' , DB_PREFIX . "PCreateAccount");
define('DBSP_PAuthenticateUser' , DB_PREFIX . "PAuthenticateUser");
define('DBSP_PGetAccountInfo', DB_PREFIX . "PGetAccountInfo");

define('DBSP_PUpdatePassword', DB_PREFIX . "PUpdatePassword");
define('DBSP_PUpdateEmail', DB_PREFIX . "PUpdateEmail");
define('DBSP_PUpdateAvatar', DB_PREFIX . "PUpdateAvatar");
define('DBSP_PUpdateGravatar', DB_PREFIX . "PUpdateGravatar");

define('DBSP_PAdminListFiles', DB_PREFIX . "PAdminListFiles");
define('DBSP_PAdminListAccounts', DB_PREFIX . "PAdminListAccounts");


define('DBF_FGetGravatarLink' , DB_PREFIX . "FGetGravatarLink");
define('DBF_FCreatePassword' , DB_PREFIX . "FCreatePassword");
define('DBF_FCheckUserIsAdmin' , DB_PREFIX . "FCheckUserIsAdmin");
define('DBF_FCheckFilePermission' , DB_PREFIX . "FCheckFilePermission");

define('DBTR_TRemoveAttachment' , DB_PREFIX . "TRemoveAttachment");

define('DBSP_PCreateNewArticle', DB_PREFIX . "PCreateNewArticle");
define('DBSP_PUpdateArticle', DB_PREFIX . "PUpdateArticle");
define('DBSP_PDisplayArticle', DB_PREFIX . "PDisplayArticle");
define('DBSP_PListArticles', DB_PREFIX . "PListArticles");
define('DBF_FCheckUserIsAdminOrOwner', DB_PREFIX . "FCheckUserIsAdminOrOwner");
define('DBF_FGetGroup', DB_PREFIX . "FGetGroup");


?>