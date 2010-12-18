<?php
namespace Swiftriver\Core\Modules\SiSPS\Parsers;
class EventfulParser implements IParser
{
    /**
     * Implementation of IParser::GetAndParse
     * @param \Swiftriver\Core\ObjectModel\Channel $channel
     * @param datetime $lassucess
     */
    public function GetAndParse($channel)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Modules::SiSPS::Parsers::EventfulParser::GetAndParse [Method invoked]", \PEAR_LOG_DEBUG);

        $contentItems = array();

        $logger->log("Core::Modules::SiSPS::Parsers::EventfulParser::GetAndParse [START: Extracting required parameters]", \PEAR_LOG_DEBUG);

        //Extract the required variables
        $apikey = $channel->parameters["APIKEY"];
        if(!isset($apikey) || ($apikey == "")) {
            $logger->log("Core::Modules::SiSPS::Parsers::EventfulParser::GetAndParse [the parameter 'APIKEY' was not supplied. Returning null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Modules::SiSPS::Parsers::EventfulParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);
            return null;
        }

        $logger->log("Core::Modules::SiSPS::Parsers::EventfulParser::GetAndParse [END: Extracting required parameters]", \PEAR_LOG_DEBUG);

        try
        {
            $json = \file_get_contents("http://api.eventful.com/rest/demands/members/list?app_key=" . $apikey);

            $object = \json_decode($json);

            $source_name = "Eventful";
            $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromIdentifier($source_name, $channel->trusted);
            $source->name = $source_name;
            $source->link = "http://eventful.com";
            $source->parent = $channel->id;
            $source->type = $channel->type;
            $source->subType = $channel->subType;

            foreach($object->members as $event)
            {
                if(!isset($event->time_stamp) || !isset($event->longitude) || !isset($event->latitude) || !isset($event->location) || !isset($event->unique_id))
                {
                    $logger->log("Core::Modules::SiSPS::Parsers::EventfulParser::GetAndParse [One event did not have the required parameters]", \PEAR_LOG_ERR);
                    continue;
                }

                $contentdate = \strtotime($event->time_stamp);

                if(isset($channel->lastSuccess) && is_numeric($channel->lastSuccess) && isset($contentdate) && is_numeric($contentdate))
                {
                    if($contentdate < $channel->lastSuccess)
                    {
                        $textContentDate = date("c", $contentdate);
                        $textlastSuccess = date("c", $channel->lastSuccess);
                        $logger->log("Core::Modules::SiSPS::Parsers::EventfulParser::GetAndParse [Skipped feed item as date $textContentDate less than last sucessful run ($textlastSuccess)]", \PEAR_LOG_DEBUG);
                        continue;
                    }
                }

                $item = \Swiftriver\Core\ObjectModel\ObjectFactories\ContentFactory::CreateContent($source);


                //Fill the Content Item
                $item->text[] = new \Swiftriver\Core\ObjectModel\LanguageSpecificText(
                        null, //here we set null as we dont know the language yet
                        "Eventful event in " .$event->location,
                        array("Eventful event in " .$event->location));
                $item->link = "http://eventful.com";
                $item->date = $contentdate;

                
                $item->gisData[] = new \Swiftriver\Core\ObjectModel\GisData (
                        $event->longitude,
                        $event->latitude,
                        $event->location);

                $contentItems[] = $item;
            }

        }
        catch(\Exception $e)
        {
            $logger->log("Core::Modules::SiSPS::Parsers::EventfulParser::GetAndParse [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Modules::SiSPS::Parsers::EventfulParser::GetAndParse [Method finished]", \PEAR_LOG_DEBUG);

        //return the content array
        return $contentItems;
    }


    /**
     * This method returns a string array with the names of all
     * the source types this parser is designed to parse. For example
     * the EventfulParser may return array("Blogs", "News Feeds");
     *
     * @return string[]
     */
    public function ListSubTypes()
    {
        return array(
            "Members List"
        );
    }

    /**
     * This method returns a string describing the type of sources
     * it can parse. For example, the EventfulParser returns "Feeds".
     *
     * @return string type of sources parsed
     */
    public function ReturnType()
    {
        return "Eventful";
    }

    /**
     * This method returns an array of the required parameters that
     * are necessary to run this parser. The Array should be in the
     * following format:
     * array(
     *  "SubType" => array ( ConfigurationElements )
     * )
     *
     * @return array()
     */
    public function ReturnRequiredParameters()
    {
        $return = array("RED Feed" => array(new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "APIKEY",
                    "string",
                    "The API key you have been given by Eventful")));
        return $return;
    }
}
?>
