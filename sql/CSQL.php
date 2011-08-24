<?php
/*********************************************************************************************
*
*	Description: class handling sql queries for Dolphin, generating sql to be used by
*				 
*				 CDatabaseController()
*				
*
*	Author: Niklas Odén
*
***********************************************************************************************/

/*==============================================================================================

	TABLE OF CONTENTS
	-----------------
	
	CORE INSTALLATION
	
		=> function installDB
			+ tables
				user - information about users
				group - information about different groups available in dolphin
				groupmember - connecting user with group/rights
			+ functions
				CheckUserIsAdmin - check if current user is admin
				CreatePassword - generating password => hash(pwd + salt)
				GetGravatarLink - creating link using user email
			+ stored procedures
				CreateAccount -	
				AuthenticateUser -
				GetAccountInfo -
				UpdatePassword -
				UpdateEmail -
				UpdateAvatar -
				UpdateGravatar -
			
			+ Default values
				populating user and grouptables with one default admin user
	
		=> function installFiles
			+ tables
				files - information about files
			+ functions
				CheckFilePermission -
			+ stored procedures
				InsertFile -
				ListFiles -
				GetFileDetails -
				UpdateOrDeleteFile -
				ListTrash -
				AdminListFiles -
				AdminListAccounts -
				
		=> function installArticles
+ tables
	articles - info about articles, ie title, text, date ...

+ functions
	CheckIfUserIsAdminOrOwner -
	GetGroup -
	
+ stored procedures
	CreateNewArticle -
	UpdateArticle -
	DisplayArticle -
	ListArticles -
	
	TUNATALK INSTALLATION
	
		=> function installTunatalk
			+ tables
				topic -	stores actual topic ie title and text
				information - stores information about the topic
				attachment - connecting files and topics
			+ stored procedures
				CreateOrUpdateTopic -
				DisplayTopic -
				ShowTopics -
				DeletePost -
				DisplayPosts -
				DisplayTopicAndPosts -
				GetTopic -
				GetAttachment -
				GetTopicAndAttachment -
				AttachFile -
			+ triggers
				RemoveAttachment -

================================================================================================*/	
	
	require_once(TP_SQLPATH . 'config.php');
	
class CSQL {

	//-----------------------------------------------------------------------------------------
	//
	//	member variables
	//
	
	protected $iMysqli;
	
	//-----------------------------------------------------------------------------------------
	//
	//	constructor and destructor
	//
	
	function __construct() {
	
		
	
	}
	
	function __destruct() {
	
	}
	
	
	//**********************************************************************************************
	//
	//	DOLPHIN - CORE
	//
	//	retrieving sql for core-installation of dolphin
	//
	//
	//**********************************************************************************************
	
	
	public function installDB() {
		
		$tblUser = DBT_User;
		$tblGroup = DBT_Group;
		$tblGM = DBT_GroupMember;
		$pwdHash = DB_PASSWORDHASHING;
		
		$spCreateAccount = DBSP_PCreateAccount;
		$spAuthenticateUser = DBSP_PAuthenticateUser;
		$spGetAccountInfo = DBSP_PGetAccountInfo;
	
		$spUpdatePassword = DBSP_PUpdatePassword;
		$spUpdateEmail = DBSP_PUpdateEmail;
		$spUpdateAvatar = DBSP_PUpdateAvatar;
		$spUpdateGravatar = DBSP_PUpdateGravatar;
		
		$fCheckUserIsAdmin = DBF_FCheckUserIsAdmin;
		$fGetGravatarLink = DBF_FGetGravatarLink;
		$fCreatePassword = DBF_FCreatePassword;
	
		$imageLink = WS_IMAGES;
		
	//-----------------------------------------------------------------------------------------
	//
	//	tables user, group and groupmember
	//
		
		$query = <<< EOD
		DROP TABLE IF EXISTS {$tblUser};
		DROP TABLE IF EXISTS {$tblGroup};
		DROP TABLE IF EXISTS {$tblGM};

		--
		-- Table for the User
		--
		CREATE TABLE {$tblUser} (

 		 -- Primary key(s)
  		idUser INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

  		-- Attributes
  		accountUser CHAR(20) NOT NULL UNIQUE,
 	 	emailUser VARCHAR(80) NULL UNIQUE,
 	 	
 	 	-- Attributes related to password
  		passwordUser VARBINARY(40) NOT NULL,
  		saltUser BINARY(10) NOT NULL,
  		methodUser CHAR(5) NOT NULL,
  		key3User BINARY(32) NULL UNIQUE,
  		expireUser DATETIME NULL,
  		
  		-- Attributes related to user profile info
  		avatarUser VARCHAR(100) NULL,
  		gravatarUser VARCHAR(100) NULL
		);


		--
		-- Table for the Group
		--
		CREATE TABLE {$tblGroup} (

  		-- Primary key(s)
  		idGroup CHAR(3) NOT NULL PRIMARY KEY,

 		 -- Attributes
 		 nameGroup CHAR(40) NOT NULL
		);


		--
		-- Table for the GroupMember
		--
		CREATE TABLE {$tblGM} (

 		 -- Primary key(s)
  		--
  		-- The PK is the combination of the two foreign keys, see below.
		--
  
  		-- Foreign keys
  		GroupMember_idUser INT NOT NULL,
  		GroupMember_idGroup CHAR(3) NOT NULL,
    
  		FOREIGN KEY (GroupMember_idUser) REFERENCES {$tblUser}(idUser),
  		FOREIGN KEY (GroupMember_idGroup) REFERENCES {$tblGroup}(idGroup),

  		PRIMARY KEY (GroupMember_idUser, GroupMember_idGroup)
  
  		-- Attributes

		);

		
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	function controlling if current user is admin
	//
	
		$query .= <<< EOD
		DROP FUNCTION IF EXISTS {$fCheckUserIsAdmin};
		
		CREATE FUNCTION {$fCheckUserIsAdmin}
		(
			fIdUser INT
		)
		RETURNS BOOLEAN
		BEGIN
			DECLARE isAdmin INT;
			
			SELECT idUser INTO isAdmin
			FROM {$tblUser} AS U
				INNER JOIN {$tblGM} AS GM
					ON U.idUser = GM.GroupMember_idUser
				INNER JOIN {$tblGroup} AS G
					ON G.idGroup = GM.GroupMember_idGroup
			WHERE
				idUser = fIdUser
			AND
				idGroup = 'adm';
			RETURN (isAdmin OR 0);
		END;
EOD;
		
	//-----------------------------------------------------------------------------------------------
	//
	//	function creating password
	//	
		$query .= <<< EOD
			DROP FUNCTION IF EXISTS {$fCreatePassword};
			CREATE FUNCTION {$fCreatePassword}
			(
				fSalt BINARY(10),
				fPassword CHAR(32),
				fMethod CHAR(5)
			)
			RETURNS VARBINARY(40)
			BEGIN
				DECLARE password VARBINARY(40);
				
				CASE TRIM(fMethod)
					WHEN 'MD5' THEN SELECT md5(CONCAT(fSalt, fPassword)) INTO password;
					WHEN 'SHA-1' THEN SELECT sha1(CONCAT(fSalt, fPassword)) INTO password;
					WHEN 'PLAIN' THEN SELECT fPassword INTO password;
				END CASE;
				
				RETURN password;
				
			END;
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	function creating gravatarlink from user email
	//
	
		$query .= <<< EOD
		DROP FUNCTION IF EXISTS {$fGetGravatarLink};
		
		CREATE FUNCTION {$fGetGravatarLink}
		(
			fEmail VARCHAR(80),
			fSize INT
		)
		RETURNS VARCHAR(80)
		BEGIN
			DECLARE fLink VARCHAR(80);
			
			SELECT CONCAT('http://www.gravatar.com/avatar/', MD5(LOWER(fEmail)), '.jpg?s=', fSize)
			INTO fLink;
			
			RETURN fLink;
		END;
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - creating new account
	//
	
		$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spCreateAccount};
		
		CREATE PROCEDURE {$spCreateAccount}(
			OUT pUserId INT,
			IN pAccountUser CHAR(20),
			IN pPassword VARCHAR(80),
			IN pMethod CHAR(5),
			OUT pStatus INT
		)
		BEGIN
		
			DECLARE salt BINARY(10);
		
			SELECT idUser INTO pUserId FROM {$tblUser} WHERE accountUser = pAccountUser;
			
			IF pUserId IS NOT NULL THEN
			BEGIN
				SET pStatus = 1;  -- Failure, the name exists
			END;
			ELSE
			BEGIN
			
				SELECT BINARY(UNIX_TIMESTAMP(NOW())) INTO salt;
			
				INSERT INTO {$tblUser}
					(accountUser, saltUser, passwordUser, methodUser, avatarUser)
				VALUES
					(pAccountUser, salt, {$fCreatePassword}(salt, pPassword, pMethod), pMethod, 'http://www.student.bth.se/~niod09/dbwebb2/development/dolphin/images/userblue.png');
					
				SET pUserId = LAST_INSERT_ID();
				INSERT INTO {$tblGM}
					(Groupmember_idUser, Groupmember_idGroup)
				VALUES(pUserId, 'usr');
				SET pStatus = 0;
			END;
			END IF;
			
