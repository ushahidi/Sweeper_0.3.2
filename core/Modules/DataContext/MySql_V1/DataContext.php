<?php
namespace Swiftriver\Core\Modules\DataContext\MySql_V1;
class DataContext implements
     \Swiftriver\Core\DAL\DataContextInterfaces\IAPIKeyDataContext,
     \Swiftriver\Core\DAL\DataContextInterfaces\IChannelDataContext,
     \Swiftriver\Core\DAL\DataContextInterfaces\IContentDataContext,
     \Swiftriver\Core\DAL\DataContextInterfaces\ISourceDataContext,
     \Swiftriver\Core\DAL\DataContextInterfaces\ITrustLogDataContext
{
    /**
     * Checks that the given API Key is registed for this
     * Core install
     *
     * Inhereted from IAPIKeyDataContext
     *
     * @param string $key
     * @return bool
     */
    public static function IsRegisterdCoreAPIKey($key) {
        if(!isset($key) || $key == "")
            return null;
        $query = "SELECT COUNT(*) FROM coreapikeys WHERE apikey = '".$key."';";
        $result = self::RunQuery($query);
        if(!isset($result) || $result == false)
            return null;
        $count = mysql_result($result, 0);
        return $count > 0;
    }

    /**
     * Given a new APIKey, this method adds it to the
     * data store or registered API keys.
     * @param string $key
     * @return bool
     */
    public static function AddRegisteredCoreAPIKey($key) {
        if(!isset($key) || $key == "")
            return false;
        if(self::IsRegisterdCoreAPIKey($key))
            return true;
        $query = "INSERT INTO coreapikeys VALUES('".$key."');";
        $result = self::RunQuery($query);
        return $result;
    }

    /**
     * Given an APIKey, this method will remove it from the
     * data store of registered API Keys
     * Returns true on sucess
     *
     * @param string key
     * @return bool
     */
    public static function RemoveRegisteredCoreAPIKey($key) {
        if(!isset($key) || $key == "")
            return true;
        if(!self::IsRegisterdCoreAPIKey($key))
            return true;
        $query = "DELETE FROM coreapikeys WHERE apikey = '".$key."';";
        $result = self::RunQuery($query);
        return $result;
    }

    /**
     * Given the IDs of Channels, this method
     * gets them from the underlying data store
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Channel[]
     */
    public static function GetChannelsById($ids){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetChannelsById [Method invoked]", \PEAR_LOG_DEBUG);

        if(!isset($ids) || $ids == null || !is_array($ids) || count($ids) < 1) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetChannelsById [No IDs supplied]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetChannelsById [Method finished]", \PEAR_LOG_DEBUG);
            return array();
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetChannelsById [START: Getting the Channels from the data store]", \PEAR_LOG_DEBUG);

        $rb = RedBeanController::RedBean();

        $idsQuery = "(";
        foreach($ids as $id)
            $idsQuery .= "'$id',";
        $idsQuery = rtrim($idsQuery, ",").")";

        $dbResults = RedBeanController::Finder()->where("channel", "textId in $idsQuery", array());

        if(!$dbResults || $dbResults == null || !is_array($dbResults) || count($dbResults) < 1) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetChannelsById [No sourcse found matching any of the IDs supplied]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetChannelsById [Method finished]", \PEAR_LOG_DEBUG);
            return array();
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetChannelsById [END: Getting the Channels from the data store]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetChannelsById [START: Parsing the data store objects]", \PEAR_LOG_DEBUG);

        $channels = array();

        foreach($dbResults as $dbResult) {

            $json = $dbResult->json;

            try {
                $channels[] = \Swiftriver\Core\ObjectModel\ObjectFactories\ChannelFactory::CreateChannelFromJSON($json);
            }
            catch (\Exception $e) {
                $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetChannelsById [An exception was thrown: $e]", \PEAR_LOG_ERR);
                $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetChannelsById [No Channel could be constructed from the DB content]", \PEAR_LOG_DEBUG);
                continue;
            }
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetChannelsById [END: Parsing the data store objects]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetChannelsById [Method finished]", \PEAR_LOG_DEBUG);

        return $channels;
    }

    /**
     * Adds a list of new Channels to the data store
     *
     * @param \Swiftriver\Core\ObjectModel\Channel[] $channels
     */
    public static function SaveChannels($channels){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveChannels [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveChannels [START: Looping throught Channels]", \PEAR_LOG_DEBUG);

        foreach($channels as $channel) {

            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveChannels [START: Looking for exosting Channel with this ID]", \PEAR_LOG_DEBUG);

            //get the Channel from the db
            $rb = RedBeanController::RedBean();
            $c = reset(RedBeanController::Finder()->where("channel", "textId = :id", array(":id" => $channel->id)));

            //If the Channel does not exists, create it.
            if(!$c || $c == null) {
                $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveChannels [No Channel found so creating one]", \PEAR_LOG_DEBUG);
                //create the new data store object for the Channel
                $c = $rb->dispense("channel");
            }

            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveChannels [END: Looking for exosting Channel with this ID]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveChannels [START: Adding required properties]", \PEAR_LOG_DEBUG);

            //Add properties we want specifically in the table
            $c = DataContext::AddPropertiesToDataSoreItem(
                    $c,
                    $channel,
                    array(
                        "textId" => "id",
                        "type" => "type",
                        "subType" => "subType",
                        "active" => "active",
                        "inprocess" => "inprocess",
                        "nextrun" => "nextrun",
                        "deleted" => "deleted",
                    ));

            //add the encoded Channel object to the data sotre object
            $c->json = json_encode($channel);

            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveChannels [END: Adding required properties]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveChannels [START: Saving the Channel]", \PEAR_LOG_DEBUG);

            //save the Channel
            $rb->store($c);

            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveChannels [END: Saving the Channel]", \PEAR_LOG_DEBUG);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveChannels [START: Looping throught Channels]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveChannels [Method finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given a list of IDs this method removes the Channels from
     * the data store.
     *
     * @param string[] $ids
     */
    public static function RemoveChannels($ids){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RemoveChannels [Method invoked]", \PEAR_LOG_DEBUG);

        if(!isset($ids) || $ids == null || !is_array($ids) || count($ids) < 1) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RemoveChannels [No IDs supplied]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RemoveChannels [Method finished]", \PEAR_LOG_DEBUG);
            return array();
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RemoveChannels [START: Looping through IDs ]", \PEAR_LOG_DEBUG);

        $rb = RedBeanController::RedBean();

        $channels = array();

        foreach($ids as $id) {
            //get the Channel from the db
            $c = reset(RedBeanController::Finder()->where("channel", "textId = :id", array(":id" => $id)));

            //If the Channel does not exists, skip it.
            if(!$c || $c == null) {
                continue;
            }

            $json = $c->json;
            $channel = \Swiftriver\Core\ObjectModel\ObjectFactories\ChannelFactory::CreateChannelFromJSON($json);
            $channel->deleted = true;

            $channels[] = $channel;
        }

        DataContext::SaveChannels($channels);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RemoveChannels [END: Looping through IDs ]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RemoveChannels [Method finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given a date time, this function returns the next due
     * Channel.
     *
     * @param DateTime $time
     * @return \Swiftriver\Core\ObjectModel\Channel
     */
    public static function SelectNextDueChannel($time){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [Method invoked]", \PEAR_LOG_DEBUG);

        //if the time is not set, set it to now
        if(!isset($time) || $time == null || !is_numeric($time)) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [The time variable was not set, setting to now]", \PEAR_LOG_DEBUG);
            $time = time();
        }

        //Get the redbean
        $rb = RedBeanController::RedBean();

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [START: Selecting data object from the data store]", \PEAR_LOG_DEBUG);

        //select the next due processing job
        $s = reset(RedBeanController::Finder()->where(
                "channel",
                "active = 1 and inprocess != 1 and deleted != 1 order by nextrun asc limit 1"
                ));

        //check if there is anythign to return
        if(!isset($s) || $s == null) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [Nothing returned from the data store]", \PEAR_LOG_DEBUG);
            return null;
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [END: Selecting data object from the data store]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [START: Constructing a Channel object fro mthe data object]", \PEAR_LOG_DEBUG);

        //Get the json from that data object
        $json = $s->json;

        try {
            //Build a new Channel from the json
            $channel = \Swiftriver\Core\ObjectModel\ObjectFactories\ChannelFactory::CreateChannelFromJSON($json);
        }
        catch(\Exception $e) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [An exception was thrown: $e]", \PEAR_LOG_ERR);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [The data object could not be converted into a Channel object]", \PEAR_LOG_DEBUG);
            return null;
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [END: Constructing a Channel object fro mthe data object]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [START: Setting properties to identify the Channel as in progress]", \PEAR_LOG_DEBUG);

        //set the nextrun
        $nextrun = time() + ($channel->updatePeriod * 60);

        //update the proerties that show the channel has been run
        $channel->nextrun = $nextrun;
        $channel->timesrun = $channel->timesrun + 1;
        $channel->lastrun = time();
        $channel->inprocess = true;

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [END: Setting properties to identify the Channel as in progress]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [START: Saving the Channel state]", \PEAR_LOG_DEBUG);

        DataContext::SaveChannels(array($channel));

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [END: Saving the Channel state]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SelectNextDueChannel [Method finished]", \PEAR_LOG_DEBUG);

        //return the Channel
        return $channel;
    }

    /**
     * Lists all the current Channel in the core
     * @return \Swiftriver\Core\ObjectModel\Channel[]
     */
    public static function ListAllChannels(){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllChannels [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllChannels [START: Querying the data store]", \PEAR_LOG_DEBUG);

        try {
            //Attempt to query the data store
            $dbResults = RedBeanController::Finder()->where("channel", "deleted != 1");

            //If the db returns noting then throw an exception
            if(!$dbResults || $dbResults == null || !is_array($dbResults) || count($dbResults) < 1) {
                throw new \Exception("No sources returned from the data store, it may be because none have been added?");
            }
        }
        catch(\Exception $e) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllChannels [An exception was thrown, returning empty array]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllChannels [$e]", \PEAR_LOG_ERR);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllChannels [Method finished]", \PEAR_LOG_DEBUG);
            return array();
        }



        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllChannels [END: Querying the data store]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllChannels [START: Creating Channel objects]", \PEAR_LOG_DEBUG);

        $channels = array();

        foreach($dbResults as $dbResult) {

            $json = $dbResult->json;

            try {
                $channels[] = \Swiftriver\Core\ObjectModel\ObjectFactories\ChannelFactory::CreateChannelFromJSON($json);
            }
            catch (\Exception $e) {
                $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllChannels [An exception was thrown: $e]", \PEAR_LOG_ERR);
                $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllChannels [No Channel could be constructed from the DB content]", \PEAR_LOG_DEBUG);
                continue;
            }
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllChannels [END: Creating Channel objects]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllChannels [Method finished]", \PEAR_LOG_DEBUG);

        return $channels;
    }

    /**
     * Lists all the current Source in the core
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public static function ListAllSources(){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [START: Querying the data store]", \PEAR_LOG_DEBUG);

        $dbResults = RedBeanController::Finder()->where("source", "deleted != 1");

        if(!$dbResults || $dbResults == null || !is_array($dbResults) || count($dbResults) < 1) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [No sourcse found]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [Method finished]", \PEAR_LOG_DEBUG);
            return array();
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [END: Querying the data store]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [START: Creating source objects]", \PEAR_LOG_DEBUG);

        $sources = array();

        foreach($dbResults as $dbResult) {

            $json = $dbResult->json;

            try {
                $sources[] = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromJSON($json);
            }
            catch (\Exception $e) {
                $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [An exception was thrown: $e]", \PEAR_LOG_ERR);
                $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [No source could be constructed from the DB content]", \PEAR_LOG_DEBUG);
                continue;
            }
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [END: Creating source objects]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::ListAllSources [Method finished]", \PEAR_LOG_DEBUG);

        return $sources;
    }

    /**
     * Given the IDs of Sources, this method
     * gets them from the underlying data store
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public static function GetSourcesById($ids){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [Method invoked]", \PEAR_LOG_DEBUG);

        if(!isset($ids) || $ids == null || !is_array($ids) || count($ids) < 1) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [No IDs supplied]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [Method finished]", \PEAR_LOG_DEBUG);
            return array();
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [START: Getting the sources from the data store]", \PEAR_LOG_DEBUG);

        $rb = RedBeanController::RedBean();

        $idsQuery = "(";
        foreach($ids as $id)
            $idsQuery .= "'$id',";
        $idsQuery = rtrim($idsQuery, ",").")";

        $dbResults = RedBeanController::Finder()->where("source", "textId in :id", array(":id" => $idsQuery));

        if(!$dbResults || $dbResults == null || !is_array($dbResults) || count($dbResults) < 1) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [No sourcse found matching any of the IDs supplied]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [Method finished]", \PEAR_LOG_DEBUG);
            return array();
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [END: Getting the sources from the data store]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [START: Parsing the data store objects]", \PEAR_LOG_DEBUG);

        $sources = array();

        foreach($dbResults as $dbResult) {

            $json = $dbResult->json;

            try {
                $sources[] = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromJSON($json);
            }
            catch (\Exception $e) {
                $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [An exception was thrown: $e]", \PEAR_LOG_ERR);
                $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [No source could be constructed from the DB content]", \PEAR_LOG_DEBUG);
                continue;
            }
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [END: Parsing the data store objects]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetSourcesById [Method finished]", \PEAR_LOG_DEBUG);

        return $sources;
    }

    /**
     * Given a set of content items, this method will persist
     * them to the data store, if they already exists then this
     * method should update the values in the data store.
     *
     * @param \Swiftriver\Core\ObjectModel\Content[] $content
     */
    public static function SaveContent($content){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveContent [Method invoked]", \PEAR_LOG_DEBUG);

        //initiate the redbean dal contoller
        $rb = RedBeanController::RedBean();

        //loop throught each item of content
        foreach($content as $item) {
            $i = reset(RedBeanController::Finder()->where("content", "textId = :id", array(":id" => $item->id)));

            if(!$i || $i == null) {
                //Create a new data store object
                $i = $rb->dispense("content");
            }

            //Add any properties we wish to be un encoded to the data store object
            $i = DataContext::AddPropertiesToDataSoreItem(
                    $i,
                    $item,
                    array(
                        "textId" => "id",
                        "state" => "state",
                        "date" => "date",
                    ));

            //Add the encoded content item to the data store object
            $i->json = json_encode($item);

            //Save the data store object
            $rb->store($i);

            //get the source from the content
            $source = $item->source;

            //get the source from the db
            $s = reset(RedBeanController::Finder()->where("source", "textId = :id", array(":id" => $source->id)));

            if(!$s || $s == null) {
                //Create a new data store object
                $s = $rb->dispense("source");
            }

            $s = DataContext::AddPropertiesToDataSoreItem(
                    $s,
                    $source,
                    array(
                        "textId" => "id",
                        "score" => "score",
                        "name" => "name",
                        "parent" => "parent",
                        "type" => "type",
                        "subType" => "subType",
                    ));
            $s->json = json_encode($source);
            $rb->store($s);

            //create the association between content and source
            RedBeanController::Associate($i, $s);
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::SaveContent [Method finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Given an array of content is's, this function will
     * fetch the content objects from the data store.
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    public static function GetContent($ids, $orderby = null) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [Method invoked]", \PEAR_LOG_DEBUG);

        //if no $orderby is sent
        if(!$orderby || $orderby == null) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [No Order By clause set, setting to 'date desc']", \PEAR_LOG_DEBUG);
            //Set it to the default - date DESC
            $orderby = "date desc";
        }

        //set up the return array
        $content = array();

        //If the $ids array is blank or empty, return the empty array
        if(!isset($ids) || !is_array($ids) || count($ids) < 1) {
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [No IDs sent to method]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [Method finsiehd]", \PEAR_LOG_DEBUG);
            return $content;
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [START: Building the RedBean Query]", \PEAR_LOG_DEBUG);

        //set up the array to hold the ids
        $queryIds = array();

        //start to build the sql
        $query = "textId in (";

        /*//for each content item, add to the query and the ids array
        for($i=0; $i<count($ids); $i++) {
            $query .= ":id$i,";
            $queryIds[":id$i"] = $ids[$i];
        }*/

        $counter = 0;
        foreach($ids as $id) {
            $query .= ":id$counter,";
            $queryIds[":id$counter"] = $id;
            $counter++;
        }

        //tidy up the query
        $query = rtrim($query, ",").") order by ".$orderby;

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [END: Building the RedBean Query]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [START: Running RedBean Query]", \PEAR_LOG_DEBUG);

        //Get the finder
        $finder = RedBeanController::Finder();

        //Find the content
        $dbContent = $finder->where("content", $query, $queryIds);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [FINISHED: Running RedBean Query]", \PEAR_LOG_DEBUG);

        //set up the return array
        $content = array();

        //set up the red bean
        $rb = RedBeanController::RedBean();

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [START: Constructing Content and Source items]", \PEAR_LOG_DEBUG);

        //loop through the db content
        foreach($dbContent as $key => $dbItem) {
            //get the associated source
            $s = reset($rb->batch("source", RedBeanController::GetRelatedBeans($dbItem, "source")));

            //Create the source from the db json
            $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromJSON($s->json);

            //get the json for the content
            $json = $dbItem->json;

            //create the content
            $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source, $json);

            //add it to the array
            $content[] = $item;
        }

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [END: Constructing Content and Source items]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContent [Method finished]", \PEAR_LOG_DEBUG);

        //return the content
        return $content;
    }

    /**
     * Given an array of content items, this method removes them
     * from the data store.
     * @param \Swiftriver\Core\ObjectModel\Content[] $content
     */
    public static function DeleteContent($content) {
        //Get the database name


        //initiate the redbean dal contoller
        $rb = RedBeanController::RedBean();

        //set up the array to hold the ids
        $ids = array();
        
        //start to build the sql
        $query = "delete from content where textId in (";
        
        //for each content item, add to the query and the ids array
        for($i=0; $i<count($content); $i++) {
            $query .= ":id$i,";
            $ids[":id$i"] = $content[$i]->id;
        }
        
        //tidy up the query
        $query = rtrim($query, ",").")";
        
        //Get the RB database adapter
        $db = RedBeanController::DataBaseAdapter();
        
        //execute the sql
        $db->exec($query, $ids);
        
        /*
        //loop throught each item of content
        foreach($content as $item) {
            $potentials = RedBeanController::Finder()->where("content", "textid = :id limit 1", array(":id" => $item->id));
            if(!isset($potentials) || !is_array($potentials) || count($potentials) == 0) {
                continue;
            }

            //get the content
            $i = reset($potentials);

            //First remove all existing text
            $textToRemove = $rb->batch("content_text", RedBeanController::GetRelatedBeans($i, "content_text"));
            if(isset($textToRemove) && is_array($textToRemove) && count($textToRemove) > 0) {
                foreach($textToRemove as $ttr) {
                    $rb->trash($ttr);
                }
            }

            //first remove the existing tags
            $tagsToRemove = $rb->batch("content_tags", RedBeanController::GetRelatedBeans($i, "content_tags"));
            if(isset($tagsToRemove) && is_array($tagsToRemove) && count($tagsToRemove) > 0) {
                foreach($tagsToRemove as $ttr) {
                    $rb->trash($ttr);
                }
            }

            //remove all existing difcollection and their difs
            $difCollectionsToRemove = $rb->batch("dif_collections", RedBeanController::GetRelatedBeans($i, "dif_collections"));
            if(isset($difCollectionsToRemove) && is_array($difCollectionsToRemove) && count($difCollectionsToRemove) > 0) {
                foreach($difCollectionsToRemove as $dctr) {
                    $difstoremove = $rb->batch("difs", RedBeanController::GetRelatedBeans($dctr, "difs"));
                    if(isset($difstoremove) && is_array($difstoremove) && count($difstoremove) > 0) {
                        foreach($difstoremove as $diftoremove) {
                            $rb->trash($diftoremove);
                        }
                    }
                    $rb->trash($dctr);
                }
            }

            //Remove the content
            $rb->trash($i);
         
        }
        */
    }

    /**
     *
     * @param string[] $parameters
     */
    public static function GetContentList($parameters) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContentList [Method invoked]", \PEAR_LOG_DEBUG);

        $baseSql =
            "from content left join ".
                "content_source on content.id = content_source.content_id left join ".
                    "source on content_source.source_id = source.id";

        $filters = array();

        $state = (key_exists("state", $parameters)) ? $parameters["state"] : null;
        if($state != null) {
            $filters[] = "content.state = '$state'";
        }

        $minVeracity = (key_exists("minVeracity", $parameters)) ? $parameters["minVeracity"] : null;
        if($minVeracity != null || $minVeracity === 0) {
            $filters[] = ($minVeracity === 0)
                ? "(source.score >= $minVeracity OR source.score IS NULL)"
                : "source.score >= $minVeracity";
        }

        $maxVeracity = (key_exists("maxVeracity", $parameters)) ? $parameters["maxVeracity"] : null;
        if($maxVeracity != null) {
            $filters[] = ($minVeracity === 0)
                ? "(source.score <= $maxVeracity OR source.score IS NULL)"
                : "source.score <= $maxVeracity";
        }

        $type = (key_exists("type", $parameters)) ? $parameters["type"] : null;
        if($type != null) {
            $filters[] = "source.type = '$type'";
        }

        $subType = (key_exists("subType", $parameters)) ? $parameters["subType"] : null;
        if($subType != null) {
            $filters[] = "source.subType = '$subType'";
        }

        $source = (key_exists("source", $parameters)) ? $parameters["source"] : null;
        if($source != null) {
            $filters[] = "source.textId = '$source'";
        }

        $pageSize = (key_exists("pageSize", $parameters)) ? $parameters["pageSize"] : null;
        $pageStart = (key_exists("pageStart", $parameters)) ? $parameters["pageStart"] : null;
        $pagination = ($pageSize != null)
            ? "limit " . (($pageStart == null) ? "0" : $pageStart) . ", $pageSize"
            : "";

        $orderBy = (key_exists("orderBy", $parameters)) ? $parameters["orderBy"] : null;
        if($orderBy == null) {
            $orderBy = "date desc";
        }

        $sql = $baseSql;
        for($i = 0; $i < count($filters); $i++) {
            $addition = ($i == 0) ? "WHERE" : "AND";
            $sql .= " " . $addition . " " . $filters[$i];
        }

        $selectSql = "select content.textId " . $sql;

        $allIds = RedBeanController::DataBaseAdapter()->getCol($selectSql, array());
        $totalCount = count($allIds);

        $selectSql .= " order by content." . $orderBy . " " . $pagination;

        $navigation = array();

        if($subType == null) {
            $typeSql = "select source.type as name, source.type as id, count(source.type) as count " . $sql . " group by source.type order by count desc";
            $types = array(
                "type" => "list",
                "key" => "type",
                "selected" => $type != null,
                "facets" => RedBeanController::DataBaseAdapter()->get($typeSql, array())
            );
            $navigation["Channels"] = $types;
        }

        if($type != null && $source == null) {
            $subTypeSql = "select source.subType as name, source.subType as id, count(source.subType) as count " . $sql . " group by source.subType order by count desc";
            $subTypes = array(
                "type" => "list",
                "key" => "subType",
                "selected" => $subType != null,
                "facets" => RedBeanController::DataBaseAdapter()->get($subTypeSql, array())
            );
            $navigation["Sub Channels"] = $subTypes;
        }

        if($subType != null && $type != null) {
            $sourceSql = "select source.name as name, source.textId as id, count(source.name) as count " .$sql . " group by source.name order by count desc";
            $sources = array(
                "type" => "list",
                "key" => "source",
                "selected" => $source != null,
                "facets" => RedBeanController::DataBaseAdapter()->get($sourceSql, array())
            );
            $navigation["Sources"] = $sources;
        }

        $subjectIds = RedBeanController::DataBaseAdapter()->getCol($selectSql, array());

        $content = DataContext::GetContent($subjectIds, $orderBy);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::GetContentList [Method finished]", \PEAR_LOG_DEBUG);

        return array (
            "totalCount" => $totalCount,
            "contentItems" => $content,
            "navigation" => $navigation
        );
    }

    /**
     * This method redords the fact that a marker (sweeper) has changed the score
     * of a source by marking a content items as either 'acurate', 'chatter' or
     * 'inacurate'
     *
     * @param string $sourceId
     * @param string $markerId
     * @param int $change
     */
    public static function RecordSourceScoreChange($sourceId, $markerId, $change, $reason = null) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RecordSourceScoreChange [Method invoked]", \PEAR_LOG_DEBUG);

        //get the red bean
        $rb = RedBeanController::RedBean();

        //create a new entry
        $entry = $rb->dispense("trustlog_sourcescorechange");

        //add the properties
        $entry->sourceId = $sourceId;
        $entry->markerId = $markerId;
        $entry->change = $change;
        if($reason != null) {
            $entry->reason = $reason;
        }

        //save the entry
        $rb->store($entry);

        $logger->log("Core::Modules::DataContext::MySQL_V1::DataContext::RecordSourceScoreChange [Method finished]", \PEAR_LOG_DEBUG);
    }

    public static function RunQuery($query) {
        //TODO: Logging
        $url = (string)Setup::$Configuration->DataBaseUrl;
        $username = (string)Setup::$Configuration->UserName;
        $password = (string)Setup::$Configuration->Password;

        //Open a connection to the DB server
        $mysql = mysql_connect($url, $username, $password);

        //Select the databse
        $database = (string)Setup::$Configuration->Database;

        $return = mysql_select_db($database, $mysql);
        $error = mysql_error($mysql);

        $return = mysql_query($query, $mysql);
        $error = mysql_error($mysql);

        mysql_close($mysql);

        return $return;
    }

    private static function AddPropertiesToDataSoreItem($dataStoreItem, $sourceItem, $propertiesArray) {
        foreach($propertiesArray as $key => $value) {
            $dataStoreItem->$key = isset($sourceItem->$value) ? $sourceItem->$value : null;
        }
        return $dataStoreItem;
    }
}
?>