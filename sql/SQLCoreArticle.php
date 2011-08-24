<?php
//==================================================================================================
//
//	Description: SQL-file, sql for core-articles
//
//	Author: Niklas Odén
//
//==================================================================================================

		$tblArticle = DBT_Article;
		$tblUser = DBT_User;
		$tblGroup = DBT_Group;
		$tblGM = DBT_GroupMember;
		$spPCreateNewArticle = DBSP_PCreateNewArticle;
		$spPUpdateArticle = DBSP_PUpdateArticle;
		$spPDisplayArticle = DBSP_PDisplayArticle;
		$spPListArticles = DBSP_PListArticles;
		$FCheckUserIsAdminOrOwner = DBF_FCheckUserIsAdminOrOwner;
		$FGetGroup = DBF_FGetGroup;

$queryArticle = <<< EOD
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

$queryArticle .= <<< EOD
		
			DROP PROCEDURE IF EXISTS {$spPCreateNewArticle};
			
			CREATE PROCEDURE {$spPCreateNewArticle}(
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

		$queryArticle .= <<< EOD
			DROP PROCEDURE IF EXISTS {$spPUpdateArticle};
			
			CREATE PROCEDURE {$spPUpdateArticle}(
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
						{$FCheckUserIsAdminOrOwner}(pArticleId, userId)
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
						{$FCheckUserIsAdminOrOwner}(pArticleId, userId)
					LIMIT 1;
				END;
				END IF;
			END;
		
EOD;
		
		$queryArticle .= <<< EOD
			DROP PROCEDURE IF EXISTS {$spPDisplayArticle};
			
			CREATE PROCEDURE {$spPDisplayArticle}
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
				SELECT {$FCheckUserIsAdminOrOwner}(pArticleId, puserId) INTO pGrantRights;
			END;
				
		
EOD;
		
		$queryArticle .= <<< EOD
		
		DROP PROCEDURE IF EXISTS {$spPListArticles};
		
		CREATE PROCEDURE {$spPListArticles}(
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
		
		
		
		$queryArticle .= <<< EOD
			DROP FUNCTION IF EXISTS {$FCheckUserIsAdminOrOwner};
			
			CREATE FUNCTION {$FCheckUserIsAdminOrOwner}(fIdArticle INT, fIdUser INT)
			RETURNS BOOLEAN
			BEGIN
				DECLARE userId INT;
				DECLARE groupStatus CHAR(3);
				DECLARE editGrant BOOLEAN;
				SET editGrant = FALSE;
				
				SELECT {$FGetGroup}(fidUser) INTO groupStatus;
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

$queryArticle .= <<< EOD
			DROP FUNCTION IF EXISTS {$FGetGroup};
			
			CREATE FUNCTION {$FGetGroup}(fIdUser INT)
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


?>