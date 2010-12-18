<?php
namespace Swiftriver\Core\Configuration\ConfigurationHandlers;

/**
 * The configuration handler for all the core configuration
 * @author mg[at]swiftly[dot]org
 */
class CoreConfigurationHandler extends BaseConfigurationHandler
{
    /**
     * The name of the configuration section
     * @var string
     */
    public $Name;

    /**
     * The fully qualified path to the modules directory
     * @var string
     */
    public $ModulesDirectory;

    /**
     * The fully qualified path of the cashing directory
     * @var string
     */
    public $CachingDirectory;

    /**
     * The base language code
     * @link http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
     * @var ISO639-1_Language_Code
     */
    public $BaseLanguageCode;

    /**
     * Boolean that enables or disables debug logging
     * @var bool
     */
    public $EnableDebugLogging = false;

    /**
     * The constructor for the CoreConfigurationHandler
     * @param string $configurationFilePath
     */
    public function __construct($configurationFilePath) 
    {
        //use the base calss to open the config file
        $xml = parent::SaveOpenConfigurationFile($configurationFilePath, "properties");

        //extract the name element and store it
        $this->Name = (string) $xml["name"];

        //loop around the properties
        foreach($xml->properties->property as $property) 
        {
            //swiftch on the name of the property
            switch((string) $property["name"])
            {
                case "ModulesDirectory" :
                    $this->ModulesDirectory = dirname(__FILE__)."/../..".$property["value"];
                    break;
                case "CachingDirectory" :
                    $this->CachingDirectory = dirname(__FILE__)."/../..".$property["value"];
                    break;
                case "BaseLanguageCode" :
                    $this->BaseLanguageCode = (string) $property["value"];
                    break;
                case "EnableDebugLogging" :
                    $value = (string) $property["value"];
                    $this->EnableDebugLogging = $value === "true";
                    break;
            }
        }
    }
}
?>