		END;
	
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - authenticate user
	//
	
	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spAuthenticateUser};
		
		CREATE PROCEDURE {$spAuthenticateUser}(
			IN pUserAccountOrEmail VARCHAR(80),
			IN pPassword VARCHAR(80),
			OUT pUserId INT,
			OUT pStatus INT
		)
		BEGIN
			SELECT idUser INTO pUserId FROM {$tblUser}
			WHERE
				(
				accountUser = pUserAccountOrEmail
			AND
				passwordUser = {$fCreatePassword}(saltUser, pPassword, methodUser)
				)
			OR
				(
				emailUser = pUserAccountOrEmail
			AND
				passwordUser = {$fCreatePassword}(saltUser, pPassword, methodUser)
				)
			;
			IF pUserId IS NULL THEN
				SET pStatus = 1;
			ELSE
				SET pStatus = 0;
			END IF;
		END;
	
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - get info about a specific account
	//
	
	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spGetAccountInfo};
		
		CREATE PROCEDURE {$spGetAccountInfo}(
			IN pUserId INT
			)
		BEGIN
			SELECT 
				U.idUser AS id, 
				U.accountUser AS account, 
				U.emailUser AS email, 
				U.avatarUser AS avatar,
				U.gravatarUser AS gravatar,
				{$fGetGravatarLink}(U.gravatarUser, 80) AS gravatarLink,
				G.Groupmember_idGroup AS groupId
			FROM {$tblUser} AS U
			INNER JOIN {$tblGM} AS G
			ON U.idUser = G.Groupmember_idUser
			WHERE
				U.idUser = pUserId;
		END;	
	
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - update password
	//
	
	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spUpdatePassword};
		
		CREATE PROCEDURE {$spUpdatePassword}(
			IN pUserId INT,
			IN pPassword VARCHAR(60)
		)
		BEGIN
			UPDATE {$tblUser} SET
				saltUser = BINARY(UNIX_TIMESTAMP(NOW())),
				passwordUser = {$fCreatePassword}(saltUser, pPassword, methodUser)
			WHERE
				idUser = pUserId
			LIMIT 1;
		END;
	
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - update email
	//
	
	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spUpdateEmail};
		
		CREATE PROCEDURE {$spUpdateEmail}(
			IN pUserId INT,
			IN pEmail VARCHAR(80),
			OUT pRowsAffected INT
		)
		BEGIN
			UPDATE {$tblUser} SET
				emailUser = pEmail
			WHERE
				idUser = pUserId
			LIMIT 1;
			SELECT ROW_COUNT() INTO pRowsAffected;
		END;
	
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - update avatar
	//
	
	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spUpdateAvatar};
		
		CREATE PROCEDURE {$spUpdateAvatar}(
			IN pUserId INT,
			IN pAvatar VARCHAR(100)
		)
		BEGIN
			UPDATE {$tblUser} SET
				avatarUser = pAvatar
			WHERE
				idUser = pUserId
			LIMIT 1;
		END;
	
EOD;

	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - update gravataar
	//
	
	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spUpdateGravatar};
		
		CREATE PROCEDURE {$spUpdateGravatar}(
			IN pUserId INT,
			IN pGravatar VARCHAR(100)
		)
		BEGIN
			UPDATE {$tblUser} SET
				gravatarUser = TRIM(pGravatar)
			WHERE
				idUser = pUserId
			LIMIT 1;
		END;
	
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	default - populating user/group tables with default admin user
	//
	
	$account = 'dolphin';
	$password = 'secret';
	$mail = 'nicke@nymusik.net';
	$avatar = $imageLink . "userblue.png";
	
		$query .= <<< EOD
			CALL {$spCreateAccount}(@pUserId, '{$account}', '{$password}', '{$pwdHash}', @pStatus);
			CALL {$spUpdateEmail}(1, '{$mail}', @pRowsAffected);
			CALL {$spUpdateAvatar}(1, '{$avatar}');
			INSERT INTO {$tblGM} (GroupMember_idUser, GroupMember_idGroup)
			VALUES (1, 'adm');
			INSERT INTO {$tblGroup} (idGroup, nameGroup)
			VALUES ('adm', 'Administrators');
			INSERT INTO {$tblGroup} (idGroup, nameGroup)
			VALUES ('usr', 'Regular users');
EOD;

		
		return $query;
		

	}
	
//==================================================================================================
//
//	CORE INSTALLATION part 2 -----  sql for file
//
//==================================================================================================
	
	//-----------------------------------------------------------------------------------------------
	//
	//	installing tables and procedures for handling files/archive
	//
	
	public function installFile() {
	
	$tblGroup = DBT_Group;
	$tblGM = DBT_GroupMember;
	$tblUser = DBT_User;
	$tblFile = DBT_File;
	$tblTopic = DBT_Topic;
	$tblAttachment = DBT_Attachment;
	$spInsertFile = DBSP_PInsertFile;
	$spListFiles = DBSP_PListFiles;
	$spGetFileDetails = DBSP_PGetFileDetails;
	$spUpdateOrDeleteFile = DBSP_PUpdateOrDeleteFile;
	$spAttachFile = DBSP_PAttachFile;
	$spListTrash = DBSP_PListTrash;
	$spGetTopicAndAttachment = DBSP_PGetTopicAndAttachment;
	$spGetTopic = DBSP_PGetTopic;
	$spGetAttachment = DBSP_PGetAttachment;
	$spAttachableFiles = DBSP_PAttachableFiles;
	$spAttachedFiles = DBSP_PAttachedFiles;
	
	$spAdminListFiles = DBSP_PAdminListFiles;
	$spAdminListAccounts = DBSP_PAdminListAccounts;
	
	$fCheckUserIsAdmin = DBF_FCheckUserIsAdmin;
	$fCheckFilePermission = DBF_FCheckFilePermission;
	
	$tRemoveAttachment = DBTR_TRemoveAttachment;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	table - handling files
	//
	
	$query = <<< EOD
	
	DROP TABLE IF EXISTS {$tblFile};
	
	CREATE TABLE {$tblFile}(
		
		-- Primary key
		idFile INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
		
		-- Foreign key
		file_idUser INT UNSIGNED NOT NULL,
		FOREIGN KEY (file_idUser) REFERENCES {$tblUser}(idUser),
		
		-- Attributes
		nameFile VARCHAR(80) NOT NULL,
		pathFile VARCHAR(160) NOT NULL,
		uniqueNameFile VARCHAR(60) NOT NULL UNIQUE,
		sizeFile INT UNSIGNED NOT NULL,
		mimetypeFile VARCHAR(127) NOT NULL,
		createdFile DATETIME NOT NULL,
		modifiedFile DATETIME NULL,
		deletedFile DATETIME NULL
	);

EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - inserting info about new file in db
	//
	
	$query .= <<< EOD
	DROP PROCEDURE IF EXISTS {$spInsertFile};
	
	CREATE PROCEDURE {$spInsertFile}
	(
		IN pIdUser INT UNSIGNED,
		IN pNameFile VARCHAR(80),
		IN pUniqueNameFile VARCHAR(60),
		IN pPathFile VARCHAR(160),
		IN pSizeFile INT UNSIGNED,
		IN pMimetypeFile VARCHAR(127)
	)
	BEGIN
		
		INSERT INTO {$tblFile}
			(file_idUser, nameFile, uniqueNameFile, pathFile, sizeFile, mimetypeFile, createdFile)
		VALUES
			(pIdUser, pNameFile, pUniqueNameFile, pPathFile, pSizeFile, pMimetypeFile, NOW());
	END;
	
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - get info about current users files in db
	//
	
	$query .= <<< EOD
	DROP PROCEDURE IF EXISTS {$spListFiles};
	
	CREATE PROCEDURE {$spListFiles}
	(
		IN pIdUser INT UNSIGNED
	)
	BEGIN
		SELECT
			idFile,
			nameFile AS name,
			uniqueNameFile AS uniqueName,
			pathFile AS path,
			sizeFile AS size,
			mimetypeFile AS mimetype,
			createdFile AS created,
			modifiedFile AS modified,
			deletedFile AS deleted
		FROM
			{$tblFile}
		WHERE 
			file_idUser = pIdUser
		AND
			deletedFile IS NULL;
	END;
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - checking if file exists and if user has the rights to edit
	//
	
	
	$query .= <<< EOD
		DROP FUNCTION IF EXISTS {$fCheckFilePermission};
	
		CREATE FUNCTION {$fCheckFilePermission}
		(
			fIdUser INT UNSIGNED,
			fIdFile INT UNSIGNED
		)
		RETURNS INT UNSIGNED
		BEGIN
			DECLARE fId INT UNSIGNED;
		
			SELECT idFile INTO fId
			FROM {$tblFile}
			WHERE
				idFile = fIdFile
			AND
				({$fCheckUserIsAdmin}(fIdUser)
				OR
				file_idUser = fIdUser);
			IF fId IS NOT NULL THEN
			RETURN 0;
			END IF;

			-- Does file exists?
			SELECT idFile INTO fId FROM {$tblFile} WHERE idFile = fIdFile;
			IF fId IS NULL THEN
			RETURN 2;
			END IF;

			-- So, file exists but user has no permissions to use/update file.
			RETURN 1;
		END;
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - get information about file
	//
	
	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spGetFileDetails};
		
		CREATE PROCEDURE {$spGetFileDetails}
		(
			IN pIdUser INT UNSIGNED,
			IN pUniqueNameFile VARCHAR(60),
			OUT pSuccess TINYINT UNSIGNED 
		)
		BEGIN
			DECLARE fileId INT UNSIGNED;
		-- Get the id of the file
		SELECT idFile INTO fileId FROM {$tblFile}
		WHERE
		uniqueNameFile = pUniqueNameFile;

		-- Check permissions
		SELECT {$fCheckFilePermission}(pIdUser, fileId) INTO pSuccess;
		-- Get details from file
		SELECT
			idFile AS fileId,
			file_idUser AS userId,
			U.accountUser AS owner,
			nameFile AS name,
			uniqueNameFile AS uniqueName,
			pathFile AS path,
			sizeFile AS size,
			mimetypeFile AS mimetype,
			createdFile AS created,
			modifiedFile AS modified,
			deletedFile AS deleted
		FROM {$tblFile} AS F
		INNER JOIN {$tblUser} AS U
			ON F.file_idUser = U.idUser
		WHERE
			uniqueNameFile = pUniqueNameFile;
		END;
EOD;
	
	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spUpdateOrDeleteFile};
		
		CREATE PROCEDURE {$spUpdateOrDeleteFile}
		(
			IN pIdUser INT UNSIGNED,
			IN pUniqueNameFile VARCHAR(60),
			IN pNameFile VARCHAR(80),
			IN pMimetypeFile VARCHAR(127),
			IN pUpdateOrDelete CHAR(7),
			OUT pSuccess TINYINT UNSIGNED
		)
		BEGIN
			DECLARE fileId INT UNSIGNED;
			-- Get the id of the file
			SELECT idFile INTO fileId FROM {$tblFile}
			WHERE
			uniqueNameFile = pUniqueNameFile;

			-- Check permissions
			SELECT {$fCheckFilePermission}(pIdUser, fileId) INTO pSuccess;
			IF pSuccess = 0 THEN
				BEGIN
				IF pUpdateOrDelete = 'edit' THEN
				BEGIN
					UPDATE {$tblFile} SET
						nameFile = pNameFile,
						mimetypeFile = pMimetypeFile,
						modifiedFile = NOW()
					WHERE
						uniqueNameFile = pUniqueNameFile;
				END;
				--
				-- pseudodelete file, ie mark it as deleted but keep info in db
				--
				ELSEIF pUpdateOrDelete = 'trash' THEN
				BEGIN
					UPDATE {$tblFile} SET
						deletedFile = NOW()
					WHERE
						uniqueNameFile = pUniqueNameFile;
				END;
				--
				-- deleting the file from db
				--
				ELSEIF pUpdateOrDelete = 'delete' THEN
				BEGIN
					DELETE FROM {$tblFile}
					WHERE
						uniqueNameFile = pUniqueNameFile
					LIMIT 1;
				END;
				--
				--	pseudorecover ie bring back from trashcan
				--
				ELSEIF pUpdateOrDelete = 'recover' THEN
				BEGIN
					UPDATE {$tblFile} SET
						deletedFile = NULL
					WHERE
						uniqueNameFile = pUniqueNameFile;
				END;
				END IF;
			END;
			END IF;
		END;
