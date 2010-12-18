<?php
namespace Swiftriver\Core\Configuration\ConfigurationHandlers;
/**
 * Configuration handler to hold the configuration relating to which Pre 
 * Processors are to be used by the system
 * @author mg[at]swiftly[dot]org
 */
class PreProcessingStepsConfigurationHandler extends BaseConfigurationHandler
{

    /**
     * The file name of the configuration file
     * @var string
     */
    private $configurationFilePath;

    /**
     * The xml
     * @var simpleXMLElement
     */
    public $xml;

    /**
     * The ordered collection of pre preocessing steps
     * @var \Swiftriver\Core\ObjectModel\PreProcessingStepEntry[]
     */
    public $PreProcessingSteps;

    /**
     * Constructor for the PreProcessingStepsConfigurationHandler
     * @param string $configurationFilePath
     */
    public function __construct($configurationFilePath) 
    {
        //Set the file name
        $this->configurationFilePath = $configurationFilePath;

        //Use the base class to open the xml
        $xml = parent::SaveOpenConfigurationFile($configurationFilePath, "preProcessingSteps");

        //Set the xml to class level
        $this->xml = $xml;

        //Set up the class level array to hold the pre processor configuration
        $this->PreProcessingSteps = array();

        //loop around the xml elements and add the confi t the array
        foreach($xml->preProcessingSteps->step as $step) {
            $this->PreProcessingSteps[] =
                    new \Swiftriver\Core\ObjectModel\PreProcessingStepEntry(
                        (string) $step["name"],
                        (string) $step["className"],
                        (string) $step["filePath"]);
        }
    }

    /**
     * Function to write out the configuration to file
     */
    public function Save()
    {
        //Creats the root element
        $root = new \SimpleXMLElement("<configuration></configuration>");

        //Create the collection element
        $collection = $root->addChild("preProcessingSteps");

        //Loop around the config in the class level array
        foreach($this->PreProcessingSteps as $step) 
        {
            //If the array element is null, continue
            if($step == null)
                continue;

            //Add the element to the xml
            $element = $collection->addChild("step");
            $element->addAttribute("name", $step->name);
            $element->addAttribute("className", $step->className);
            $element->addAttribute("filePath", $step->filePath);
        }

        //Write the xml to the file
        $root->asXML($this->configurationFilePath);
    }
}
?>