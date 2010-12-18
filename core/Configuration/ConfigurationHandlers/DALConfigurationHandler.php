<?php
namespace Swiftriver\Core\Configuration\ConfigurationHandlers;
/**
 * Configuration access to the switchable IDataContext type
 * @author mg[at]swiftly[dot]org
 */
class DALConfigurationHandler extends BaseConfigurationHandler
{
    /**
     * The PHP5.3 namespace quialified class name of the implemetor
     * of the Swiftriver\DAL\DataContectInterfaces\IDataContext
     * @var Type
     */
    public $DataContextType;

    /**
     * The directory in which the $this->DataContectType class is location
     * relative to the Modules Directory
     * @var string
     */
    public $DataContextDirectory;

    /**
     * Constructor for the DALConfigurationHandler
     * @param string $configurationFilePath
     */
    public function __construct($configurationFilePath) 
    {
        //Use the base class to read in the configuration
        $xml = parent::SaveOpenConfigurationFile($configurationFilePath, "properties");

        //loop through the configuration properties
        foreach($xml->properties->property as $property) 
        {
            //Switch on the property name
            switch((string) $property["name"])
            {
                case "DataContextType" :
                    $this->DataContextType = $property["value"];
                    break;
                case "DataContextPath" :
                    $this->DataContextDirectory = $property["value"];
                    break;
            }
        }
    }
}
?>
