<?php
namespace Swiftriver\Core\ObjectModel\ObjectFactories;
/**
 * Factory used to build Channel objects
 * @author mg[at]swiftly[dot]org
 */
class ChannelFactory 
{
    /**
     * Creates a new Channel obejct from an id string
     * @param string $identifier
     * @return \Swiftriver\Core\ObjectModel\Channel 
     */
    public static function CreateChannelFromIdentifier($identifier) 
    {
        $channel = new \Swiftriver\Core\ObjectModel\Channel();
        $channel->id = md5($identifier, true);
        return $channel;
    }

    public static function CreateChannelFromJSON($json) 
    {
        //decode the json
        $object = json_decode($json);

        //If there is an error in the JSON
        if(!$object || $object == null) 
            throw new \Exception("There was an error in the JSON passed in to the ChannelFactory.");

        //create a new Channel
        $channel = new \Swiftriver\Core\ObjectModel\Channel();

        //set the basic properties
        $channel->id =               isset($object->id) ? $object->id : md5(uniqid(rand(), true));
        $channel->name =             isset($object->name) ? $object->name : null;
        $channel->type =             isset($object->type) ? $object->type : null;
        $channel->subType =          isset($object->subType) ? $object->subType : null;
        $channel->updatePeriod =     isset($object->updatePeriod) ? $object->updatePeriod : 30;
        $channel->nextrun =          isset($object->nextrun) ? $object->nextrun : strtotime("+ ".$channel->updatePeriod." minutes");
        $channel->active =           isset($object->active) ? $object->active : true;
        $channel->lastSuccess =      isset($object->lastSuccess) ? $object->lastSuccess : null;
        $channel->inprocess =        isset($object->inprocess) ? $object->inprocess : false;
        $channel->timesrun =         isset($object->timesrun) ? $object->timesrun : 0;
        $channel->deleted =          isset($object->deleted) ? $object->deleted : false;
        $channel->trusted =          isset($object->trusted) ? $object->trusted : false;
        $channel->parameters =       array();
        
        //If te parameters collection is set move tem to the channel
        if(isset($object->parameters))
            foreach($object->parameters as $key => $value)
                $channel->parameters[$key] = $value;

        //return the Channel
        return $channel;
    }
}
?>
