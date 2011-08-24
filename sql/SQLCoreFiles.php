<?php
//=====================================================================================
//
// 	Description: SQL-file containing sql for core-file
//
//	Author: Niklas Odén
//
//=====================================================================================

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
	
	$queryFile = <<< EOD
	
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
	
	$queryFile .= <<< EOD
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
	
	$queryFile .= <<< EOD
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
	
	
	$queryFile .= <<< EOD
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
	
	$queryFile .= <<< EOD
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
	
	$queryFile .= <<< EOD
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

	
	
	$queryFile .= <<< EOD
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

	$queryFile .= <<< EOD
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

		
	$queryFile .= <<< EOD
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

?>