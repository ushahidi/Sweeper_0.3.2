<?php
namespace Swiftriver\Core\Modules\SiSPS\Parsers;
class TwitterParser implements IParser {
    /**
     * This method returns a string array with the names of all
     * the source types this parser is designed to parse. For example
     * the RSSParser may return array("Blogs", "News Feeds");
     *
     * @return string[]
     */
    public function ListSubTypes() {
        return array(
            "Search",
            "Follow User"
        );
    }

    /**
     * This method returns a string describing the type of sources
     * it can parse. For example, the RSSParser returns "Feeds".
     *
     * @return string type of sources parsed
     */
    public function ReturnType() {
        return "Twitter";
    }

    /**
     * This method returns an array of the required paramters that
     * are nessesary to run this parser. The Array should be in the
     * following format:
     * array(
     *  "SubType" => array ( ConfigurationElements )
     * )
     *
     * @return array()
     */
    public function ReturnRequiredParameters(){
        return array(
            "Search" => array (
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                        "SearchKeyword",
                        "string",
                        "The keyword(s) to search for"
                )
            ),/*
            "Follow User" => array(
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                        "TwitterAccount",
                        "string",
                        "The account name of the Twitter user"
                )
            )*/
        );
    }

    /**
     * Given a set of parameters, this method should
     * fetch content from a channel and parse each
     * content into the Swiftriver object model :
     * Content Item. The $lastSuccess datetime is passed
     * to the function to ensure that content that has
     * already been parsed is not duplicated.
     *
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     * @return Swiftriver\Core\ObjectModel\Content[] contentItems
     */
    public function GetAndParse($channel) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetAndParse [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetAndParse [START: Switching processing based on subType]", \PEAR_LOG_DEBUG);

        $content = array();

        switch ($channel->subType) {
            case "Search" : $content = $this->GetForTwitterSearch($channel); break;
            case "Follow User" : $content = $this->GetForTwitterAccount($channel); break;
            default : $content = array();
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetAndParse [END: Switching processing based on subType]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetAndParse [Method invoked]", \PEAR_LOG_DEBUG);

        return $content;
    }

    /**
     * Uses the twitter search api to return content from 
     * twitter.
     * 
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    private function GetForTwitterSearch($channel) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [START: Extracting required parameters]", \PEAR_LOG_DEBUG);

        //Extract the required variables
        $SearchKeyword = $channel->parameters["SearchKeyword"];
        if(!isset($SearchKeyword) || ($SearchKeyword == "")) {
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [the parapeter 'SearchKeyword' was not supplued. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [END: Extracting required parameters]", \PEAR_LOG_DEBUG);

        //Twitter url combined with the account name passed to this feed.
        $TwitterUrl = "http://search.twitter.com/search.json?q=".urlencode($SearchKeyword);

        //Create the Content array
        $contentItems = array();

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [START: Calling the twitter api]", \PEAR_LOG_DEBUG);

        $json = \file_get_contents($TwitterUrl);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [END: Calling the twitter API]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [START: Parsing feed items]", \PEAR_LOG_DEBUG);

        $tweets = \json_decode($json);

        if(!$tweets || $tweets == null) {
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [No feeditems recovered from the feed]", \PEAR_LOG_DEBUG);
        }

        //Loop throught the Feed Items
        foreach($tweets->results as $tweet) {
            //Extract the date of the content
            $contentdate = strtotime($tweet->created_at);
            if(isset($channel->lastSuccess) && is_numeric($channel->lastSuccess) && isset($contentdate) && is_numeric($contentdate)) {
                if($contentdate < $channel->lastSuccess) {
                    $textContentDate = date("c", $contentdate);
                    $textlastSuccess = date("c", $channel->lastSuccess);
                    $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Skipped feed item as date $textContentDate less than last sucessful run ($textlastSuccess)]", \PEAR_LOG_DEBUG);
                    continue;
                }
            }

            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Adding feed item]", \PEAR_LOG_DEBUG);

            //Extract all the relevant feedItem info
            $item = $this->ParseTweetFromJSON($tweet, $channel);

            //Add the item to the Content array
            $contentItems[] = $item;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [END: Parsing feed items]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterSearch [Method finished]", \PEAR_LOG_DEBUG);

        //return the content array
        return $contentItems;
        
    }

    /**
     * User the twitter RSS call to follow the tweets of a given
     * twitter user.
     * 
     * @param \Swiftriver\Core\ObjectModel\Source $source 
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    private function GetForTwitterAccount($channel) {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [START: Extracting required parameters]", \PEAR_LOG_DEBUG);

        //Extract the required variables
        $TwitterAccount = $channel->parameters["TwitterAccount"];
        if(!isset($TwitterAccount) || ($TwitterAccount == "")) {
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [the parapeter 'TwitterAccount' was not supplued. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [END: Extracting required parameters]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [START: Including the SimplePie module]", \PEAR_LOG_DEBUG);

        //Include the Simple Pie Framework to get and parse feeds
        $config = \Swiftriver\Core\Setup::Configuration();
        include_once $config->ModulesDirectory."/SimplePie/simplepie.inc";

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [END: Including the SimplePie module]", \PEAR_LOG_DEBUG);

        //Construct a new SimplePie Parsaer
        $feed = new \SimplePie();

        //Get the cach directory
        $cacheDirectory = $config->CachingDirectory;

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Setting the caching directory to $cacheDirectory]", \PEAR_LOG_DEBUG);

        //Set the caching directory
        $feed->set_cache_location($cacheDirectory);

        //Twitter url combined with the account name passed to this feed.
        $TwitterUrl = "http://twitter.com/statuses/user_timeline/".$TwitterAccount.".rss";

        //Pass the feed URL to the SImplePie object
        $feed->set_feed_url($TwitterUrl);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Setting the feed url to $TwitterUrl]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Initilising the feed]", \PEAR_LOG_DEBUG);

		$feed->enable_cache(false);

        //Run the SimplePie
        $feed->init();

        //Create the Content array
        $contentItems = array();

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [START: Parsing feed items]", \PEAR_LOG_DEBUG);

        $tweets = $feed->get_items();

        if(!$tweets || $tweets == null || !is_array($tweets) || count($tweets) < 1) {
            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [No feeditems recovered from the feed]", \PEAR_LOG_DEBUG);
        }

        //Loop throught the Feed Items
        foreach($tweets as $tweet) {
            //Extract the date of the content
            $contentdate = strtotime($tweet->get_date('c'));
            if(isset($channel->lastSuccess) && is_numeric($channel->lastSuccess) && isset($contentdate) && is_numeric($contentdate)) {
                if($contentdate < $channel->lastSuccess) {
                    $textContentDate = date("c", $contentdate);
                    $textlastSuccess = date("c", $channel->lastSuccess);
                    $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Skipped feed item as date $textContentDate less than last sucessful run ($textlastSuccess)]", \PEAR_LOG_DEBUG);
                    continue;
                }
            }

            $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Adding feed item]", \PEAR_LOG_DEBUG);

			//Extract all the relevant feedItem info
            $item = $this->ParseTweetFromATOMItem($tweet, $channel);

            //Add the item to the Content array
            $contentItems[] = $item;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [END: Parsing feed items]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::GetForTwitterAccount [Method finished]", \PEAR_LOG_DEBUG);

        //return the content array
        return $contentItems;
    }

    /**
     * Method for parsing the json returned from the curl oppertation
     * to content items.
     *
     * @param string json $data
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    private function ParseTweetFromJSON($tweet, $channel){
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::ParseTweetsFromJSON [Method invoked]", \PEAR_LOG_DEBUG);


        $source_name = $tweet->from_user;
        $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromIdentifier($source_name, $channel->trusted);
        $source->name = $source_name;
        $source->link = "http://twitter.com/" . $tweet->from_user;
        $source->parent = $channel->id;
        $source->type = $channel->type;
        $source->subType = $channel->subType;
        $source->applicationIds["twitter"] = $tweet->from_user_id;
        $source->applicationProfileImages["twitter"] = $tweet->profile_image_url;

        //Create a new Content item
        $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source);

        //Fill the Content Item
        $item->text[] = new \Swiftriver\Core\ObjectModel\LanguageSpecificText(
                null, //here we set null as we dont know the language yet
                $tweet->text,
                array($tweet->text));
        $item->link = $tweet->source;
        $item->date = strtotime($tweet->created_at);

        if($tweet->geo != null && $tweet->geo->type == "Point" && \is_array($tweet->geo->coordinates))
            $item->gisData[] = new \Swiftriver\Core\ObjectModel\GisData (
                    $tweet->geo->coordinates[1],
                    $tweet->geo->coordinates[0],
                    "");

        //Sanitize the tweet text into a DIF collection
        $sanitizedTweetDiffCollection = $this->ParseTweetToSanitizedTweetDiffCollection($item);

        //Add the dif collection to the item
        $item->difs = array($sanitizedTweetDiffCollection);

        $logger->log("Core::Modules::SiSPS::Parsers::TwitterParser::ParseTweetsFromJSON [Method finished]", \PEAR_LOG_DEBUG);

        return $item;
    }

    /**
     * Parses the simplepie item to a content item
     * @param \SimplePie_Item $tweet
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     */
    private function ParseTweetFromATOMItem($tweet, $channel)
    {
        //Extract all the relevant feedItem info
        $title = $tweet->get_title();
        //$description = $tweet->get_description();
        $contentLink = $tweet->get_permalink();
        $date = $tweet->get_date();
        
        //Create the source
        $author = $tweet->get_author();
        $source_name = ($author != null) ? $author->get_name() : $channel->name;
        $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromIdentifier($source_name, $channel->trusted);
        $source->name = $source_name;
        $source->email = ($author != null) ? $tweet->get_author()->get_email() : null;
        $source->link = ($author != null) ? $tweet->get_author()->get_link() : null;
        $source->parent = $channel->id;
        $source->type = $channel->type;
        $source->subType = $channel->subType;

        //Add location data
        //Long and lat
        $location = $tweet->get_item_tags("http://www.georss.org/georss", "point");

        $long = 0;
        $lat = 0;
        $name = "";

        if(is_array($location)) {
            $lat_lon_array = split(" ", $location[0]["data"]);
            
            $long = $lat_lon_array[1];
            $lat = $lat_lon_array[0];
        
            //Name
            $location_name = $tweet->get_item_tags("http://api.twitter.com", "place");

            if(is_array($location_name)) {
                if(isset($location_name[0]["child"]["http://api.twitter.com"]["full_name"][0]["data"])){
                    $name = $location_name[0]["child"]["http://api.twitter.com"]["full_name"][0]["data"];
                }
            }

            $source->gisData = array(new \Swiftriver\Core\ObjectModel\GisData($long, $lat, $name));

        }

        //Create a new Content item
        $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source);

        //Fill the Content Item
        $item->text[] = new \Swiftriver\Core\ObjectModel\LanguageSpecificText(
                null, //here we set null as we dont know the language yet
                $title,
                array());
        $item->link = $contentLink;
        $item->date = strtotime($date);

        //Sanitize the tweet text into a DIF collection
        $sanitizedTweetDiffCollection = $this->ParseTweetToSanitizedTweetDiffCollection($tweet);

        //Add the dif collection to the item
        $item->difs = array(
            $sanitizedTweetDiffCollection,
        );

        //return the item
        return $item;
    }

    /**
     * @param \Swiftriver\Core\ObjectModel\Content $item
     * @return \Swiftriver\Core\ObjectModel\DuplicationIdentificationFieldCollection
     */
    private function ParseTweetToSanitizedTweetDiffCollection($item) {
        //Get the original text
        $tweetText = $item->text[0]->title;

        //Break the text down into words
        $tweetTextParts = explode(" ", $tweetText);

        //Set up the sanitized return string
        $sanitizedText = "";

        //loop through all the words
        foreach($tweetTextParts as $part) {
            //to lowwer the word
            $part = strtolower($part);

            //If the word contains none standard chars, continue
            if(preg_match("/[^\w\d\.\(\)\!']/si", $part))
                continue;

            //if the owrd is just rt then continue
            if($part == "rt")
                continue;

            //Add the word to the sanitized
            $sanitizedText .= $part . " ";
        }

        //Create a new Diff
        $dif = new \Swiftriver\Core\ObjectModel\DuplicationIdentificationField(
                "Sanitized Tweet",
                utf8_encode($sanitizedText)
        );

        //Create the new diff collection
        $difCollection = new \Swiftriver\Core\ObjectModel\DuplicationIdentificationFieldCollection(
                "Sanitized Tweet",
                array($dif)
        );

        //Return the diff collection
        return $difCollection;
    }
}
?>
