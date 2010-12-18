<?php
namespace Swiftriver\Core\Modules\DataContext\MySql_V2;
/**
 * @author mg[at]swiftly[dot]org
 */
class DataContext implements
     \Swiftriver\Core\DAL\DataContextInterfaces\IAPIKeyDataContext,
     \Swiftriver\Core\DAL\DataContextInterfaces\IChannelDataContext,
     \Swiftriver\Core\DAL\DataContextInterfaces\IContentDataContext,
     \Swiftriver\Core\DAL\DataContextInterfaces\ISourceDataContext,
     \Swiftriver\Core\DAL\DataContextInterfaces\ITrustLogDataContext
{
    /**
     * Generic function used to gain a new PDO connection to
     * the database.
     *
     * @return \PDO
     */
    public static function PDOConnection()
    {
        $databaseUrl = (string) Setup::$Configuration->DataBaseUrl;

        $databaseName = (string) Setup::$Configuration->Database;

        $connectionString = "mysql:host=$databaseUrl;dbname=$databaseName";

        $username = (string) Setup::$Configuration->UserName;

        $password = (string) Setup::$Configuration->Password;

        $pdo = new \PDO($connectionString, $username, $password);

        return $pdo;
    }

    /**
     * Checks that the given API Key is registed for this
     * Core install
     * @param string $key
     * @return bool
     */
    public static function IsRegisterdCoreAPIKey($key)
    {

    }

    /**
     * Given a new APIKey, this method adds it to the
     * data store or registered API keys.
     * Returns true on sucess
     *
     * @param string $key
     * @return bool
     */
    public static function AddRegisteredCoreAPIKey($key)
    {

    }

    /**
     * Given an APIKey, this method will remove it from the
     * data store of registered API Keys
     * Returns true on sucess
     *
     * @param string key
     * @return bool
     */
    public static function RemoveRegisteredCoreAPIKey($key)
    {

    }

    /**
     * Given the IDs of Channels, this method
     * gets them from the underlying data store
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Channel[]
     */
    public static function GetChannelsById($ids)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [Method Invoked]", \PEAR_LOG_DEBUG);

        $channels = array();

        if(!\is_array($ids) || count($ids) < 1)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [No ids supplied]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [Method finished]", \PEAR_LOG_DEBUG);

            return $channels;
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [START: Building queries]", \PEAR_LOG_DEBUG);

        $sql = "CALL SC_GetChannelByChannelIds ( :ids )";

        $idsArray = "(";

        foreach($ids as $id)
            $idsArray .= "'$id',";

        $idsArray = \rtrim($idsArray, ",") . ")";

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [END: Building queries]", \PEAR_LOG_DEBUG);

        try
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [START: Connecting via PDO]", \PEAR_LOG_DEBUG);

            $db = self::PDOConnection();

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [END: Connecting via PDO]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [START: Preparing PDO statement]", \PEAR_LOG_DEBUG);
            
            $statement = $db->prepare($sql);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [END: Preparing PDO statement]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [START: Executing PDO statement]", \PEAR_LOG_DEBUG);

            $result = $statement->execute(array(":ids" => $idsArray));

            if($result === false)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                $errorInfo = $statement->errorInfo();

                $errorMessage = $errorInfo[2];

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [$errorMessage]", \PEAR_LOG_ERR);
            }

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [END: Executing PDO statement]", \PEAR_LOG_DEBUG);

            if(isset($result) && $result != null && $result !== 0)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [START: Looping over results]", \PEAR_LOG_DEBUG);

                foreach($statement->fetchAll() as $row)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [START: Constructing Channel Object from json]", \PEAR_LOG_DEBUG);

                    $json = $row['json'];

                    $channel = \Swiftriver\Core\ObjectModel\ObjectFactories\ChannelFactory::CreateChannelFromJSON($json);

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [END: Constructing Channel Object from json]", \PEAR_LOG_DEBUG);

                    $channels[] = $channel;
                }

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [END: Looping over results]", \PEAR_LOG_DEBUG);
            }

            $db = null;
        }
        catch (\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [An Exception was thrown:]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetChannelsById [Method Finished]", \PEAR_LOG_DEBUG);

        return $channels;
    }

    /**
     * Adds a list of new Channels to the data store
     *
     * @param \Swiftriver\Core\ObjectModel\Channel[] $Channels
     */
    public static function SaveChannels($channels)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [Method Invoked]", \PEAR_LOG_DEBUG);

        if(!\is_array($channels) || count($channels) < 1)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [No channels supplied]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [Method finished]", \PEAR_LOG_DEBUG);

            return;
        }

        $sql = "CALL SC_SaveChannel ( :id, :type, :subType, :active, :inProcess, :nextRun, :json)";

        try
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [START: Connecting to db via PDO]", \PEAR_LOG_DEBUG);

            $db = self::PDOConnection();

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [END: Connecting to db via PDO]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [START: Preparing PDO statment]", \PEAR_LOG_DEBUG);

            $statement = $db->prepare($sql);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [END: Preparing PDO statment]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [START: Looping through channels]", \PEAR_LOG_DEBUG);

            foreach($channels as $channel)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [START: Executing PDO statement for channel]", \PEAR_LOG_DEBUG);

                $parameters = array (
                    "id" => $channel->id,
                    "type" => $channel->type,
                    "subType" => $channel->subType,
                    "active" => $channel->active,
                    "inProcess" => $channel->inprocess,
                    "nextRun" => $channel->nextrun,
                    "json" => \json_encode($channel));

                $result = $statement->execute($parameters);

                if($result === false)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                    $errorInfo = $statement->errorInfo();

                    $errorMessage = $errorInfo[2];

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [$errorMessage]", \PEAR_LOG_ERR);
                }

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [END: Executing PDO statement for channel]", \PEAR_LOG_DEBUG);
            }

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [END: Looping through channels]", \PEAR_LOG_DEBUG);

            $db = null;
        }
        catch(\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [An exception was thrown]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveChannels [Method Finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given a list of IDs this method removes the Channels from
     * the data store.
     *
     * @param string[] $ids
     */
    public static function RemoveChannels($ids)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [Method Invoked]", \PEAR_LOG_DEBUG);

        if(!\is_array($ids) || count($ids) < 1)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [No ids supplied]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [Method finished]", \PEAR_LOG_DEBUG);

            return;
        }

        $sql = "CALL SC_DeleteChannel ( :id )";

        try
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [START: Connecting to db via PDO]", \PEAR_LOG_DEBUG);

            $db = self::PDOConnection();

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [END: Connecting to db via PDO]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [START: Preparing PDO statment]", \PEAR_LOG_DEBUG);

            $statement = $db->prepare($sql);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [END: Preparing PDO statment]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [START: Looping through ids]", \PEAR_LOG_DEBUG);

            foreach($ids as $id)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [START: Executing PDO statement for channel]", \PEAR_LOG_DEBUG);

                $result = $statement->execute(array("id" => $id));

                if($result === false)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                    $errorInfo = $statement->errorInfo();

                    $errorMessage = $errorInfo[2];

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [$errorMessage]", \PEAR_LOG_ERR);
                }


                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [END: Executing PDO statement for channel]", \PEAR_LOG_DEBUG);
            }

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [END: Looping through ids]", \PEAR_LOG_DEBUG);

            $db = null;
        }
        catch(\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [An exception was thrown]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::RemoveChannels [Method Finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given a date time, this function returns the next due
     * Channel.
     *
     * @param DateTime $time
     * @return \Swiftriver\Core\ObjectModel\Channel
     */
    public static function SelectNextDueChannel($time)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [Method Invoked]", \PEAR_LOG_DEBUG);

        $channel = null;

        if(!isset($time) || $time == null)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [No time supplied, setting time to now]", \PEAR_LOG_DEBUG);

            $time = time();
        }

        $sql = "CALL SC_SelectNextDueChannel ( :time )";

        try
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [START: Connecting to db via PDO]", \PEAR_LOG_DEBUG);

            $db = self::PDOConnection();

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [END: Connecting to db via PDO]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [START: Preparing PDO statment]", \PEAR_LOG_DEBUG);

            $statement = $db->prepare($sql);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [END: Preparing PDO statment]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [START: Executing PDO statment]", \PEAR_LOG_DEBUG);

            $result = $statement->execute(array("time" => $time));

            if($result === false)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                $errorInfo = $statement->errorInfo();

                $errorMessage = $errorInfo[2];

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [$errorMessage]", \PEAR_LOG_ERR);
            }

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [END: Executing PDO statment]", \PEAR_LOG_DEBUG);

            if(isset($result) && $result != null && $result !== 0)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [START: Looping over results]", \PEAR_LOG_DEBUG);

                foreach($statement->fetchAll() as $row)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [START: Constructing Channel Object from json]", \PEAR_LOG_DEBUG);

                    $json = $row['json'];

                    $channel = \Swiftriver\Core\ObjectModel\ObjectFactories\ChannelFactory::CreateChannelFromJSON($json);

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [END: Constructing Channel Object from json]", \PEAR_LOG_DEBUG);

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [START: Marking channel as in process]", \PEAR_LOG_DEBUG);

                    $channel->inprocess = true;

                    self::SaveChannels(array($channel));

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [END: Marking channel as in process]", \PEAR_LOG_DEBUG);
                }

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [END: Looping over results]", \PEAR_LOG_DEBUG);
            }

            $db = null;
        }
        catch(\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [An exception was thrown]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SelectNextDueChannel [Method Finished]", \PEAR_LOG_DEBUG);

        return $channel;
    }

    /**
     * Lists all the current Channel in the core
     * @return \Swiftriver\Core\ObjectModel\Channel[]
     */
    public static function ListAllChannels()
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [Method Invoked]", \PEAR_LOG_DEBUG);

        $channels = array();

        $sql = "CALL SC_ListAllChannels()";

        try
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [START: Connecting via PDO]", \PEAR_LOG_DEBUG);

            $db = self::PDOConnection();

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [END: Connecting via PDO]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [START: Preparing PDO statement]", \PEAR_LOG_DEBUG);

            $statement = $db->prepare($sql);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [END: Preparing PDO statement]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [START: Executing PDO statement]", \PEAR_LOG_DEBUG);

            $result = $statement->execute();

            if($result === false)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                $errorInfo = $statement->errorInfo();

                $errorMessage = $errorInfo[2];

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [$errorMessage]", \PEAR_LOG_ERR);
            }

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [END: Executing PDO statement]", \PEAR_LOG_DEBUG);

            if(isset($result) && $result != null && $result !== 0)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [START: Looping over results]", \PEAR_LOG_DEBUG);

                foreach($statement->fetchAll() as $row)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [START: Constructing Channel Object from json]", \PEAR_LOG_DEBUG);

                    $json = $row['json'];

                    $channel = \Swiftriver\Core\ObjectModel\ObjectFactories\ChannelFactory::CreateChannelFromJSON($json);

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [END: Constructing Channel Object from json]", \PEAR_LOG_DEBUG);

                    $channels[] = $channel;
                }

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [END: Looping over results]", \PEAR_LOG_DEBUG);
            }

            $db = null;
        }
        catch (\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [An Exception was thrown:]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [Method Finished]", \PEAR_LOG_DEBUG);

        return $channels;
    }

    /**
     * Given a set of content items, this method will persist
     * them to the data store, if they already exists then this
     * method should update the values in the data store.
     *
     * @param \Swiftriver\Core\ObjectModel\Content[] $content
     */
    public static function SaveContent($content)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [Method Invoked]", \PEAR_LOG_DEBUG);

        if( !\is_array($content) || \count($content) < 1 )
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [No Content Supplied]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [Mrethod Finished]", \PEAR_LOG_DEBUG);

            return;
        }

        $saveContentSql = "CALL SC_SaveContent ( :id, :sourceId, :state, :date, :json )";

        $saveSourceSql = "CALL SC_SaveSource ( :id, :channelId, :date, :score, :name, :type, :subType, :json )";

        $saveTagSql = "CALL SC_AddTag ( :contentId, :tagId, :tagType, :tagText )";

        $removeTagsSql = "CALL SC_RemoveAllTags ( :id )";

        try
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [START: Connecting to the DB via PDO]", \PEAR_LOG_DEBUG);

            $db = self::PDOConnection();

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [END: Connecting to the DB via PDO]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [START: Preparing PDO Statements]", \PEAR_LOG_DEBUG);

            $contentStatement = $db->prepare($saveContentSql);

            $sourceStatement = $db->prepare($saveSourceSql);

            $tagStatement = $db->prepare($saveTagSql);

            $removeTagsStatement = $db->prepare($removeTagsSql);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [END: Preparing PDO Statements]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [START: Looping through content]", \PEAR_LOG_DEBUG);

            foreach($content as $item)
            {
                $source = $item->source;

                $sourceParams = array (
                    "id" => $source->id,
                    "channelId" => $source->parent,
                    "date" => $source->date,
                    "score" => $source->score,
                    "name" => $source->name,
                    "type" => $source->type,
                    "subType" => $source->subType,
                    "json" => \json_encode($source));

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [START: Saving content source]", \PEAR_LOG_DEBUG);

                $result = $sourceStatement->execute($sourceParams);

                if($result === false)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                    $errorInfo = $sourceStatement->errorInfo();

                    $errorMessage = $errorInfo[2];

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [$errorMessage]", \PEAR_LOG_ERR);
                }

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [END: Saving content source]", \PEAR_LOG_DEBUG);

                $contentParams = array (
                    "id" => $item->id,
                    "sourceId" => $source->id,
                    "state" => $item->state,
                    "date" => $item->date,
                    "json" => \json_encode($item));

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [START: Saving content]", \PEAR_LOG_DEBUG);

                $result = $contentStatement->execute($contentParams);

                if($result === false)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                    $errorInfo = $contentStatement->errorInfo();

                    $errorMessage = $errorInfo[2];

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [$errorMessage]", \PEAR_LOG_ERR);
                }

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [END: Saving content]", \PEAR_LOG_DEBUG);

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [START: Looping through content tags]", \PEAR_LOG_DEBUG);

                if(is_array($item->tags) && count($item->tags) > 0)
                {
                    $removeTagsStatement->execute(array("id" => $item->id));

                    foreach($item->tags as $tag)
                    {
                        $tagParams = array (
                            "contentId" => $item->id,
                            "tagId" => \md5(\strtolower($tag->text)),
                            "tagType" => $tag->type,
                            "tagText" => \strtolower($tag->text));

                        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [START: Saving Tag]", \PEAR_LOG_DEBUG);

                        $result = $tagStatement->execute($tagParams);

                        if($result === false)
                        {
                            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                            $errorInfo = $tagStatement->errorInfo();

                            $errorMessage = $errorInfo[2];

                            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [$errorMessage]", \PEAR_LOG_ERR);
                        }

                        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [END: Saving Tag]", \PEAR_LOG_DEBUG);
                    }
                }

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [END: Looping through content tags]", \PEAR_LOG_DEBUG);
            }

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [END: Looping through content]", \PEAR_LOG_DEBUG);

            $db = null;
        }
        catch (\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [An Exception was thrown:]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllChannels [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::SaveContent [Method Finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given an array of content is's, this function will
     * fetch the content objects from the data store.
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    public static function GetContent($ids, $orderby = null)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [Method Invoked]", \PEAR_LOG_DEBUG);

        $content = array();

        if(!\is_array($ids) || \count($ids) < 1)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [No Ids supplied]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [Method Finished]", \PEAR_LOG_DEBUG);

            return $content;
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [START: Building SQL Statment]", \PEAR_LOG_DEBUG);

        $getContentSql = "CALL SC_GetContent( :ids )";

        $idsArray = "(";

        foreach($ids as $id)
            $idsArray .= "'$id',";

        $idsArray = \rtrim($idsArray, ",") . ")";

        $getTagsSql = "CALL SC_SelectTags ( :id )";

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [END: Building SQL Statment]", \PEAR_LOG_DEBUG);

        try
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [START: Connecting via PDO]", \PEAR_LOG_DEBUG);

            $db = self::PDOConnection();

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [END: Connecting via PDO]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [START: Preparing PDO statements]", \PEAR_LOG_DEBUG);

            $getContentStatement = $db->prepare($getContentSql);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [END: Preparing PDO statements]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [START: Executing PDO statement]", \PEAR_LOG_DEBUG);

            $result = $getContentStatement->execute(array(":ids" => $idsArray));

            if($result === false)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                $errorInfo = $getContentStatement->errorInfo();

                $errorMessage = $errorInfo[2];

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [$errorMessage]", \PEAR_LOG_ERR);
            }

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [END: Executing PDO statement]", \PEAR_LOG_DEBUG);

            if(isset($result) && $result != null && $result !== 0)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [START: Looping over results]", \PEAR_LOG_DEBUG);

                foreach($getContentStatement->fetchAll() as $row)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [START: Constructing content Object from json]", \PEAR_LOG_DEBUG);

                    $sourcejson = $row["sourcejson"];
                    
                    $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromJSON($sourcejson);

                    $contentjson = $row["contentjson"];

                    $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source, $contentjson);

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [END: Constructing content Object from json]", \PEAR_LOG_DEBUG);

                    $content[] = $item;
                }

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [END: Looping over results]", \PEAR_LOG_DEBUG);
            }

            $db = null;

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [START: Getting Content Tags]", \PEAR_LOG_DEBUG);

            foreach($content as $item)
            {
                $db = self::PDOConnection();

                $getTagsStatement = $db->prepare($getTagsSql);

                $result = $getTagsStatement->execute(array("id" => $item->id));

                if($result === false)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                    $errorInfo = $getTagsStatement->errorInfo();

                    $errorMessage = $errorInfo[2];

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [$errorMessage]", \PEAR_LOG_ERR);
                }

                if(isset($result) && $result != null && $result !== 0)
                {
                    $item->tags = array();

                    foreach($getTagsStatement->fetchAll() as $row)
                        $item->tags[] = new \Swiftriver\Core\ObjectModel\Tag($row["text"], $row["type"]);
                }

                $db = null;
            }
            
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [START: Getting Content Tags]", \PEAR_LOG_DEBUG);
        }
        catch (\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [An Exception was thrown:]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContent [Method Finished]", \PEAR_LOG_DEBUG);

        return $content;
    }

    /**
     *
     * @param string[] $parameters
     */
    public static function GetContentList($parameters)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySql_V2::DataContext::GetContentList [Method invoked]", \PEAR_LOG_DEBUG);

        $baseSql = "from SC_Content content left join SC_Sources source on content.sourceId = source.id";

        $filters = array();

        $state = (key_exists("state", $parameters)) ? $parameters["state"] : null;
        if($state != null)
            $filters[] = "content.state = '$state'";

        $minVeracity = (key_exists("minVeracity", $parameters)) ? $parameters["minVeracity"] : null;
        if($minVeracity != null || $minVeracity === 0)
            $filters[] = ($minVeracity === 0)
                ? "(source.score >= $minVeracity OR source.score IS NULL)"
                : "source.score >= $minVeracity";

        $maxVeracity = (key_exists("maxVeracity", $parameters)) ? $parameters["maxVeracity"] : null;
        if($maxVeracity != null)
            $filters[] = ($minVeracity === 0)
                ? "(source.score <= $maxVeracity OR source.score IS NULL)"
                : "source.score <= $maxVeracity";

        $type = (key_exists("type", $parameters)) ? $parameters["type"] : null;
        if($type != null)
            $filters[] = "source.type = '$type'";

        $subType = (key_exists("subType", $parameters)) ? $parameters["subType"] : null;
        if($subType != null)
            $filters[] = "source.subType = '$subType'";

        $source = (key_exists("source", $parameters)) ? $parameters["source"] : null;
        if($source != null)
            $filters[] = "source.id = '$source'";

        $tag = (\key_exists("tag", $parameters)) ? $parameters["tag"] : null;
        if($tag != null)
            $filters[] = "content.id in (select ct.contentId from SC_Content_Tags ct join SC_Tags t on ct.tagId = t.id where t.text = '$tag')";

        $pageSize = (key_exists("pageSize", $parameters)) ? $parameters["pageSize"] : null;

        $pageStart = (key_exists("pageStart", $parameters)) ? $parameters["pageStart"] : null;

        $pagination = ($pageSize != null)
            ? "limit " . (($pageStart == null) ? "0" : $pageStart) . ", $pageSize"
            : "";


        $orderBy = "date desc";

        $sql = $baseSql;
        for($i = 0; $i < count($filters); $i++) {
            $addition = ($i == 0) ? "WHERE" : "AND";
            $sql .= " " . $addition . " " . $filters[$i];
        }

        $countSql = "select count(content.id) " . $sql;

        try
        {
            $db = self::PDOConnection();

            $countStatement = $db->prepare($countSql);

            $result = $countStatement->execute();

            if($result === false)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContentList [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                $errorInfo = $countStatement->errorInfo();

                $errorMessage = $errorInfo[2];

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContentList [$errorMessage]", \PEAR_LOG_ERR);
            }

            $totalCount = (int) $countStatement->fetchColumn();

            $selectSql = "select content.id " . $sql . " order by content." . $orderBy . " " . $pagination;

            $navigation = array();

            if($tag == null)
            {
                $tagsSql = "SELECT t.text as name, t.text as id, count(t.text) as count FROM SC_Tags t join SC_Content_Tags ct ON t.id = ct.tagId WHERE ct.contentId in (SELECT content.id " . $sql . ") GROUP BY t.text ORDER BY count DESC";
                $tagsStatement = $db->prepare($tagsSql);
                $tagsStatement->execute();
                $results = $tagsStatement->fetchAll(\PDO::FETCH_ASSOC);
                $types = array(
                    "type" => "list",
                    "key" => "tags",
                    "selected" => $type != null,
                    "facets" => $results);
                $navigation["Tags"] = $types;
            }


            if($subType == null)
            {
                $typeSql = "select source.type as name, source.type as id, count(source.type) as count " . $sql . " group by source.type order by count desc";
                $typeStatement = $db->prepare($typeSql);
                $typeStatement->execute();
                $results = $typeStatement->fetchAll(\PDO::FETCH_ASSOC);
                $types = array(
                    "type" => "list",
                    "key" => "type",
                    "selected" => $type != null,
                    "facets" => $results);
                $navigation["Channels"] = $types;
            }

            if($type != null && $source == null)
            {
                $subTypeSql = "select source.subType as name, source.subType as id, count(source.subType) as count " . $sql . " group by source.subType order by count desc";
                $subTypeStatement = $db->prepare($subTypeSql);
                $subTypeStatement->execute();
                $results = $subTypeStatement->fetchAll(\PDO::FETCH_ASSOC);
                $subTypes = array(
                    "type" => "list",
                    "key" => "subType",
                    "selected" => $subType != null,
                    "facets" => $results);
                $navigation["Sub Channels"] = $subTypes;
            }

            if($subType != null && $type != null)
            {
                $sourceSql = "select source.name as name, source.textId as id, count(source.name) as count " .$sql . " group by source.name order by count desc";
                $sourceStatement = $db->prepare($sourceSql);
                $sourceStatement->execute();
                $results = $sourceStatement->fetchAll(\PDO::FETCH_ASSOC);
                $sources = array(
                    "type" => "list",
                    "key" => "source",
                    "selected" => $source != null,
                    "facets" => $results);
                $navigation["Sources"] = $sources;
            }

            $ids = array();

            foreach($db->query($selectSql) as $row)
                $ids[] = $row[0];

            $content = self::GetContent($ids, $orderBy);
        }
        catch (\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContentList [An Exception was thrown:]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetContentList [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySql_V2::DataContext::GetContentList [Method finished]", \PEAR_LOG_DEBUG);

        return array (
            "totalCount" => $totalCount,
            "contentItems" => $content,
            "navigation" => $navigation
        );
    }

    /**
     * Given an array of content items, this method removes them
     * from the data store.
     * @param \Swiftriver\Core\ObjectModel\Content[] $content
     */
    public static function DeleteContent($content)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [Method Invoked]", \PEAR_LOG_DEBUG);

        if (!\is_array($content) || \count($content) < 1)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [No content provided]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [Method Finished]", \PEAR_LOG_DEBUG);

            return;
        }

        $deleteContentSql = "CALL SC_DeleteContent( :id )";

        try
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [START: COnnecting to the db via PDO]", \PEAR_LOG_DEBUG);

            $db = self::PDOConnection();

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [END: COnnecting to the db via PDO]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [START: Preparing PDO statement]", \PEAR_LOG_DEBUG);

            $deleteContentStatement = $db->prepare($deleteContentSql);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [END: Preparing PDO statement]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [START: Looping through content]", \PEAR_LOG_DEBUG);

            foreach ($content as $item)
            {
                $result = $deleteContentStatement->execute(array("id" => $item->id));

                if($result === false)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                    $errorInfo = $deleteContentStatement->errorInfo();

                    $errorMessage = $errorInfo[2];

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [$errorMessage]", \PEAR_LOG_ERR);
                }
            }

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [END: Looping through content]", \PEAR_LOG_DEBUG);
        }
        catch (\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [An Exception was thrown:]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [Method Finished]", \PEAR_LOG_DEBUG);

        $db = null;
    }

    /**
     * Given the IDs of Sources, this method
     * gets them from the underlying data store
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public static function GetSourcesById($ids)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [Method Invoked]", \PEAR_LOG_DEBUG);

        $sources = array();

        if (!\is_array($ids) || \count($ids) < 1)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [No ids supplied]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [Method Finished]", \PEAR_LOG_DEBUG);

            return $sources;
        }

        $getSourceSql = "CALL SC_GetSource( :id )";

        try
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [START: Looping through ids]", \PEAR_LOG_DEBUG);

            foreach($ids as $id)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [START: Connecting to the db via PDO]", \PEAR_LOG_DEBUG);

                $db = self::PDOConnection();

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [END: Connecting to the db via PDO]", \PEAR_LOG_DEBUG);

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [START: Preparing PDO statement]", \PEAR_LOG_DEBUG);

                $getSourceStatement = $db->prepare($getSourceSql);

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [END: Preparing PDO statement]", \PEAR_LOG_DEBUG);

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [START: Executing PDO statement]", \PEAR_LOG_DEBUG);

                $result = $getSourceStatement->execute(array("id" => $id));

                if($result === false)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                    $errorInfo = $getSourceStatement->errorInfo();

                    $errorMessage = $errorInfo[2];

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [$errorMessage]", \PEAR_LOG_ERR);
                }

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [END: Executing PDO statement]", \PEAR_LOG_DEBUG);

                foreach($getSourceStatement->fetchAll() as $row)
                {
                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [START: Constructing source obejct]", \PEAR_LOG_DEBUG);

                    $json = $row["json"];

                    $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromJSON($json);

                    $sources[] = $source;

                    $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [END: Constructing source obejct]", \PEAR_LOG_DEBUG);
                }

                $db = null;
            }

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [END: Looping through ids]", \PEAR_LOG_DEBUG);
        }
        catch (\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [An Exception was thrown:]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::DeleteContent [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::GetSourcesById [Method Finished]", \PEAR_LOG_DEBUG);

        return $sources;
    }

    /**
     * Lists all the current Source in the core
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public static function ListAllSources()
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllSources [Method initiated]", \PEAR_LOG_DEBUG);

        $sources = array();

        $selectAllSourcesSql = "CALL SC_SelectAllSources ()";

        try
        {
            $db = self::PDOConnection();

            $selectAllSourcesStatment = $db->prepare($selectAllSourcesSql);

            $result = $selectAllSourcesStatment->execute();

            if($result === false)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllSources [An Exception was thrown by the PDO framwork]", \PEAR_LOG_ERR);

                $errorInfo = $selectAllSourcesStatment->errorInfo();

                $errorMessage = $errorInfo[2];

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllSources [$errorMessage]", \PEAR_LOG_ERR);
            }

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllSources [END: Executing PDO statement]", \PEAR_LOG_DEBUG);

            foreach($selectAllSourcesStatment->fetchAll() as $row)
            {
                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllSources [START: Constructing source obejct]", \PEAR_LOG_DEBUG);

                $json = $row["json"];

                $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromJSON($json);

                $sources[] = $source;

                $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllSources [END: Constructing source obejct]", \PEAR_LOG_DEBUG);
            }

            $db = null;
        }
        catch (\PDOException $e)
        {
            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllSources [An Exception was thrown:]", \PEAR_LOG_ERR);

            $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllSources [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V2::DataContext::ListAllSources [Method finished]", \PEAR_LOG_DEBUG);

        return $sources;
    }

    /**
     * This method redords the fact that a marker (sweeper) has changed the score
     * of a source by marking a content items as either 'acurate', 'chatter' or
     * 'inacurate'
     *
     * @param string $sourceId
     * @param string $markerId
     * @param string|null $reason
     * @param int $change
     */
    public static function RecordSourceScoreChange($sourceId, $markerId, $change, $reason = null)
    {
        //This function is no loger supported.
        return;
    }
}
?>
