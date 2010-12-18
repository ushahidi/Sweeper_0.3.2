<?php
namespace Swiftriver\Core\Modules\SiSPS\Parsers;
/**
 * @author mg[at]swiftrly[dot]org
 */
class GoogleNewsParser implements IParser
{
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
    public function GetAndParse($channel)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [START: Extracting required parameters]", \PEAR_LOG_DEBUG);

        $searchPhrase = $channel->parameters["SearchPhrase"];
        
        if(!isset($searchPhrase) || $searchPhrase == null || !\is_string($searchPhrase))
        {
            $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [the parameter 'SearchPhrase' was not supplied. Returning null]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);

            return null;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [END: Extracting required parameters]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [START: Including the SimplePie module]", \PEAR_LOG_DEBUG);

        //Include the Simple Pie Framework to get and parse feeds
        $config = \Swiftriver\Core\Setup::Configuration();

        $simplePiePath = $config->ModulesDirectory."/SimplePie/simplepie.inc";

        include_once($simplePiePath);

        //Include the Simple Pie YouTube Framework
        $simpleTubePiePath = $config->ModulesDirectory."/SimplePie/simpletube.inc";

        include_once($simpleTubePiePath);

        $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [END: Including the SimplePie module]", \PEAR_LOG_DEBUG);

        //Construct a new SimplePie Parser
        $feed = new \SimplePie();

        //Get the cache directory
        $cacheDirectory = $config->CachingDirectory;

        $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [Setting the caching directory to $cacheDirectory]", \PEAR_LOG_DEBUG);

        //Set the caching directory
        $feed->set_cache_location($cacheDirectory);

        $feedUrl = "http://news.google.co.uk/news?q=" . \urlencode($searchPhrase) . "&output=rss";

        $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [Setting the feed url to $feedUrl]", \PEAR_LOG_DEBUG);

        //Pass the feed URL to the SImplePie object
        $feed->set_feed_url($feedUrl);

        $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [Initializing the feed]", \PEAR_LOG_DEBUG);

        //Run the SimplePie
        $feed->init();

        //Strip HTML
        $feed->strip_htmltags(array('span', 'font', 'style', 'table', 'td', 'tr', 'div', 'p', 'br', 'a'));

        //Create the Content array
        $contentItems = array();

        $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [START: Parsing feed items]", \PEAR_LOG_DEBUG);

        $feeditems = $feed->get_items();

        if(!$feeditems || $feeditems == null || !is_array($feeditems) || count($feeditems) < 1) {
            $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [No feeditems recovered from the feed]", \PEAR_LOG_DEBUG);
        }

        $lastSuccess = $channel->lastSuccess;

        //Loop through the Feed Items
        foreach($feeditems as $feedItem)
        {
            //Extract the date of the content
            $contentdate =  strtotime($feedItem->get_date());
            
            if(isset($lastSuccess) && is_numeric($lastSuccess) && isset($contentdate) && is_numeric($contentdate))
            {
                if($contentdate < $lastSuccess)
                {
                    $textContentDate = date("c", $contentdate);

                    $textlastSuccess = date("c", $lastSuccess);

                    $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [Skipped feed item as date $textContentDate less than last sucessful run ($textlastSuccess)]", \PEAR_LOG_DEBUG);

                    continue;
                }
            }

            $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [Adding feed item]", \PEAR_LOG_DEBUG);

            //Get source data
            $source_name = $feedItem->get_author()->name;
            $source_name = ($source_name == null || $source_name == "") ? "Google News Search - $searchPhrase" : $source_name . " @ " . "Google News Search - $searchPhrase";
            $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromIdentifier($source_name, $channel->trusted);
            $source->name = $source_name;
            $source->email = $feedItem->get_author()->email;
            $source->parent = $channel->id;
            $source->type = $channel->type;
            $source->subType = $channel->subType;
			
            //Extract all the relevant feedItem info
            $title = $feedItem->get_title();
            $description = $feedItem->get_description();
            $contentLink = $feedItem->get_permalink();
            $date = $feedItem->get_date();

            //Create a new Content item
            $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source);

            //Fill the Content Item
            $item->text[] = new \Swiftriver\Core\ObjectModel\LanguageSpecificText(
                    null, //here we set null as we dont know the language yet 
                    $title, 
                    array($description));
            $item->link = $contentLink;
            $item->date = strtotime($date);

            //Add the item to the Content array
            $contentItems[] = $item;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [END: Parsing feed items]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Modules::SiSPS::Parsers::GoogleNewsParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);

        //return the content array
        return $contentItems;
    }

    /**
     * This method returns a string array with the names of all
     * the source types this parser is designed to parse. For example
     * the RSSParser may return array("Blogs", "News Feeds");
     *
     * @return string[]
     */
    public function ListSubTypes()
    {
        return array("Keyword Search");
    }

    /**
     * This method returns a string describing the type of sources
     * it can parse. For example, the RSSParser returns "Feeds".
     *
     * @return string type of sources parsed
     */
    public function ReturnType()
    {
        return "Google News";
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
    public function ReturnRequiredParameters()
    {
        return array(
            "Keyword Search" => array (
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "SearchPhrase",
                    "string", 
                    "The phrase to search Google News for.")));
    }
}
?>
