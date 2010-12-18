<?php
namespace Swiftriver\Core\Configuration\ConfigurationHandlers;
/**
 * Configuration handler to hold the configuration relating to the which
 * Event Handlers are active in the system
 * @author mg[at]swiftly[dot]org
 */
class EventDistributionConfigurationHandler extends BaseConfigurationHandler {

    /**
     * The file path of the associated configuration file
     * @var string
     */
    private $configurationFilePath;

    /**
     * The ordered collection of pre preocessing steps
     * @var \Swiftriver\Core\ObjectModel\PreProcessingStepEntry[]
     */
    public $EventHandlers;

    /**
     * Constructior for the EventDistribution System
     * @param string $configurationFilePath
     */
    public function __construct($configurationFilePath) 
    {
        //Set the file path
        $this->configurationFilePath = $configurationFilePath;

        //Use the base class to open the config file
        $xml = parent::SaveOpenConfigurationFile($configurationFilePath, "eventHandlers");

        //Set up the array to hold the event handlers
        $this->EventHandlers = array();

        //Loop around the configuration elements
        foreach($xml->eventHandlers->handler as $handler)
        {
            //Get all the values from the configuration element
            $name = (string) $handler["name"];
            $classname = (string) $handler["className"];
            $handler = (string) $handler["filePath"];
            
            //Create a new EventHandlerEntry object
            $config = new \Swiftriver\Core\ObjectModel\EventHandlerEntry($name, $classname, $handler);
            
            //Add it to the array
            $this->EventHandlers[] = $config;
        }
    }

    /**
     * Function to write out the configutration to a file
     */
    public function Save()
    {
        //Set up the root element
        $root = new \SimpleXMLElement("<configuration></configuration>");

        //Add the collection elememt
        $collection = $root->addChild("eventHandlers");

        //Loop around the collection of event handlers
        foreach($this->EventHandlers as $step) 
        {
            //If the collection element is null then continue
            if($step == null)
                continue;

            //Set up the element
            $element = $collection->addChild("handler");

            //Add the event handler properties
            $element->addAttribute("name", $step->name);
            $element->addAttribute("className", $step->className);
            $element->addAttribute("filePath", $step->filePath);
        }

        //write out the configuration to xml
        $root->asXML($this->configurationFilePath);
    }
}
?>
