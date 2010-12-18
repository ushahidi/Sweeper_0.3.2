/*
DROP TABLE IF EXISTS SC_Channels;
DROP TABLE IF EXISTS SC_Sources;
DROP TABLE IF EXISTS SC_Content;
DROP TABLE IF EXISTS SC_Tags;
DROP TABLE IF EXISTS SC_Content_Tags;
*/
-- *****************************************************************************
-- Tables 
-- *****************************************************************************

-- Create the Channel table
CREATE TABLE IF NOT EXISTS SC_Channels (
    id VARCHAR( 48 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    type VARCHAR( 48 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    subType VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    active BIT( 1 ) NOT NULL ,
    inProcess BIT( 1 ) NOT NULL ,
    nextRun INT NOT NULL ,
    json TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    PRIMARY KEY ( id )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- Create the Sources Table
CREATE TABLE IF NOT EXISTS SC_Sources (
    id VARCHAR( 48 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    channelId VARCHAR( 48 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    date INT NOT NULL ,
    score INT NULL ,
    name VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    type  VARCHAR( 48 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    subType VARCHAR( 48 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    json TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    PRIMARY KEY ( id )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- Create the Content Table
CREATE TABLE IF NOT EXISTS SC_Content (
    id VARCHAR (48) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    sourceId VARCHAR( 48 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    state VARCHAR (48) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    date INT NOT NULL ,
    json TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    PRIMARY KEY ( id )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- Create the Tags Table
CREATE TABLE IF NOT EXISTS SC_Tags (
    id VARCHAR ( 48 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    type VARCHAR ( 48 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    text VARCHAR ( 256 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    PRIMARY KEY ( id )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- Create the Cotent_Tags
CREATE TABLE IF NOT EXISTS SC_Content_Tags (
    contentId VARCHAR (48) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    tagId VARCHAR (48) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
    PRIMARY KEY ( contentId, tagId )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;


-- *****************************************************************************
-- Channel Related Stored Procedures
-- *****************************************************************************

-- Create the GetChannelByChannelId stored procedure
DROP PROCEDURE IF EXISTS SC_GetChannelByChannelIds;
CREATE PROCEDURE SC_GetChannelByChannelIds (IN channelIdsAsInArray VARCHAR(48))
    BEGIN
        DECLARE text VARCHAR (256);
        SET text = CONCAT('SELECT json, active, inProcess FROM SC_Channels WHERE id in ', channelIdsAsInArray);
        SET @queryText = text;
        PREPARE query FROM @queryText;
        EXECUTE query;
    END;

-- Create the SaveChannel stored procedure
DROP PROCEDURE IF EXISTS SC_SaveChannel;
CREATE PROCEDURE SC_SaveChannel (
        IN channelId VARCHAR( 48 ),
        IN channelType VARCHAR( 48 ),
        IN channelSubType VARCHAR( 256 ),
        IN channelActive BIT( 1 ),
        IN channelInProcess BIT( 1 ),
        IN channelNextRun INT,
        IN channelJson TEXT)
    BEGIN
        DECLARE count INT DEFAULT 0;
        SET count = (SELECT count(id) FROM SC_Channels WHERE id = channelId);
        IF (count > 0) THEN
            UPDATE
                SC_Channels
            SET
                type = channelType,
                subType = channelSubType,
                active = channelActive,
                inProcess = channelInProcess,
                nextRun = channelNextRun,
                json = channelJson
            WHERE
                id = channelId;
        ELSE
            INSERT
                INTO SC_Channels
            VALUES (
                channelId,
                channelType,
                channelSubType,
                channelActive,
                channelInProcess,
                channelNextRun,
                channelJson);
        END IF;
    END;

-- Create the DeleteChannel stored procedure
DROP PROCEDURE IF EXISTS SC_DeleteChannel;
CREATE PROCEDURE SC_DeleteChannel (IN channelId VARCHAR (48))
    BEGIN
        DELETE FROM SC_Channels WHERE id = channelId;
    END;

-- Create the SelectNextDueChannel stored procedure
DROP PROCEDURE IF EXISTS SC_SelectNextDueChannel;
CREATE PROCEDURE SC_SelectNextDueChannel (IN dueBeforeTime INT)
    BEGIN
        SELECT
            json
        FROM
            SC_Channels
        WHERE
            nextRun <= dueBeforeTime
        AND
            active = 1
        AND
            inProcess = 0
        ORDER BY
            nextRun ASC
        LIMIT
            1;
    END;

-- Create the ListAllChannels Procedure
DROP PROCEDURE IF EXISTS SC_ListAllChannels;
CREATE PROCEDURE SC_ListAllChannels ()
    BEGIN
        SELECT
            id, type, subType, active, inProcess, nextRun, json
        FROM
            SC_Channels;
    END;


-- *****************************************************************************
-- Content Related Stored Procedures
-- *****************************************************************************

-- Create the SaveContent stored procedure
DROP PROCEDURE IF EXISTS SC_SaveContent;
CREATE PROCEDURE SC_SaveContent (
        contentId VARCHAR ( 48 ),
        contentSourceId VARCHAR ( 48 ),
        contentState VARCHAR ( 48 ),
        contentDate INT,
        contentJson TEXT)
    BEGIN
        DECLARE count INT DEFAULT 0;
        SET count = (SELECT count(id) FROM SC_Content WHERE id = contentId);
        IF (count > 0) THEN
            UPDATE
                SC_Content
            SET
                sourceId = contentSourceId,
                state = contentState,
                date = contentDate,
                json = contentJson
            WHERE
                id = contentId;
        ELSE
            INSERT
                INTO SC_Content
            VALUES (
                contentId,
                contentSourceId,
                contentState,
                contentDate,
                contentJson);
        END IF;
    END;

-- Create the GetContent stored procedure
DROP PROCEDURE IF EXISTS SC_GetContent;
CREATE PROCEDURE SC_GetContent (contentIdsAsInArray VARCHAR (2560))
    BEGIN
        SET @queryText = CONCAT('SELECT c.json as contentjson, s.json as sourcejson FROM SC_Content c JOIN SC_Sources s ON c.sourceId = s.id WHERE c.id in ', contentIdsAsInArray);
        SET @queryText = CONCAT(@queryText, ' order by c.date desc');
        PREPARE query FROM @queryText;
        EXECUTE query;
    END;

-- Create the DeleteContent stored procedure
DROP PROCEDURE IF EXISTS SC_DeleteContent;
CREATE PROCEDURE SC_DeleteContent (IN contentIdToDelete VARCHAR (48))
    BEGIN
        DELETE FROM SC_Content_Tags WHERE contentId = contentIdToDelete;
        DELETE FROM SC_Content WHERE id = contentIdToDelete;
    END;

-- *****************************************************************************
-- Source Related Stored Procedures
-- *****************************************************************************

-- Create the SaveSource Stored procedure
DROP PROCEDURE IF EXISTS SC_SaveSource;
CREATE PROCEDURE SC_SaveSource (
        IN sourceId VARCHAR ( 48 ),
        IN sourceChannelId VARCHAR ( 48 ),
        IN sourceDate INT,
        IN sourceScore INT,
        IN sourceName VARCHAR ( 256 ),
        IN sourceType VARCHAR ( 48 ),
        IN sourceSubType VARCHAR ( 48 ),
        IN sourceJson TEXT)
    BEGIN
        DECLARE count INT DEFAULT 0;
        SET count = (SELECT count(id) FROM SC_Sources WHERE id = sourceId);
        IF (count > 0) THEN
            UPDATE
                SC_Sources
            SET
                channelId = sourceChannelId,
                date = sourceDate,
                score = sourceScore,
                name = sourceName,
                type = sourceType,
                subType = sourceSubType,
                json = sourceJson
            WHERE
                id = sourceId;
        ELSE
            INSERT
                INTO SC_Sources
            VALUES (
                sourceId,
                sourceChannelId,
                sourceDate,
                sourceScore,
                sourceName,
                sourceType,
                sourceSubType,
                sourceJson);
        END IF;
    END;

-- *****************************************************************************
-- Tag Related Stored Procedures
-- *****************************************************************************

-- Create the AddTag stored procedure
DROP PROCEDURE IF EXISTS SC_AddTag;
CREATE PROCEDURE SC_AddTag (
        IN tagContentId VARCHAR ( 48 ),
        IN tagTagId VARCHAR ( 48 ),
        IN tagTagType VARCHAR ( 48 ),
        IN tagTagText VARCHAR ( 256 ))
    BEGIN
        DECLARE count INT DEFAULT 0;
        SET count = (SELECT COUNT(*) FROM SC_Tags WHERE id = tagTagId);
        IF ( count < 1 ) THEN
            INSERT
                INTO SC_Tags
            VALUES (
                tagTagId,
                tagTagType,
                tagTagText);
        END IF;
        SET count = (SELECT COUNT(*) FROM SC_Content_Tags WHERE contentId = tagContentId AND tagId = tagTagId);
        IF ( count < 1 ) THEN
            INSERT
                INTO SC_Content_Tags
            VALUES (
                tagContentId,
                tagTagId);
        END IF;
    END;

-- Create GetTags Stored Procedure
DROP PROCEDURE IF EXISTS SC_SelectTags;
CREATE PROCEDURE SC_SelectTags ( IN contentTagId VARCHAR ( 48 ) )
    BEGIN
        SELECT
            t.type, t.text
        FROM
            SC_Tags t JOIN SC_Content_Tags ct
                ON t.id = ct.tagId
        WHERE
            ct.contentId = contentTagId;
    END;

-- Create the Remove All Tags Procedure
DROP PROCEDURE IF EXISTS SC_RemoveAllTags;
CREATE PROCEDURE SC_RemoveAllTags ( IN contentTagId VARCHAR ( 48 ) )
    BEGIN
        DELETE FROM
            SC_Content_Tags
        WHERE
            contentId = contentTagId;
    END;

-- Create the Select Source stored procedure
DROP PROCEDURE IF EXISTS SC_GetSource;
CREATE PROCEDURE SC_GetSource ( IN sourceId VARCHAR ( 48 ) )
    BEGIN
        SELECT
            json
        FROM
            SC_Sources
        WHERE
            id = sourceId;
    END;

-- Create the Select All Source stored procedure
DROP PROCEDURE IF EXISTS SC_SelectAllSources;
CREATE PROCEDURE SC_SelectAllSources ()
    BEGIN
        SELECT
            json
        FROM
            SC_Sources;
    END;