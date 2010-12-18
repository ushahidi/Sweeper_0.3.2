<?php
namespace Swiftriver\Core\Workflows\SourceServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class SourceServicesBase extends \Swiftriver\Core\Workflows\WorkflowBase
{
    public function ParseParsersToJSON($parsers)
    {
        $return;

        $return->sourceTypes  = array();

        foreach($parsers as $parser)
        {
            $sourceType;

            $sourceType->type = $parser->ReturnType();

            $sourceType->subTypes = $parser->ListSubTypes();

            $sourceType->configurationProperties = $parser->ReturnRequiredParameters();

            $return->sourceTypes[] = $sourceType;

            unset($sourceType);
        }

        return json_encode($return);
    }

    public function ParseJSONToId($json)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Workflows::SourceServices::SourceServicesBase::ParseJSONToId [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::SourceServicesBase::ParseJSONToId [START: Decodeing JSON]", \PEAR_LOG_DEBUG);

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

        $logger->log("Core::Workflows::SourceServices::SourceServicesBase::ParseJSONToId [END: Decoding JSON]", \PEAR_LOG_DEBUG);

        return $id;
    }

    public function ParseSourcesToJSON($sources)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Workflows::SourceServices::SourceServicesBase::ParseSourcesToJSON [Method invoked]", \PEAR_LOG_INFO);

        $json = '{"sources":[';

        if(isset($sources) && is_array($sources) && count($sources) > 0) 
            foreach($sources as $channel) 
                $json .= json_encode($channel).",";

        $json = rtrim($json, ",").']}';

        $logger->log("Core:Workflows::SourceServices::SourceServicesBase::ParseSourcesToJSON [Method finsihed]", \PEAR_LOG_INFO);

        return $json;
    }

    public function ParseJSONToSource($json)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Workflows::SourceServices::SourceServicesBase::ParseJSONToSource [Method invoked]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::SourceServicesBase::ParseJSONToSource [START: Creating new source]", \PEAR_LOG_DEBUG);

        try
        {
            //Try and get a source from the factory
            $source = \Swiftriver\Core\ObjectModel\ObjectFactories\SourceFactory::CreateSourceFromJSON($json);
        } 
        catch (\Exception $e)
        {
            //If exception, get the mesasge
            $message = $e->getMessage();

            //and log it
            $logger->log("Core::Workflows::SourceServices::SourceServicesBase::ParseJSONToSource [$message]", \PEAR_LOG_ERR);

            $logger->log("Core::Workflows::SourceServices::SourceServicesBase::ParseJSONToSource [Method finished]", \PEAR_LOG_INFO);

            throw new \InvalidArgumentException("The JSON passed to this method did not contain data required to construct a source object: $message");
        }

        $logger->log("Core::Workflows::SourceServices::SourceServicesBase::ParseJSONToSource [END: Creating new source]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::SourceServicesBase::ParseJSONToSource [Method finished]", \PEAR_LOG_DEBUG);

        return $source;
    }
}
?>
