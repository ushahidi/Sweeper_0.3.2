<?php
namespace Swiftriver\Core\Configuration\ConfigurationHandlers;

/**
 * Configuration handler that manages the dynamic storage of module
 * specific configuration details by damically creating configuration 
 * property groups.
 * @author mg[at]swiftly[dot]org
 */
class DynamicModuleConfigurationHandler extends BaseConfigurationHandler {

    /**
     * The path of the coniguration file
     * @var string
     */
    private $configurationFilePath;

    /**
     * Associative array containig the dynamic configuration
     * @var array
     */
    public $Configuration = array();

    /**
     * Constructor for the DyanmicModuleConfigurationHandler
     * @param string $configurationFilePath
     */
    public function __construct($configurationFilePath)
    {
        //Save the file path
        $this->configurationFilePath = $configurationFilePath;

        //Use the base class the lad the configuration
        $xml = parent::SaveOpenConfigurationFile($configurationFilePath, "modules");

        //loop through all the modules that have been loaded so far
        foreach($xml->modules->module as $module) 
        {
            //Get the name of the module
            $moduleName = (string) $module["name"];

            //Set up an array to old the module spacific configuration
            $configuration = array();

            //Loop through the modules configuration properties
            foreach($module->properties->property as $property) 
            {
                //Get the property name
                $name = (string) $property["name"];

                //Get the property type
                $type = (string) $property["type"];

                //Get the property description
                $description = (string) $property["description"];

                //Get the property value if there is one
                $value = (string) $property["value"];

                //Create a new strongly typed configuration element from the data
                $configEelement = new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                        $name,
                        $type,
                        $description,
                        $value);

                //Add the element to the configuration array
                $configuration[$name] = $configEelement;
            }

            //Add the configuration elements array to the modules collection
            $this->Configuration[$moduleName] = $configuration;
        }
    }

    /**
     * Function that writes the configuration out to the same file it
     * was written from
     */
    public function Save() 
    {
        //Create the root xml element
        $root = new \SimpleXMLElement("<configuration></configuration>");

        //Add the modules collection element
        $modulesCollection = $root->addChild("modules");

        //Loop through the configurationstored in this class
        foreach($this->Configuration as $key => $value) 
        {
            //Gte the module name
            $module = $modulesCollection->addChild("module");

            //Add this name to the xml atribute
            $module->addAttribute("name", $key);

            //Add a collection element for the properties
            $properties = $module->addChild("properties");

            //Loop around the properties and add them to the collection element
            foreach($value as $configurationElement) {
                $property = $properties->addChild("property");
                $property->addAttribute("name", $configurationElement->name);
                $property->addAttribute("type", $configurationElement->type);
                $property->addAttribute("description", $configurationElement->description);
                $property->addAttribute("value", $configurationElement->value);
            }
        }

        //Write out the xml to the file
        $root->asXML($this->configurationFilePath);
    }
}
?>