EOD;

	
	
	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spListTrash};
	
		CREATE PROCEDURE {$spListTrash}
		(
			IN pIdUser INT UNSIGNED
		)
		BEGIN
			SELECT
				nameFile AS name,
				uniqueNameFile AS uniqueName,
				pathFile AS path,
				sizeFile AS size,
				mimetypeFile AS mimetype,
				createdFile AS created,
				modifiedFile AS modified,
				deletedFile AS deleted
			FROM
				{$tblFile}
			WHERE 
				file_idUser = pIdUser
			AND
				deletedFile IS NOT NULL;
		END;
EOD;

	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spAdminListFiles};
		
		CREATE PROCEDURE {$spAdminListFiles}
		(
			OUT pTotalSize INT,
			OUT pTotalFiles INT
		)
		BEGIN
			SELECT
				F.idFile,
				F.nameFile AS name,
				F.uniqueNameFile AS uniqueName,
				F.pathFile AS path,
				F.sizeFile AS size,
				F.mimetypeFile AS mimetype,
				F.createdFile AS created,
				F.modifiedFile AS modified,
				F.deletedFile AS deleted,
				U.accountUser As user,
				U.idUser
			FROM
				{$tblFile} AS F
			INNER JOIN {$tblUser} AS U
			ON
				U.idUser = F.file_idUser;
			SELECT SUM(sizeFile) INTO pTotalSize FROM {$tblFile};
			SELECT COUNT(*) INTO pTotalFiles FROM {$tblFile};

		END;
EOD;

		
	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spAdminListAccounts};
		
		CREATE PROCEDURE {$spAdminListAccounts}
		(
			
		)
		BEGIN
			SELECT
				U.idUser AS id, 
				U.accountUser AS account, 
				U.emailUser AS email, 
				U.avatarUser AS avatar,
				U.gravatarUser AS gravatar,
				G.Groupmember_idGroup AS groupId
			FROM {$tblUser} AS U
			INNER JOIN {$tblGM} AS G
			ON U.idUser = G.Groupmember_idUser;
		END;
EOD;
	
		return $query;
	}
	
	//-----------------------------------------------------------------------------------------
	//
	//	retrieving sql query
	//
	
	public function installArticleTable() {
		
		$tblArticle = DBT_Article;
		$tblStatistics = DBT_Statistics;
		$tblUser = DBT_User;
		
		$query = <<< EOD
			DROP TABLE IF EXISTS {$tblArticle};
			
			CREATE TABLE {$tblArticle} (

 		 	-- Primary key
  			id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
			
			-- Foreign keys
  			articleIdUser INT NOT NULL,
  			FOREIGN KEY (articleIdUser) REFERENCES {$tblUser}(idUser),
			
  			-- Attributes
  			articleTitle CHAR(45) NOT NULL,
 	 		articleText BLOB NOT NULL,
 	 		articleAuthor CHAR(30) NOT NULL,
  			articleDate DATETIME NOT NULL,
  			articleModifyDate DATETIME NULL
		);
EOD;

		$query .= <<< EOD
			DROP TABLE IF EXISTS {$tblStatistics};
			
			CREATE TABLE {$tblStatistics} (
				
				-- Primary key and foreign key
				userId INT NOT NULL PRIMARY KEY,
				FOREIGN KEY (userId) REFERENCES {$tblUser}(idUser),
				
				-- Attributes
				noOfArticles INT NOT NULL
			);
			
			--
			-- Add default users
			--
			INSERT INTO {$tblStatistics} (userId, noOfArticles)
			VALUES (1, 0);
			INSERT INTO {$tblStatistics} (userId, noOfArticles)
			VALUES (2, 0);
			INSERT INTO {$tblStatistics} (userId, noOfArticles)
			VALUES (3, 0);
EOD;
		return $query;
	}
	
	//-----------------------------------------------------------------------------------------------
	//
	//	installprocedures
	//
	
	public function installProcedures() {
		
		$tblArticle = DBT_Article;
		$tblUser = DBT_User;
		$tblGroup = DBT_Group;
		$tblGM = DBT_GroupMember;
		$tblStatistics = DBT_Statistics;
		
		$query = <<< EOD
		
			DROP PROCEDURE IF EXISTS PCreateNewArticle;
			
			CREATE PROCEDURE PCreateNewArticle(
				OUT pArticleId INT,
				IN title CHAR(45),
				IN text BLOB,
				IN userId INT)
			BEGIN
				INSERT INTO {$tblArticle}
					(articleIdUser, articleTitle, articleText, articleDate)
					VALUES 
					(userId, title, text, NOW());
				SET pArticleId = LAST_INSERT_ID();
			END;
EOD;

		$query .= <<< EOD
			DROP PROCEDURE IF EXISTS PUpdateArticle;
			
			CREATE PROCEDURE PUpdateArticle(
				IN editOrDelete INT,
				IN pArticleId INT,
				IN title CHAR(45),
				IN text BLOB,
				IN userId INT)
			BEGIN
				IF editOrDelete = 1 THEN
				BEGIN
					DELETE FROM {$tblArticle} 
					WHERE 
						id = pArticleId AND
						FCheckUserIsOwnerOrAdmin(pArticleId, userId)
					LIMIT 1;
				END;
				ELSE
				BEGIN
					UPDATE {$tblArticle}
					SET
						articleTitle = title,
						articleText = text,
						articleModifyDate = NOW()
					WHERE
						id = pArticleId AND 
						FCheckUserIsOwnerOrAdmin(pArticleId, userId)
					LIMIT 1;
				END;
				END IF;
			END;
		
EOD;
		
		$query .= <<< EOD
			DROP PROCEDURE IF EXISTS PDisplayArticle;
			
			CREATE PROCEDURE PDisplayArticle
				(
					OUT pGrantRights BOOLEAN,
					IN pArticleId INT,
					IN pUserId INT
				)
			BEGIN
				IF pArticleId = 0 THEN
				BEGIN
					SELECT * FROM {$tblArticle} AS A
					INNER JOIN {$tblUser} As U
					ON A.articleIdUser = U.idUser
					ORDER BY articleDate DESC
					LIMIT 1;
				END;
				ELSE
				BEGIN
					SELECT * FROM {$tblArticle} AS A
					INNER JOIN {$tblUser} As U
					ON A.articleIdUser = U.idUser
					WHERE id = pArticleId;
				END;
				END IF;
				SELECT FCheckUserIsOwnerOrAdmin(pArticleId, puserId) INTO pGrantRights;
			END;
				
		
EOD;
		
		$query .= <<< EOD
		
		DROP PROCEDURE IF EXISTS PListArticles;
		
		CREATE PROCEDURE PListArticles(
			IN userId INT,
			IN pAllOrCurrent CHAR(3))
		BEGIN
			IF pAllOrCurrent = "all" THEN
			BEGIN
				SELECT * FROM {$tblArticle} AS A
				INNER JOIN {$tblUser} AS U
				ON A.articleIdUser = U.idUser
				ORDER BY articleDate DESC;
			END;
			ELSE
			BEGIN
				SELECT * FROM {$tblArticle} AS A
				INNER JOIN {$tblUser} AS U
				ON A.articleIdUser = U.idUser
				WHERE 
				idUser = userId
				ORDER BY articleDate DESC;
			END;
			END IF;
		END;
		
EOD;
		
		
		
		$query .= <<< EOD
			DROP FUNCTION IF EXISTS FCheckUserIsOwnerOrAdmin;
			
			CREATE FUNCTION FCheckUserIsOwnerOrAdmin(fIdArticle INT, fIdUser INT)
			RETURNS BOOLEAN
			BEGIN
				DECLARE userId INT;
				DECLARE groupStatus CHAR(3);
				DECLARE editGrant BOOLEAN;
				SET editGrant = FALSE;
				
				SELECT FGetGroup(fidUser) INTO groupStatus;
				SELECT articleIdUser INTO userId FROM {$tblArticle}
				WHERE Id = fIdArticle;
				
				IF (groupStatus = 'adm' OR userId = fIdUser) THEN
				BEGIN
					SET editGrant = TRUE;
				END;
				END IF;
				
				return editGrant;
			END;
			
			
			
EOD;
//----------------------------------------------------------------alt. version
		/*$query = <<< EOD
			DROP FUNCTION IF EXISTS FCheckUserIsOwnerOrAdmin;
			
			CREATE FUNCTION FCheckUserIsOwnerOrAdmin(fIdArticle INT, fIdUser INT)
			RETURNS BOOLEAN
			BEGIN
				DECLARE userId INT;
				DECLARE groupStatus INT;
				DECLARE editGrant BOOLEAN;
				SET editGrant = FALSE;
				
				SELECT FMultiInfo('user', fIdUser, 'group') INTO groupStatus;
				SELECT FMultiInfo('article', fIdArticle, 'user') INTO userId;
				
				IF (groupStatus = 1 OR userId = fIdUser) THEN
				BEGIN
					SET editGrant = TRUE;
				END;
				END IF;
				
				return editGrant;
			END;
			
			
			
EOD;*/

//---------------------------------------------------------------------------------
		$query .= <<< EOD
			DROP FUNCTION IF EXISTS FGetGroup;
			
			CREATE FUNCTION FGetGroup(fIdUser INT)
			RETURNS CHAR(3)
			BEGIN
				DECLARE nOrG CHAR(3);
				BEGIN
					SELECT GroupMember_idGroup INTO nOrG FROM {$tblGM} AS G
					INNER JOIN {$tblUser} AS U 
					ON G.GroupMember_idUser = U.idUser
					WHERE idUser = fIdUser;
				END;
				RETURN nOrG;
			END;
		
EOD;

		$query .= <<< EOD
			
			DROP TRIGGER IF EXISTS TAddArticle;
			
			CREATE TRIGGER TAddArticle
			AFTER INSERT ON {$tblArticle}
			FOR EACH ROW
			BEGIN
				UPDATE {$tblStatistics}
				SET noOfArticles = noOfArticles + 1
				WHERE
				userId = NEW.articleIdUser;
			END;
			
EOD;

		$query .= <<< EOD
			
			DROP TRIGGER IF EXISTS TSubtractArticle;
			
			CREATE TRIGGER TSubtractArticle
			AFTER DELETE ON {$tblArticle}
			FOR EACH ROW
			BEGIN
				UPDATE {$tblStatistics}
				SET noOfArticles = noOfArticles - 1
				WHERE
				userId = OLD.articleIdUser;
			END;
			
EOD;

		$query .= <<< EOD
		
			DROP TRIGGER IF EXISTS TInsertUser;
			
			CREATE TRIGGER TInsertUser
			AFTER INSERT ON {$tblUser}
			FOR EACH ROW
			BEGIN
				INSERT INTO {$tblStatistics}
					(userId, noOfArticles)
				VALUES
					(NEW.idUser, 0);
			END;
		
EOD;

	
		
			/*$query = <<< EOD
				
				DROP FUNCTION IF EXISTS FMultiInfo;
				
				CREATE FUNCTION FMultiInfo(fSelectAOrU CHAR(8), fAOrUId INT, fSelectGOrU CHAR(5))
				RETURNS INT
				BEGIN
					DECLARE fInfo INT;
					DECLARE fUserId INT;
					DECLARE fGroupId CHAR(3);
					SET fInfo = 2;
					IF fSelectAOrU = 'article' THEN
					BEGIN
						IF fSelectGOrU = 'group' THEN
						BEGIN
							SELECT GroupMember_idGroup INTO fGroupId FROM {$tblGM} AS G
							INNER JOIN {$tblUser} AS U 
							ON G.GroupMember_idUser = U.idUser
							WHERE idUser = (SELECT articleIdUser FROM {$tblArticle}
							WHERE Id = fAOrUId);
							IF fGroupId = 'adm' THEN
							BEGIN
								SET fInfo = 1;
							END;
							END IF;
						END;
						ELSE
						BEGIN
							SELECT articleIdUser INTO fInfo FROM {$tblArticle}
							WHERE Id = fAOrUId;
						END;
						END IF;
					END;
					ELSE
					BEGIN
						SELECT GroupMember_idGroup INTO fGroupId FROM {$tblGM} AS G
						INNER JOIN {$tblUser} AS U 
						ON G.GroupMember_idUser = U.idUser
						WHERE idUser = fAOrUId;
						IF fGroupId = 'adm' THEN
						BEGIN
							SET fInfo = 1;
						END;
						END IF;
					END;
					END IF;
					
					RETURN fInfo;
				END;
EOD;*/
			return $query;
	}
	
	//-----------------------------------------------------------------------------------------------
	//
	//	install temp procedures
	//
	
	public function installTemp() {
		
		$tblArticle = DBT_Article;
		$tblUser = DBT_User;
		$tblGroup = DBT_Group;
		$tblGM = DBT_GroupMember;
		$tblStatistics = DBT_Statistics;
		
		$query = <<< EOD
		
			DROP PROCEDURE IF EXISTS PCheckTriggers;
			
			CREATE PROCEDURE PCheckTriggers()
			BEGIN
				SELECT * FROM {$tblStatistics} AS S
				INNER JOIN {$tblUser} AS U
				ON S.userId = U.idUser;
			END;
EOD;

		$query .= <<< EOD
			
			DROP PROCEDURE IF EXISTS PUpdateUsers;
			
			CREATE PROCEDURE PUpdateUsers
			(
			IN addOrDelete INT
			)
			BEGIN
				DECLARE totUser CHAR(20);
				DECLARE tempUser CHAR(20);
				SET tempUser = "MrTemporary";
				IF addOrDelete = 0 THEN
				BEGIN
					DELETE FROM {$tblUser}
					WHERE idUser > 3;
					DELETE FROM {$tblStatistics}
					WHERE userId > 3;
				END;
				ELSE
				BEGIN
					SET totUser = CONCAT(tempUser, (SELECT CAST((SELECT COUNT(idUser) FROM {$tblUser}) AS CHAR(8))));
					INSERT INTO {$tblUser} 
						(accountUser, emailUser, passwordUser)
					VALUES 
						(totUser, 'mrtemp@epost.se', md5('hemligt'));
				END;
				END IF;
			END;
EOD;
		return $query;
	}
	
	//-----------------------------------------------------------------------------------------------
	//
	//	login request
	//
	
	public function login() {
	
		$tblUser = DBT_User;
		$tblGroup = DBT_Group;
		$tblGM = DBT_GroupMember;
		
		
		global $username, $password;
		

		// Create the query
		$query = <<< EOD
		SELECT 
    		idUser AS id, 
   		 accountUser AS account,
   		 GroupMember_idGroup AS groupid
		FROM {$tblUser} AS U
    		INNER JOIN {$tblGM} AS GM
        ON U.idUser = GM.GroupMember_idUser
		WHERE
    		accountUser        = '{$username}' AND
    		passwordUser     = md5('{$password}')
		;
EOD;
		return $query;
	}
	
	//**********************************************************************************************
	//
	//	functions handling articles
	//
	//**********************************************************************************************
	
	//-----------------------------------------------------------------------------------------------
	//
	//	new article
	//
	
	public function newArticle($articleTitle, $articleText) {
	
		$tblArticle = DBT_Article;
		$user = $_SESSION['accountUser'];
		
		$query = <<< EOD
	INSERT INTO dlp_Article (articleTitle, articleText, articleAuthor, articleDate)
	VALUES ('{$articleTitle}', '{$articleText}', '{$user}', NOW());
EOD;
	
	return $query;
	
	}
	
	//-----------------------------------------------------------------------------------------------
	//
	//	edit article
	//
	
	public function editArticle($articleId, $articleTitle, $articleText) {
	
		$tblArticle = DBT_Article;
		
		$query = <<< EOD
			UPDATE dlp_Article
			SET
			articleTitle = '{$articleTitle}', 
			articleText = '{$articleText}',
			articleDate = NOW()
			WHERE
			id = '{$articleId}';
EOD;
	
	return $query;
	
	}
	
	//-----------------------------------------------------------------------------------------------
	//
	//	delete article
	//
	
	public function deleteArticle($articleId) {
	
		$tblArticle = DBT_Article;
		
		$query = <<< EOD
			DELETE FROM dlp_Article
			WHERE
			id = '{$articleId}';
EOD;
	
	return $query;
	
	}
	
	//-----------------------------------------------------------------------------------------------
	//
	//	get all articles
	//
	
	public function getArticles() {
	
		$tblArticle = DBT_Article;
		$user = $_SESSION['accountUser'];
		
		$query = <<< EOD
			SELECT * FROM {$tblArticle}
				ORDER BY articleDate DESC;
EOD;
	
	return $query;
	
	}
	
	//-----------------------------------------------------------------------------------------------
	//
	//	get the latest article
	//
	
	public function getLatestArticle() {
	
		$tblArticle = DBT_Article;
		$user = $_SESSION['accountUser'];
		
		$query = <<< EOD
				SELECT * FROM {$tblArticle}
				ORDER BY articleDate DESC
				LIMIT 1;
EOD;
	
	return $query;
	
	}
	
	//-----------------------------------------------------------------------------------------------
	//
	//	get article
	//
	
	public function getArticle($articleId) {
	
		$tblArticle = DBT_Article;
		$user = $_SESSION['accountUser'];
		
		$query = <<< EOD
				SELECT * FROM {$tblArticle}
				WHERE id = '{$articleId}';
EOD;
	return $query;
	
	}
	
	//-----------------------------------------------------------------------------------------------
	//
	//	get the latest articles
	//
	
	public function getLatestArticles() {
	
		$tblArticle = DBT_Article;
		$user = $_SESSION['accountUser'];
		
		$query = <<< EOD
				SELECT id, articleTitle FROM {$tblArticle}
				ORDER BY articleDate DESC
				LIMIT 5;
EOD;
	
	return $query;
	
	}

