<?php
namespace Swiftriver\Core\Workflows\ChannelServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class ChannelServicesBase extends \Swiftriver\Core\Workflows\WorkflowBase
{
    public function ParseParsersToJSON($parsers)
    {
        $return;

        $return->channelTypes  = array();

        foreach($parsers as $parser) {
            $channelType;
            $channelType->type = $parser->ReturnType();
            $channelType->subTypes = $parser->ListSubTypes();
            $channelType->configurationProperties = $parser->ReturnRequiredParameters();
            $return->channelTypes[] = $channelType;
            unset($channelType);
        }

        return json_encode($return);
    }

    public function ParseJSONToId($json)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Workflows::ChannelServices::ChannelServicesBase::ParseJSONToId [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::ChannelServicesBase::ParseJSONToId [START: Decodeing JSON]", \PEAR_LOG_DEBUG);

        //Call json decode on the json
        $object = json_decode($json);

        //check to see if the object decoded
        if(!$object || $object == null) 
            throw new \InvalidArgumentException("The json passed to the method did not decode");

        //get the id from the object
        $id = $object->id;

        //Check that the ID is there
        if(!$id || $id == null || !is_string($id))
            throw new \InvalidArgumentException("The JSON did not contain a valid ID string");

        $logger->log("Core::Workflows::ChannelServices::ChannelServicesBase::ParseJSONToId [END: Decoding JSON]", \PEAR_LOG_DEBUG);

        return $id;
    }

    public function ParseChannelsToJSON($channels)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Workflows::ChannelServices::ChannelServicesBase::ParseChannelsToJSON [Method invoked]", \PEAR_LOG_INFO);

        $json = '{"channels":[';

        if(isset($channels) && is_array($channels) && count($channels) > 0) 
            foreach($channels as $channel) 
                $json .= json_encode($channel).",";

        $json = rtrim($json, ",").']}';

        $logger->log("Core:Workflows::ChannelServices::ChannelServicesBase::ParseChannelsToJSON [Method finsihed]", \PEAR_LOG_INFO);

        return $json;
    }

    public function ParseJSONToChannel($json)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Workflows::ChannelServices::ChannelServicesBase::ParseJSONToChannel [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::ChannelServicesBase::ParseJSONToChannel [START: Creating new Channel]", \PEAR_LOG_DEBUG);

        try
        {
            //Try and get a Channel from the factory
            $channel = \Swiftriver\Core\ObjectModel\ObjectFactories\ChannelFactory::CreateChannelFromJSON($json);
        } 
        catch (\Exception $e)
        {
            //If exception, get the mesasge
            $message = $e->getMessage();

            //and log it
            $logger->log("Core::Workflows::ChannelServices::ChannelServicesBase::ParseJSONToChannel [$message]", \PEAR_LOG_ERR);

            $logger->log("Core::Workflows::ChannelServices::ChannelServicesBase::ParseJSONToChannel [Method finished]", \PEAR_LOG_INFO);

            throw new \InvalidArgumentException("The JSON passed to this method did not contain data required to construct a Channel object: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::ChannelServicesBase::ParseJSONToChannel [END: Creating new Channel]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::ChannelServicesBase::ParseJSONToChannel [Method finished]", \PEAR_LOG_DEBUG);

        return $channel;
    }
}
?>
