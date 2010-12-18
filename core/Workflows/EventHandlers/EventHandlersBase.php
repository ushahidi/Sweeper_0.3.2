<?php
namespace Swiftriver\Core\Workflows\EventHandlers;
/**
 * @author mg[at]swiftly[dot]org
 */
class EventHandlersBase extends \Swiftriver\Core\Workflows\WorkflowBase
{
    /**
     * Parses a collection of IEventHandlers into well formed JSON
     * @param \Swiftriver\Core\EventDistribution\IEventHandler[] $handlers
     */
    public function ParseHandlersToJson($handlers)
    {
        $modulesConfig = \Swiftriver\Core\Setup::DynamicModuleConfiguration();

        $return;

        $return->handlers = array();
        
        foreach($handlers as $handler)
        {
            $h;

            $h->name = $handler->Name();

            $h->description = $handler->Description();

            $h->configurationProperties = $handler->ReturnRequiredParameters();

            if(array_key_exists($h->name, $modulesConfig->Configuration))
            {
                $configuration = $modulesConfig->Configuration[$h->name];

                if($configuration != null)
                    foreach($h->configurationProperties as $property) 
                        foreach($configuration as $key => $config) 
                            if($property->name == $key) 
                                $property->value = $config->value;
            }

            $h->active = isset($handler->active);

            $return->handlers[] = $h;

            unset($h);
        }
        return json_encode($return);
    }

    /**
     * Given a string of JSON this function parses it
     * and returns the name property or throws an
     * InvalidArgumentException
     * @param string $json
     * @return string
     */
    public function ParseJsonToEventHandlerName($json)
    {
        $result = json_decode($json);

        if(!$result || $result == null) 
            throw new \InvalidArgumentException("The json was malformed");

        if(!isset($result->name) || !is_string($result->name)) 
            throw new \InvalidArgumentException("The JSON did not contain the required 'name' string");

        return $result->name;
    }

    /**
     * Given a json string this function will attemp to 
     * extract the associative array of configuration 
     * options or throw an InvalidArgumentException
     * 
     * @param string $json
     * @return AssociativeArray 
     */
    public function ParseJsonToEventHandlerConfiguration($json)
    {
        $array = json_decode($json, true);

        if(!$array || $array == null) 
            throw new \InvalidArgumentException("The json was malformed");

        $return = array();

        $config = $array["data"];

        if($config == null) 
            return $return;

        foreach($config as $key => $value) 
            $return[$key] = $value;

        return $return;
    }
}
?>