//==================================================================================================
//
//	sql for TunaTalk
//
//==================================================================================================
	
	//-----------------------------------------------------------------------------------------------
	//
	//	installing tables and procedures for TunaTalk
	//
	
	public function installTunaTalk() {
	
	$tblUser = DBT_User;
	$tblGroup = DBT_Group;
	$tblGM = DBT_GroupMember;
	
	$tblTopic = DBT_Topic;
	$tblPost = DBT_Post;
	$tblArticle = DBT_Article;
	$tblUser = DBT_User;
	$tblInformation = DBT_Information;
	$tblFile = DBT_File;
	$tblAttachment = DBT_Attachment;
	$spDisplayTopic = DBSP_PDisplayTopic;
	$spCreateOrUpDateTopic = DBSP_PCreateOrUpdateTopic;
	$spShowTopics = DBSP_PShowTopics;
	$spDeletePost = DBSP_PDeletePost;
	$spDisplayPosts = DBSP_PDisplayPosts;
	$spDisplayTopicAndPosts = DBSP_PDisplayTopicAndPosts;
	$spCreateAccount = DBSP_PCreateAccount;
	$spAuthenticateUser = DBSP_PAuthenticateUser;
	$spGetAccountInfo = DBSP_PGetAccountInfo;
	
	$spUpdatePassword = DBSP_PUpdatePassword;
	$spUpdateEmail = DBSP_PUpdateEmail;
	$spUpdateAvatar = DBSP_PUpdateAvatar;
	$spUpdateGravatar = DBSP_PUpdateGravatar;
	
	$fGetGravatarLink = DBF_FGetGravatarLink;
	$fCreatePassword = DBF_FCreatePassword;
	
	$spGetTopicAndAttachment = DBSP_PGetTopicAndAttachment;
	$spGetTopic = DBSP_PGetTopic;
	$spGetAttachment = DBSP_PGetAttachment;
	$spAttachableFiles = DBSP_PAttachableFiles;
	$spAttachedFiles = DBSP_PAttachedFiles;
	$spAttachFile = DBSP_PAttachFile;
	
	$pwdHash = DB_PASSWORDHASHING;
	$imageLink = WS_IMAGES;
	
	$tRemoveAttachment = DBTR_TRemoveAttachment;
	
//	==========================================================================================
//
//	installing tables for the forum
//
//	$tblTopic - containing the actual posts (title and text, both published and draft)
//
//	$tblInformation - containing the info ABOUT the post
//

	$query = <<< EOD
			
			DROP TABLE IF EXISTS {$tblTopic};
			
			CREATE TABLE {$tblTopic} (
			
			-- Primary key
			idTopic INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
			
			-- Attributes
			topicTitle CHAR(60) NOT NULL,
			topicText BLOB NOT NULL,
			topicTitleDraft CHAR(60) NULL,
			topicTextDraft BLOB NULL
			
			);
EOD;
		
		$query .= <<< EOD
		
			DROP TABLE IF EXISTS {$tblInformation};
			
			CREATE TABLE {$tblInformation} (
			
			-- Primary key
			-- idInfo INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
			
			-- Foreign keys
			info_idTopic INT NOT NULL,
			FOREIGN KEY (info_idTopic) REFERENCES {$tblTopic}(idTopic),
			info_parentTopic INT NOT NULL,
			FOREIGN KEY (info_parentTopic) REFERENCES {$tblTopic}(idTopic),
			info_lastPostBy INT NOT NULL,
			FOREIGN KEY (info_lastPostBy) REFERENCES {$tblUser}(idUser),
			topic_idUser INT NOT NULL,
			FOREIGN KEY (topic_idUser) REFERENCES {$tblUser}(idUser),
			
			-- Primary key
			PRIMARY KEY (info_idTopic),
			
			-- Attributes
			createdDate DATETIME NOT NULL,
			lastEditDate DATETIME NULL,
			nrOfPosts INT NOT NULL,
			lastPostDate DATETIME NOT NULL,
			info_publish CHAR(7) NULL,
			topicOrPost BOOLEAN
			
			);
		
EOD;
		

//	==========================================================================================
//
//	SP to create or update posts
//
		$query .= <<< EOD
			
			DROP PROCEDURE IF EXISTS {$spCreateOrUpDateTopic};
			
			CREATE PROCEDURE {$spCreateOrUpDateTopic}(
			INOUT pTopicId INT,
			INOUT pParentId INT,
			IN pTitle CHAR(60),
			IN pContent BLOB,
			IN pUserId INT,
			IN pSaveOrPublish CHAR(7)
			)
			BEGIN
				
				DECLARE postIsPublished BOOLEAN;
			
				IF pSaveOrPublish = 'publish' THEN
				BEGIN
					IF pTopicId = 0 THEN
					BEGIN
						IF pParentId = 0 THEN
						BEGIN
							INSERT INTO {$tblTopic}
								(topicTitle, topicText)
							VALUES
								(pTitle, pContent);
							SET pTopicId = LAST_INSERT_ID();
							INSERT INTO {$tblInformation}
								(info_idTopic, topic_idUser, createdDate, nrOfPosts, info_publish)
							VALUES
								(pTopicId, pUserId, NOW(), 1, pSaveOrPublish);
						END;
						ELSE
						BEGIN
							INSERT INTO {$tblTopic}
								(topicText)
							VALUES
								(pContent);
							SET pTopicId = LAST_INSERT_ID();
							INSERT INTO {$tblInformation}
								(info_idTopic, info_parentTopic, topic_idUser, createdDate, info_publish)
							VALUES
								(pTopicId, pParentId, pUserId, NOW(), pSaveOrPublish);
							UPDATE {$tblInformation} SET
								nrOfPosts = nrOfPosts + 1,
								info_lastPostBy = pUserId,
								lastPostDate = NOW()
							WHERE info_idTopic = pParentId
							LIMIT 1;
							
						END;
						END IF;
					END;
					ELSE
					BEGIN
					
						SELECT info_publish INTO postIsPublished FROM {$tblInformation} WHERE info_idTopic = pTopicId;
					
						IF pParentId = 0 THEN
						BEGIN
							UPDATE {$tblTopic} SET
								topicTitle = pTitle,
								topicText = pContent,
								topicTitleDraft = NULL,
								topicTextDraft = NULL
							WHERE 
								idTopic = pTopicId
							LIMIT 1;
							UPDATE {$tblInformation} SET
								lastEditDate = NOW(),
								info_publish = pSaveOrPublish
							WHERE
								info_idTopic = pTopicId;
						END;
						ELSE
						BEGIN
							UPDATE {$tblTopic} SET
								topicText = pContent
							WHERE
								idTopic = pTopicId;
							UPDATE {$tblInformation} SET
								lastEditDate = NOW()
							WHERE
								info_idTopic = pTopicId;
								
							IF postIsPublished IS NULL THEN
							BEGIN
								UPDATE {$tblTopic} SET
									topicTextDraft = NULL
								WHERE
									idTopic = pTopicId;
								UPDATE {$tblInformation} SET
									info_publish = pSaveOrPublish
								WHERE
									info_idTopic = pTopicId;
								UPDATE {$tblInformation} SET
									nrOfPosts = nrOfPosts + 1,
									info_lastPostBy = pUserId,
									lastPostDate = NOW()
								WHERE info_idTopic = pParentId
								LIMIT 1;
							END;
							END IF;	
						END;
						END IF;
					END;
					END IF;
					END;
					ELSE
					--
					--	save article
					--
					BEGIN
						IF pTopicId = 0 THEN
					BEGIN
						IF pParentId = 0 THEN
						BEGIN
							INSERT INTO {$tblTopic}
								(topicTitleDraft, topicTextDraft)
							VALUES
								(pTitle, pContent);
							SET pTopicId = LAST_INSERT_ID();
							INSERT INTO {$tblInformation}
								(info_idTopic, topic_idUser, createdDate, nrOfPosts)
							VALUES
								(pTopicId, pUserId, NOW(), 1);
						END;
						ELSE
						BEGIN
							INSERT INTO {$tblTopic}
								(topicTextDraft)
							VALUES
								(pContent);
							SET pTopicId = LAST_INSERT_ID();
							INSERT INTO {$tblInformation}
								(info_idTopic, info_parentTopic, topic_idUser, createdDate)
							VALUES
								(pTopicId, pParentId, pUserId, NOW());
							-- UPDATE {$tblInformation} SET
							--	nrOfPosts = nrOfPosts + 1,
							--	info_lastPostBy = pUserId,
							--	lastPostDate = NOW()
							-- WHERE info_idTopic = pParentId
							-- LIMIT 1;
							
						END;
						END IF;
					END;
					ELSE
					BEGIN
						IF pParentId = 0 THEN
						BEGIN
							UPDATE {$tblTopic} SET
								topicTitleDraft = pTitle,
								topicTextDraft = pContent
							WHERE 
								idTopic = pTopicId
							LIMIT 1;
							-- UPDATE {$tblInformation} SET
							--	lastEditDate = NOW()
							-- WHERE
							--	info_idTopic = pTopicId;
							
						END;
						ELSE
						BEGIN
							UPDATE {$tblTopic} SET
								topicTextDraft = pContent
							WHERE
								idTopic = pTopicId;
							-- UPDATE {$tblInformation} SET
							-- 	lastEditDate = NOW()
							-- WHERE
							--	info_idTopic = pTopicId;
						END;
						END IF;
					END;
					END IF;
				END;
				END IF;
			END;
			
			
		
EOD;

//	==========================================================================================
//
//	SP to display a topic
//	

		$query .= <<< EOD
			DROP PROCEDURE IF EXISTS {$spDisplayTopic};
			
			CREATE PROCEDURE {$spDisplayTopic}
				(
					IN pTopicId INT
				)
			BEGIN
				
					SELECT
						T.topicTitle AS title,
						T.topicText AS content,
						I.createdDate AS created,
						I.nrOfPosts AS posts,
						I.info_lastPostBy AS lastPost,
						I.lastPostDate AS lastPostDate,
						COALESCE(I.createdDate, I.lastEditDate) AS lastEdit,
						I.info_parentTopic AS parent,
						U.idUser AS idUser,
						U.avatarUser AS avatar,
						U.accountUser AS username
					FROM {$tblInformation} AS I
						INNER JOIN {$tblTopic} AS T
						ON I.info_idTopic = T.idTopic
						INNER JOIN {$tblUser} AS U
						ON I.topic_idUser = U.idUser
						
					WHERE
						info_idTopic = pTopicId;
				
					SELECT
						U.accountUser AS lastAuthor
					FROM {$tblInformation} AS I
					INNER JOIN {$tblUser} AS U
					ON I.info_lastPostBy = U.idUser
					WHERE
						info_idTopic = pTopicId;
			END;
				
		
EOD;




//	==========================================================================================
//
//	SP to show all topics together with information about each topic
//

		$query .= <<< EOD
			DROP PROCEDURE IF EXISTS {$spShowTopics};
			
			CREATE PROCEDURE {$spShowTopics}(
			
			)
			BEGIN
				SELECT
					T.topicTitle AS title,
					T.idTopic AS idTopic,
					I.createdDate AS created,
					I.nrOfPosts AS posts,
					U.accountUser AS username
				FROM {$tblInformation} AS I
					INNER JOIN {$tblUser} AS U
					ON I.topic_idUser = U.idUser
					INNER JOIN {$tblTopic} AS T
					ON I.info_idTopic = T.idTopic
				WHERE
					I.nrOfPosts > 0 AND I.info_publish = 'publish'
				ORDER BY
					I.createdDate DESC;
			END;
		
EOD;
		

//	==========================================================================================
//
//	SP to delete all posts related to a topic
//
	
		$query .= <<< EOD
			DROP PROCEDURE IF EXISTS {$spDeletePost};
			
			CREATE PROCEDURE {$spDeletePost}(
				IN pTopicId INT,
				IN pParentId INT
			)
			BEGIN
				
				IF pParentId = 0 THEN
				BEGIN
					--
					--	Delete posts connected to topic
					--
					
					DELETE FROM {$tblTopic}
					WHERE
						idTopic = ANY (SELECT info_idTopic FROM {$tblInformation}
									WHERE
									info_parentTopic = pTopicId);
					DELETE FROM {$tblInformation}
					WHERE
					info_parentTopic = pTopicId;
					DELETE FROM {$tblTopic}
					WHERE
					topicTitle = 'deletecode69';
					--
					--	Delete topic
					--
					
					DELETE FROM {$tblInformation}
					WHERE
						info_idTopic = pTopicId
					LIMIT 1;
					DELETE FROM {$tblTopic}
					WHERE
						idTopic = pTopicId
					LIMIT 1;
					
				END;
				ELSE
				BEGIN
					--
					--	Delete chosen post
					--
					DELETE FROM {$tblTopic}
					WHERE idTopic = pTopicId
					LIMIT 1;
					DELETE FROM {$tblInformation}
					WHERE info_idTopic = pTopicId;
					--
					--	Adjust counter of current topic
					--
					UPDATE {$tblInformation} SET
					nrOfPosts = (nrOfPosts - 1 )
					WHERE
					info_idTopic = pParentId
					LIMIT 1;
				END;
				END IF;
			END;
			
EOD;

	

//	==========================================================================================
//
//	SP to display posts related to a specific topic
//	
	

		$query .= <<< EOD
		
		DROP PROCEDURE IF EXISTS {$spDisplayPosts};
		
		CREATE PROCEDURE {$spDisplayPosts}(
			IN pTopicId INT
		)
		BEGIN
			
			SELECT
				T.topicText AS content,
				I.createdDate AS created,
				I.topic_idUser AS userId,
				I.info_parentTopic AS parent,
				I.info_idTopic AS postId,
				U.idUser AS idUserPost,
				U.accountUser AS username,
				U.avatarUser AS avatar,
				U.gravatarUser AS gravatar
			FROM {$tblInformation} AS I
			INNER JOIN {$tblTopic} AS T
			ON  I.info_idTopic = T.idTopic
			INNER JOIN {$tblUser} AS U
			ON I.topic_idUser = idUser
			WHERE
				info_parentTopic = pTopicId AND info_publish = 'publish';
			
		END;
		
EOD;
	

//	==========================================================================================
//
//	SP to display both a topic and its related posts
//

		
			$query .= <<< EOD
		
		DROP PROCEDURE IF EXISTS {$spDisplayTopicAndPosts};
		
		CREATE PROCEDURE {$spDisplayTopicAndPosts}(
			IN pTopicId INT
		)
		BEGIN
			
			call {$spDisplayTopic}(pTopicId);
			
			call {$spDisplayPosts}(pTopicId);
			
		END;
EOD;
		$query .= <<< EOD
		--
		-- Table for attached files
		--
		CREATE TABLE {$tblAttachment} (

 		 -- Primary key(s)
  		--
  		-- The PK is the combination of the two foreign keys, see below.
		--
  
  		-- Foreign keys
  		Attachment_idTopic INT NOT NULL,
  		Attachment_idFile INT NOT NULL,
    
  		FOREIGN KEY (Attachment_idTopic) REFERENCES {$tblTopic}(idTopic),
  		FOREIGN KEY (Attachment_idFile) REFERENCES {$tblFile}(idFile),

  		PRIMARY KEY (Attachment_idTopic, Attachment_idFile)
  
  		-- Attributes

		);

EOD;
	$query .= <<< EOD
			DROP PROCEDURE IF EXISTS {$spGetTopic};
		
			CREATE PROCEDURE {$spGetTopic}
			(
				IN pTopicId TINYINT UNSIGNED
			)
			BEGIN
				SELECT * FROM {$tblTopic}
				WHERE
					idTopic = pTopicId;
			END;
EOD;

	$query .= <<< EOD
			DROP PROCEDURE IF EXISTS {$spGetAttachment};
		
			CREATE PROCEDURE {$spGetAttachment}
			(
				IN pTopicId TINYINT UNSIGNED
			)
			BEGIN
				SELECT 
					F.nameFile AS name,
					F.pathFile AS path,
					F.uniqueNameFile AS uniqueName,
					F.file_idUser AS userId,
					F.idFile AS fileId
				FROM {$tblTopic} AS T
					INNER JOIN {$tblAttachment} AS A
						ON T.idTopic = A.Attachment_idTopic
					INNER JOIN {$tblFile} AS F
						ON A.Attachment_idFile = idFile
				WHERE
					idTopic = pTopicId;
			END;
EOD;

	$query .= <<< EOD
		
			DROP PROCEDURE IF EXISTS {$spGetTopicAndAttachment};
		
			CREATE PROCEDURE {$spGetTopicAndAttachment}
			(
				IN pTopicId TINYINT UNSIGNED
			)
			BEGIN
				call {$spGetTopic}(pTopicId);
				call {$spGetAttachment}(pTopicId);
			END;
EOD;

		$query .= <<< EOD
		
		DROP PROCEDURE IF EXISTS {$spAttachFile};
		
			CREATE PROCEDURE {$spAttachFile}
			(
				IN pTopicId INT,
				IN pFileId INT
			)
			BEGIN
				INSERT INTO {$tblAttachment}
					(Attachment_idTopic, Attachment_idFile)
				VALUES
					(pTopicId, pFileId);
			
			END;
EOD;

		$query .= <<< EOD
	
			DROP TRIGGER IF EXISTS {$tRemoveAttachment};
		
			CREATE TRIGGER {$tRemoveAttachment}
			AFTER DELETE ON {$tblFile}
				FOR EACH ROW
				BEGIN
					DELETE FROM {$tblAttachment}
					WHERE
					Attachment_idFile = OLD.idFile;
				END;
EOD;

/*		
		$query .= <<< EOD
			
			DROP PROCEDURE IF EXISTS {$spCreateOrUpDateTopic};
			
			CREATE PROCEDURE {$spCreateOrUpDateTopic}(
			INOUT pTopicId INT,
			INOUT pParentId INT,
			IN pTitle CHAR(60),
			IN pContent BLOB,
			IN pUserId INT,
			IN pSaveOrPublish CHAR(7)
			)
			BEGIN
				
				DECLARE postIsPublished BOOLEAN;
			
				IF pSaveOrPublish = 'publish' THEN
				BEGIN
					IF pTopicId = 0 THEN
					BEGIN
						IF pParentId = 0 THEN
						BEGIN
							INSERT INTO {$tblTopic}
								(topicTitle, topicText)
							VALUES
								(pTitle, pContent);
							SET pTopicId = LAST_INSERT_ID();
							INSERT INTO {$tblInformation}
								(info_idTopic, topic_idUser, createdDate, nrOfPosts, info_publish)
							VALUES
								(pTopicId, pUserId, NOW(), 1, pSaveOrPublish);
						END;
						ELSE
						BEGIN
							INSERT INTO {$tblTopic}
								(topicText)
							VALUES
								(pContent);
							SET pTopicId = LAST_INSERT_ID();
							INSERT INTO {$tblInformation}
								(info_idTopic, info_parentTopic, topic_idUser, createdDate, info_publish)
							VALUES
								(pTopicId, pParentId, pUserId, NOW(), pSaveOrPublish);
							UPDATE {$tblInformation} SET
								nrOfPosts = nrOfPosts + 1,
								info_lastPostBy = pUserId,
								lastPostDate = NOW()
							WHERE info_idTopic = pParentId
							LIMIT 1;
							
						END;
						END IF;
					END;
					ELSE
					BEGIN
					
						SELECT info_publish INTO postIsPublished FROM {$tblInformation} WHERE info_idTopic = pTopicId;
					
						IF pParentId = 0 THEN
						BEGIN
							UPDATE {$tblTopic} SET
								topicTitle = pTitle,
								topicText = pContent,
								topicTitleDraft = NULL,
								topicTextDraft = NULL
							WHERE 
								idTopic = pTopicId
							LIMIT 1;
							UPDATE {$tblInformation} SET
								lastEditDate = NOW(),
								info_publish = pSaveOrPublish
							WHERE
								info_idTopic = pTopicId;
						END;
						ELSE
						BEGIN
							UPDATE {$tblTopic} SET
								topicText = pContent
							WHERE
								idTopic = pTopicId;
							UPDATE {$tblInformation} SET
								lastEditDate = NOW()
							WHERE
								info_idTopic = pTopicId;
								
							IF postIsPublished IS NULL THEN
							BEGIN
								UPDATE {$tblTopic} SET
									topicTextDraft = NULL
								WHERE
									idTopic = pTopicId;
								UPDATE {$tblInformation} SET
									info_publish = pSaveOrPublish
								WHERE
									info_idTopic = pTopicId;
								UPDATE {$tblInformation} SET
									nrOfPosts = nrOfPosts + 1,
									info_lastPostBy = pUserId,
									lastPostDate = NOW()
								WHERE info_idTopic = pParentId
								LIMIT 1;
							END;
							END IF;	
						END;
						END IF;
					END;
					END IF;
					END;
					ELSE
					--
					--	save article
					--
					BEGIN
						IF pTopicId = 0 THEN
					BEGIN
						IF pParentId = 0 THEN
						BEGIN
							INSERT INTO {$tblTopic}
								(topicTitleDraft, topicTextDraft)
							VALUES
								(pTitle, pContent);
							SET pTopicId = LAST_INSERT_ID();
							INSERT INTO {$tblInformation}
								(info_idTopic, topic_idUser, createdDate, nrOfPosts)
							VALUES
								(pTopicId, pUserId, NOW(), 1);
						END;
						ELSE
						BEGIN
							INSERT INTO {$tblTopic}
								(topicTextDraft)
							VALUES
								(pContent);
							SET pTopicId = LAST_INSERT_ID();
							INSERT INTO {$tblInformation}
								(info_idTopic, info_parentTopic, topic_idUser, createdDate)
							VALUES
								(pTopicId, pParentId, pUserId, NOW());
							-- UPDATE {$tblInformation} SET
							--	nrOfPosts = nrOfPosts + 1,
							--	info_lastPostBy = pUserId,
							--	lastPostDate = NOW()
							-- WHERE info_idTopic = pParentId
							-- LIMIT 1;
							
						END;
						END IF;
					END;
					ELSE
					BEGIN
						IF pParentId = 0 THEN
						BEGIN
							UPDATE {$tblTopic} SET
								topicTitleDraft = pTitle,
								topicTextDraft = pContent
							WHERE 
								idTopic = pTopicId
							LIMIT 1;
							-- UPDATE {$tblInformation} SET
							--	lastEditDate = NOW()
							-- WHERE
							--	info_idTopic = pTopicId;
							
						END;
						ELSE
						BEGIN
							UPDATE {$tblTopic} SET
								topicTextDraft = pContent
							WHERE
								idTopic = pTopicId;
							-- UPDATE {$tblInformation} SET
							-- 	lastEditDate = NOW()
							-- WHERE
							--	info_idTopic = pTopicId;
						END;
						END IF;
					END;
					END IF;
				END;
				END IF;
			END;
			
			
		
EOD;

	$query = <<< EOD
		DROP PROCEDURE IF EXISTS {$spCreateAccount};
		
		CREATE PROCEDURE {$spCreateAccount}(
			OUT pUserId INT,
			IN pAccountUser CHAR(20),
			IN pPassword VARCHAR(80),
			IN pMethod CHAR(5),
			OUT pStatus INT
		)
		BEGIN
		
			DECLARE salt BINARY(10);
		
			SELECT idUser INTO pUserId FROM {$tblUser} WHERE accountUser = pAccountUser;
			
			IF pUserId IS NOT NULL THEN
			BEGIN
				SET pStatus = 1;  -- Failure, the name exists
			END;
			ELSE
			BEGIN
			
				SELECT BINARY(UNIX_TIMESTAMP(NOW())) INTO salt;
			
				INSERT INTO {$tblUser}
					(accountUser, saltUser, passwordUser, methodUser, avatarUser)
				VALUES
					(pAccountUser, salt, {$fCreatePassword}(salt, pPassword, pMethod), pMethod, 'http://www.student.bth.se/~niod09/dbwebb2/development/dolphin/images/userblue.png');
					
				SET pUserId = LAST_INSERT_ID();
				INSERT INTO {$tblGM}
					(Groupmember_idUser, Groupmember_idGroup)
				VALUES(pUserId, 'usr');
				SET pStatus = 0;
			END;
			END IF;
			
		END;
	
EOD;
	
	$query = <<< EOD
		DROP PROCEDURE IF EXISTS {$spAuthenticateUser};
		
		CREATE PROCEDURE {$spAuthenticateUser}(
			IN pUserAccountOrEmail VARCHAR(80),
			IN pPassword VARCHAR(80),
			OUT pUserId INT,
			OUT pStatus INT
		)
		BEGIN
			SELECT idUser INTO pUserId FROM {$tblUser}
			WHERE
				(
				accountUser = pUserAccountOrEmail
			AND
				passwordUser = {$fCreatePassword}(saltUser, pPassword, methodUser)
				)
			OR
				(
				emailUser = pUserAccountOrEmail
			AND
				passwordUser = {$fCreatePassword}(saltUser, pPassword, methodUser)
				)
			;
			IF pUserId IS NULL THEN
				SET pStatus = 1;
			ELSE
				SET pStatus = 0;
			END IF;
		END;
	
EOD;
	
	
	$query = <<< EOD
		DROP PROCEDURE IF EXISTS {$spGetAccountInfo};
		
		CREATE PROCEDURE {$spGetAccountInfo}(
			IN pUserId INT
			)
		BEGIN
			SELECT 
				U.idUser AS id, 
				U.accountUser AS account, 
				U.emailUser AS email, 
				U.avatarUser AS avatar,
				U.gravatarUser AS gravatar,
				{$fGetGravatarLink}(U.gravatarUser, 80) AS gravatarLink,
				G.Groupmember_idGroup AS groupId
			FROM {$tblUser} AS U
			INNER JOIN {$tblGM} AS G
			ON U.idUser = G.Groupmember_idUser
			WHERE
				U.idUser = pUserId;
		END;	
	
EOD;
	
	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spUpdatePassword};
		
		CREATE PROCEDURE {$spUpdatePassword}(
			IN pUserId INT,
			IN pPassword VARCHAR(60)
		)
		BEGIN
			UPDATE {$tblUser} SET
				saltUser = BINARY(UNIX_TIMESTAMP(NOW())),
				passwordUser = {$fCreatePassword}(saltUser, pPassword, methodUser)
			WHERE
				idUser = pUserId
			LIMIT 1;
		END;
	
EOD;

	$query = <<< EOD
		DROP PROCEDURE IF EXISTS {$spUpdateEmail};
		
		CREATE PROCEDURE {$spUpdateEmail}(
			IN pUserId INT,
			IN pEmail VARCHAR(80),
			OUT pRowsAffected INT
		)
		BEGIN
			UPDATE {$tblUser} SET
				emailUser = pEmail
			WHERE
				idUser = pUserId
			LIMIT 1;
			SELECT ROW_COUNT() INTO pRowsAffected;
		END;
	
EOD;

	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spUpdateAvatar};
		
		CREATE PROCEDURE {$spUpdateAvatar}(
			IN pUserId INT,
			IN pAvatar VARCHAR(100)
		)
		BEGIN
			UPDATE {$tblUser} SET
				avatarUser = pAvatar
			WHERE
				idUser = pUserId
			LIMIT 1;
		END;
	
EOD;

	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spUpdateGravatar};
		
		CREATE PROCEDURE {$spUpdateGravatar}(
			IN pUserId INT,
			IN pGravatar VARCHAR(100)
		)
		BEGIN
			UPDATE {$tblUser} SET
				gravatarUser = TRIM(pGravatar)
			WHERE
				idUser = pUserId
			LIMIT 1;
		END;
	
EOD;
	
	$query = <<< EOD
		DROP FUNCTION IF EXISTS {$fGetGravatarLink};
		
		CREATE FUNCTION {$fGetGravatarLink}
		(
			fEmail VARCHAR(80),
			fSize INT
		)
		RETURNS VARCHAR(80)
		BEGIN
			DECLARE fLink VARCHAR(80);
			
			SELECT CONCAT('http://www.gravatar.com/avatar/', MD5(LOWER(fEmail)), '.jpg?s=', fSize)
			INTO fLink;
			
			RETURN fLink;
		END;
EOD;
	
	
	$pwdHash = DB_PASSWORDHASHING;
	$account = 'admin';
	$password = 'secret';
	$mail = 'nicke@nymusik.net';
	$avatar = $imageLink . "userblue.png";
	
		$query = <<< EOD
			CALL {$spCreateAccount}(@pUserId, '{$account}', '{$password}', '{$pwdHash}', @pStatus);
			CALL {$spUpdateEmail}(1, '{$mail}', @pRowsAffected);
			CALL {$spUpdateAvatar}(1, '{$avatar}');
			INSERT INTO {$tblGM} (GroupMember_idUser, GroupMember_idGroup)
			VALUES (1, 'adm');
EOD;
	
		$query = <<< EOD
			DROP FUNCTION IF EXISTS {$fCreatePassword};
			CREATE FUNCTION {$fCreatePassword}
			(
				fSalt BINARY(10),
				fPassword CHAR(32),
				fMethod CHAR(5)
			)
			RETURNS VARBINARY(40)
			BEGIN
				DECLARE password VARBINARY(40);
				
				CASE TRIM(fMethod)
					WHEN 'MD5' THEN SELECT md5(CONCAT(fSalt, fPassword)) INTO password;
					WHEN 'SHA-1' THEN SELECT sha1(CONCAT(fSalt, fPassword)) INTO password;
					WHEN 'PLAIN' THEN SELECT fPassword INTO password;
				END CASE;
				
				RETURN password;
				
			END;
EOD;
	*/
		return $query;
	
	}

