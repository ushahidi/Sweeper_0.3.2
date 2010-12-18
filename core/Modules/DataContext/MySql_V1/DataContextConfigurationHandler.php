<?php
namespace Swiftriver\Core\Modules\DataContext\MySql_V1;
class DataContextConfigurationHandler extends \Swiftriver\Core\Configuration\ConfigurationHandlers\BaseConfigurationHandler {

    private $configurationFilePath;

    /**
     * @var string
     */
    public $DataBaseUrl;

    /**
     * @var string
     */
    public $UserName;

    /**
     * @var string
     */
    public $Password;

    /**
     * @var string
     */
    public $Database;

    /**
     * @var simpleXMLElement
     */
    public $xml;

    public function __construct($configurationFilePath) {
        $this->configurationFilePath = $configurationFilePath;
        $xml = simplexml_load_file($configurationFilePath);
        $this->xml = $xml;
        foreach($xml->properties->property as $property) {
            switch((string) $property["name"]) {
                case "DataBaseUrl" :
                    $this->DataBaseUrl = $property["value"];
                    break;
                case "UserName" :
                    $this->UserName = $property["value"];
                    break;
                case "Password" :
                    $this->Password = $property["value"];
                    break;
                case "Database" :
                    $this->Database = $property["value"];
                    break;
            }
        }
    }

    public function Save() {
        $this->xml->asXML($this->configurationFilePath);
    }
}
?>
