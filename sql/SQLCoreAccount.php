<?php
//======================================================================================
//
//	Description: SQL-File containing sql for account tables and stored procedures
//
//	Author: Niklas Odén
//
//======================================================================================
		
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
		
		$queryAccount = <<< EOD
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
	
		$queryAccount .= <<< EOD
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
		$queryAccount .= <<< EOD
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
	
		$queryAccount .= <<< EOD
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
	
		$queryAccount .= <<< EOD
		DROP PROCEDURE IF EXISTS {$spCreateAccount};
		
		CREATE PROCEDURE {$spCreateAccount}(
			OUT pUserId INT,
			IN pAccountUser CHAR(20),
			IN pGroupUser CHAR(3),
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
				VALUES(pUserId, pGroupUser);
				SET pStatus = 0;
			END;
			END IF;
			
		END;
	
EOD;
	
	//-----------------------------------------------------------------------------------------------
	//
	//	stored procedure - authenticate user
	//
	
	$queryAccount .= <<< EOD
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
	
	$queryAccount .= <<< EOD
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
	
	$queryAccount .= <<< EOD
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
	
	$queryAccount .= <<< EOD
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
	
	$queryAccount .= <<< EOD
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
	
	$queryAccount .= <<< EOD
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
	$admin = 'adm';
	$user = 'usr';
	$account_user = 'tuna';
	$password = 'secret';
	$mail = 'someadmin@somemail.net';
	$mail2 = 'someuser@somemail.com';
	$avatar = $imageLink . "userblue.png";
	
		$queryAccount .= <<< EOD
			CALL {$spCreateAccount}(@pUserId, '{$account}', '{$admin}', '{$password}', '{$pwdHash}', @pStatus);
			CALL {$spUpdateEmail}(1, '{$mail}', @pRowsAffected);
			CALL {$spUpdateAvatar}(1, '{$avatar}');
			
			CALL {$spCreateAccount}(@pUserId, '{$account_user}', '{$user}', '{$password}', '{$pwdHash}', @pStatus);
			CALL {$spUpdateEmail}(2, '{$mail2}', @pRowsAffected);
			CALL {$spUpdateAvatar}(2, '{$avatar}');
			
            
			INSERT INTO {$tblGroup} (idGroup, nameGroup)
			VALUES ('adm', 'Administrators');
			INSERT INTO {$tblGroup} (idGroup, nameGroup)
			VALUES ('usr', 'Regular users');
EOD;


?>