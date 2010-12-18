<?php
namespace Swiftriver\Core\Modules\SiSPS\Parsers;
/**
 * @author mg[at]swiftrly[dot]org
 */
class MeetupParser implements IParser
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

        $logger->log("Core::Modules::SiSPS::Parsers::MeetupParser::GetAndParse [Method invoked]", \PEAR_LOG_DEBUG);

        $contentItems = array();

        $apikey = $channel->parameters["APIKey"];

        $urlname = $channel->parameters["urlname"];

        $url = "http://api.meetup.com/ew/events?urlname=$urlname&key=$apikey&format=xml";

        $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromIdentifier("Meetup " . $urlname);

        $source->name = "Meetup $urlname";

        $source->link = "http://meetup.com/$urlname";

        $source->type = $channel->type;

        $source->subType = $channel->subType;

        $source->parent = $channel->id;

        $xml = \file_get_contents($url);

        $element = new \SimpleXmlElement($xml);

        foreach($element->items->item as $item)
        {
            $rawdate = (string) $item->created;

            $date = (0 + $rawdate) / 1000;

            $lastSuccess = $channel->lastSuccess;

            if(isset($lastSuccess) && is_numeric($lastSuccess) && isset($date) && is_numeric($date))
            {
                if($date < $lastSuccess)
                {
                    $textContentDate = date("c", $date);

                    $textlastSuccess = date("c", $lastSuccess);

                    $logger->log("Core::Modules::SiSPS::Parsers::MeetupParser::GetAndParse [Skipped feed item as date $textContentDate less than last sucessful run ($textlastSuccess)]", \PEAR_LOG_DEBUG);

                    continue;
                }
            }

            $content = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source);

            $content->date = $date;

            $content->gisData = array(
                new \Swiftriver\Core\ObjectModel\GisData(
                        (float) $item->lon,
                        (float) $item->lat,
                        ""));

            $content->link = (string) $item->meetup_url;

            $content->text = array(
                new \Swiftriver\Core\ObjectModel\LanguageSpecificText(
                        null,
                        "Meetup in " . (string) $item->city,
                        array((string) $item->description)));

            $contentItems[] = $content;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::MeetupParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);

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
        return array("Meetup Everywhere Event Search");
    }

    /**
     * This method returns a string describing the type of sources
     * it can parse. For example, the RSSParser returns "Feeds".
     *
     * @return string type of sources parsed
     */
    public function ReturnType()
    {
        return "Meetup";
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
            "Meetup Everywhere Event Search" => array (
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "APIKey",
                    "string",
                    "Your API Key."),
                new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "urlname",
                    "string",
                    "The Url name of the everywhere site.")));
    }
}
?>