//==================================================================================================
//
//	sql for file
//
//==================================================================================================
	
	//-----------------------------------------------------------------------------------------------
	//
	//	installing tables and procedures for handling files/archive
	//
	/*
	public function installFile() {
	
	$tblGroup = DBT_Group;
	$tblGM = DBT_GroupMember;
	$tblUser = DBT_User;
	$tblFile = DBT_File;
	$tblTopic = DBT_Topic;
	$tblAttachment = DBT_Attachment;
	$spInsertFile = DBSP_PInsertFile;
	$spListFiles = DBSP_PListFiles;
	$spGetFileDetails = DBSP_PGetFileDetails;
	$spUpdateOrDeleteFile = DBSP_PUpdateOrDeleteFile;
	$spAttachFile = DBSP_PAttachFile;
	$spListTrash = DBSP_PListTrash;
	$spGetTopicAndAttachment = DBSP_PGetTopicAndAttachment;
	$spGetTopic = DBSP_PGetTopic;
	$spGetAttachment = DBSP_PGetAttachment;
	$spAttachableFiles = DBSP_PAttachableFiles;
	$spAttachedFiles = DBSP_PAttachedFiles;
	
	$spAdminListFiles = DBSP_PAdminListFiles;
	$spAdminListAccounts = DBSP_PAdminListAccounts;
	
	$fCheckUserIsAdmin = DBF_FCheckUserIsAdmin;
	$fCheckFilePermission = DBF_FCheckFilePermission;
	
	$tRemoveAttachment = DBTR_TRemoveAttachment;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	table - handling files
	//
	
	$query = <<< EOD
	
	DROP TABLE IF EXISTS {$tblFile};
	
	CREATE TABLE {$tblFile}(
		
		-- Primary key
		idFile INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
		
		-- Foreign key
		file_idUser INT UNSIGNED NOT NULL,
		FOREIGN KEY (file_idUser) REFERENCES {$tblUser}(idUser),
		
		-- Attributes
		nameFile VARCHAR(80) NOT NULL,
		pathFile VARCHAR(160) NOT NULL,
		uniqueNameFile VARCHAR(60) NOT NULL UNIQUE,
		sizeFile INT UNSIGNED NOT NULL,
		mimetypeFile VARCHAR(127) NOT NULL,
		createdFile DATETIME NOT NULL,
		modifiedFile DATETIME NULL,
		deletedFile DATETIME NULL
	);

EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - inserting info about new file in db
	//
	
	$query .= <<< EOD
	DROP PROCEDURE IF EXISTS {$spInsertFile};
	
	CREATE PROCEDURE {$spInsertFile}
	(
		IN pIdUser INT UNSIGNED,
		IN pNameFile VARCHAR(80),
		IN pUniqueNameFile VARCHAR(60),
		IN pPathFile VARCHAR(160),
		IN pSizeFile INT UNSIGNED,
		IN pMimetypeFile VARCHAR(127)
	)
	BEGIN
		
		INSERT INTO {$tblFile}
			(file_idUser, nameFile, uniqueNameFile, pathFile, sizeFile, mimetypeFile, createdFile)
		VALUES
			(pIdUser, pNameFile, pUniqueNameFile, pPathFile, pSizeFile, pMimetypeFile, NOW());
	END;
	
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - get info about current users files in db
	//
	
	$query = <<< EOD
	DROP PROCEDURE IF EXISTS {$spListFiles};
	
	CREATE PROCEDURE {$spListFiles}
	(
		IN pIdUser INT UNSIGNED
	)
	BEGIN
		SELECT
			idFile,
			nameFile AS name,
			uniqueNameFile AS uniqueName,
			pathFile AS path,
			sizeFile AS size,
			mimetypeFile AS mimetype,
			createdFile AS created,
			modifiedFile AS modified,
			deletedFile AS deleted
		FROM
			{$tblFile}
		WHERE 
			file_idUser = pIdUser
		AND
			deletedFile IS NULL;
	END;
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - checking if file exists and if user has the rights to edit
	//
	
	
	$query = <<< EOD
	DROP FUNCTION IF EXISTS {$fCheckFilePermission};
	
	CREATE FUNCTION {$fCheckFilePermission}
	(
		fIdUser INT UNSIGNED,
		fIdFile INT UNSIGNED
	)
	RETURNS INT UNSIGNED
	BEGIN
		DECLARE fId INT UNSIGNED;
		
		SELECT idFile INTO fId
		FROM {$tblFile}
		WHERE
			idFile = fIdFile
		AND
			({$fCheckUserIsAdmin}(fIdUser)
			OR
			file_idUser = fIdUser);
		IF fId IS NOT NULL THEN
		RETURN 0;
		END IF;

		-- Does file exists?
		SELECT idFile INTO fId FROM {$tblFile} WHERE idFile = fIdFile;
		IF fId IS NULL THEN
		RETURN 2;
		END IF;

		-- So, file exists but user has no permissions to use/update file.
		RETURN 1;
	END;
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - get information about file
	//
	
	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spGetFileDetails};
		
		CREATE PROCEDURE {$spGetFileDetails}
		(
			IN pIdUser INT UNSIGNED,
			IN pUniqueNameFile VARCHAR(60),
			OUT pSuccess TINYINT UNSIGNED 
		)
		BEGIN
			DECLARE fileId INT UNSIGNED;
		-- Get the id of the file
		SELECT idFile INTO fileId FROM {$tblFile}
		WHERE
		uniqueNameFile = pUniqueNameFile;

		-- Check permissions
		SELECT {$fCheckFilePermission}(pIdUser, fileId) INTO pSuccess;
		-- Get details from file
		SELECT
			idFile AS fileId,
			file_idUser AS userId,
			U.accountUser AS owner,
			nameFile AS name,
			uniqueNameFile AS uniqueName,
			pathFile AS path,
			sizeFile AS size,
			mimetypeFile AS mimetype,
			createdFile AS created,
			modifiedFile AS modified,
			deletedFile AS deleted
		FROM {$tblFile} AS F
		INNER JOIN {$tblUser} AS U
			ON F.file_idUser = U.idUser
		WHERE
			uniqueNameFile = pUniqueNameFile;
		END;
EOD;
		
	$query = <<< EOD
		--
		-- Table for attached files
		--
		CREATE TABLE {$tblAttachment} (

 		 -- Primary key(s)
  		--
  		-- The PK is the combination of the two foreign keys, see below.
		--
  
  		-- Foreign keys
  		Attachment_idTopic INT NOT NULL,
  		Attachment_idFile INT NOT NULL,
    
  		FOREIGN KEY (Attachment_idTopic) REFERENCES {$tblTopic}(idTopic),
  		FOREIGN KEY (Attachment_idFile) REFERENCES {$tblFile}(idFile),

  		PRIMARY KEY (Attachment_idTopic, Attachment_idFile)
  
  		-- Attributes

		);

EOD;
	$query = <<< EOD
		DROP PROCEDURE IF EXISTS {$spGetTopic};
		
		CREATE PROCEDURE {$spGetTopic}
		(
			IN pTopicId TINYINT UNSIGNED
		)
		BEGIN
			SELECT * FROM {$tblTopic}
			WHERE
				idTopic = pTopicId;
		END;
EOD;

	$query .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spGetAttachment};
		
		CREATE PROCEDURE {$spGetAttachment}
		(
			IN pTopicId TINYINT UNSIGNED
		)
		BEGIN
			SELECT 
				F.nameFile AS name,
				F.pathFile AS path,
				F.uniqueNameFile AS uniqueName,
				F.file_idUser AS userId,
				F.idFile AS fileId
			FROM {$tblTopic} AS T
				INNER JOIN {$tblAttachment} AS A
					ON T.idTopic = A.Attachment_idTopic
				INNER JOIN {$tblFile} AS F
					ON A.Attachment_idFile = idFile
			WHERE
				idTopic = pTopicId;
		END;
EOD;

	$query .= <<< EOD
		
		DROP PROCEDURE IF EXISTS {$spGetTopicAndAttachment};
		
		CREATE PROCEDURE {$spGetTopicAndAttachment}
		(
			IN pTopicId TINYINT UNSIGNED
		)
		BEGIN
			call {$spGetTopic}(pTopicId);
			call {$spGetAttachment}(pTopicId);
		END;
EOD;

	$query = <<< EOD
		
		DROP PROCEDURE IF EXISTS {$spAttachFile};
		
		CREATE PROCEDURE {$spAttachFile}
		(
			IN pTopicId INT,
			IN pFileId INT
		)
		BEGIN
			INSERT INTO {$tblAttachment}
				(Attachment_idTopic, Attachment_idFile)
			VALUES
				(pTopicId, pFileId);
			
		END;
EOD;

	$query = <<< EOD
	
		DROP TRIGGER IF EXISTS {$tRemoveAttachment};
		
		CREATE TRIGGER {$tRemoveAttachment}
		AFTER DELETE ON {$tblFile}
			FOR EACH ROW
			BEGIN
				DELETE FROM {$tblAttachment}
				WHERE
				Attachment_idFile = OLD.idFile;
			END;
EOD;


	$query = <<< EOD
		DROP PROCEDURE IF EXISTS {$spUpdateOrDeleteFile};
		
		CREATE PROCEDURE {$spUpdateOrDeleteFile}
		(
			IN pIdUser INT UNSIGNED,
			IN pUniqueNameFile VARCHAR(60),
			IN pNameFile VARCHAR(80),
			IN pMimetypeFile VARCHAR(127),
			IN pUpdateOrDelete CHAR(7),
			OUT pSuccess TINYINT UNSIGNED
		)
		BEGIN
			DECLARE fileId INT UNSIGNED;
			-- Get the id of the file
			SELECT idFile INTO fileId FROM {$tblFile}
			WHERE
			uniqueNameFile = pUniqueNameFile;

			-- Check permissions
			SELECT {$fCheckFilePermission}(pIdUser, fileId) INTO pSuccess;
			IF pSuccess = 0 THEN
				BEGIN
				IF pUpdateOrDelete = 'edit' THEN
				BEGIN
					UPDATE {$tblFile} SET
						nameFile = pNameFile,
						mimetypeFile = pMimetypeFile,
						modifiedFile = NOW()
					WHERE
						uniqueNameFile = pUniqueNameFile;
				END;
				--
				-- pseudodelete file, ie mark it as deleted but keep info in db
				--
				ELSEIF pUpdateOrDelete = 'trash' THEN
				BEGIN
					UPDATE {$tblFile} SET
						deletedFile = NOW()
					WHERE
						uniqueNameFile = pUniqueNameFile;
				END;
				--
				-- deleting the file from db
				--
				ELSEIF pUpdateOrDelete = 'delete' THEN
				BEGIN
					DELETE FROM {$tblFile}
					WHERE
						uniqueNameFile = pUniqueNameFile
					LIMIT 1;
				END;
				--
				--	pseudorecover ie bring back from trashcan
				--
				ELSEIF pUpdateOrDelete = 'recover' THEN
				BEGIN
					UPDATE {$tblFile} SET
						deletedFile = NULL
					WHERE
						uniqueNameFile = pUniqueNameFile;
				END;
				END IF;
			END;
			END IF;
		END;
EOD;

	
	
	$query = <<< EOD
	DROP PROCEDURE IF EXISTS {$spListTrash};
	
	CREATE PROCEDURE {$spListTrash}
	(
		IN pIdUser INT UNSIGNED
	)
	BEGIN
		SELECT
			nameFile AS name,
			uniqueNameFile AS uniqueName,
			pathFile AS path,
			sizeFile AS size,
			mimetypeFile AS mimetype,
			createdFile AS created,
			modifiedFile AS modified,
			deletedFile AS deleted
		FROM
			{$tblFile}
		WHERE 
			file_idUser = pIdUser
		AND
			deletedFile IS NOT NULL;
	END;
EOD;
	
	
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - get info about current users files in db
	//
	
	$query = <<< EOD
	DROP PROCEDURE IF EXISTS {$spAttachableFiles};
	
	CREATE PROCEDURE {$spAttachableFiles}
	(
		IN pIdUser INT UNSIGNED,
		IN pIdTopic INT UNSIGNED
	)
	BEGIN
		SELECT
			idFile,
			nameFile AS name,
			uniqueNameFile AS uniqueName,
			pathFile AS path,
			sizeFile AS size,
			mimetypeFile AS mimetype,
			createdFile AS created,
			modifiedFile AS modified,
			deletedFile AS deleted
			
		FROM
			{$tblFile} AS F
		WHERE
			idFile NOT IN(SELECT Attachment_idFile FROM {$tblAttachment} WHERE Attachment_idTopic = pIdTopic)
		AND
			deletedFile IS NULL
		;
	END;
EOD;

	$query = <<< EOD
	DROP PROCEDURE IF EXISTS {$spAttachedFiles};
	
	CREATE PROCEDURE {$spAttachedFiles}
	(
		IN pIdUser INT UNSIGNED,
		IN pIdTopic INT UNSIGNED
	)
	BEGIN
		SELECT
			idFile,
			nameFile AS name,
			uniqueNameFile AS uniqueName,
			pathFile AS path,
			sizeFile AS size,
			mimetypeFile AS mimetype,
			createdFile AS created,
			modifiedFile AS modified,
			deletedFile AS deleted
			
		FROM
			{$tblFile} AS F
		WHERE
			idFile IN (SELECT Attachment_idFile FROM {$tblAttachment} WHERE Attachment_idTopic = pIdTopic)
		AND
			deletedFile IS NULL
		;
	END;
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedures - admin resources
	//
	
	

	$query = <<< EOD
		DROP PROCEDURE IF EXISTS {$spAdminListFiles};
		
		CREATE PROCEDURE {$spAdminListFiles}
		(
			OUT pTotalSize INT,
			OUT pTotalFiles INT
		)
		BEGIN
			SELECT
				F.idFile,
				F.nameFile AS name,
				F.uniqueNameFile AS uniqueName,
				F.pathFile AS path,
				F.sizeFile AS size,
				F.mimetypeFile AS mimetype,
				F.createdFile AS created,
				F.modifiedFile AS modified,
				F.deletedFile AS deleted,
				U.accountUser As user,
				U.idUser
			FROM
				{$tblFile} AS F
			INNER JOIN {$tblUser} AS U
			ON
				U.idUser = F.file_idUser;
			SELECT SUM(sizeFile) INTO pTotalSize FROM {$tblFile};
			SELECT COUNT(*) INTO pTotalFiles FROM {$tblFile};

		END;
EOD;

		
		$query = <<< EOD
		DROP PROCEDURE IF EXISTS {$spAdminListAccounts};
		
		CREATE PROCEDURE {$spAdminListAccounts}
		(
			
		)
		BEGIN
			SELECT
				U.idUser AS id, 
				U.accountUser AS account, 
				U.emailUser AS email, 
				U.avatarUser AS avatar,
				U.gravatarUser AS gravatar,
				G.Groupmember_idGroup AS groupId
			FROM {$tblUser} AS U
			INNER JOIN {$tblGM} AS G
			ON U.idUser = G.Groupmember_idUser;
		END;
EOD;
	
	
	return $query;
	
	}
	*/
	} // end of class
?>