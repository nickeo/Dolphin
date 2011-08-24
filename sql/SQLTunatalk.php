<?php
//==================================================================================================
//
//
//
//
//
//==================================================================================================

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
//	$tblInformation - containing info ABOUT the post
//

	$queryTunatalk = <<< EOD
			
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
		
		$queryTunatalk .= <<< EOD
		
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
		$queryTunatalk .= <<< EOD
			
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

		$queryTunatalk .= <<< EOD
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
						U.gravatarUser AS gravatar,
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

		$queryTunatalk .= <<< EOD
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
	
		$queryTunatalk .= <<< EOD
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
	

		$queryTunatalk .= <<< EOD
		
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

		
			$queryTunatalk .= <<< EOD
		
		DROP PROCEDURE IF EXISTS {$spDisplayTopicAndPosts};
		
		CREATE PROCEDURE {$spDisplayTopicAndPosts}(
			IN pTopicId INT
		)
		BEGIN
			
			call {$spDisplayTopic}(pTopicId);
			
			call {$spDisplayPosts}(pTopicId);
			
		END;
EOD;
		$queryTunatalk .= <<< EOD
		--
		-- Table for attached files
		--
		
		DROP TABLE IF EXISTS {$tblAttachment};
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
	$queryTunatalk .= <<< EOD
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

	$queryTunatalk .= <<< EOD
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

	$queryTunatalk .= <<< EOD
		
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

		$queryTunatalk .= <<< EOD
		
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

		$queryTunatalk .= <<< EOD
	
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
		
		$queryTunatalk .= <<< EOD
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
		
		$queryTunatalk .= <<< EOD
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
?>