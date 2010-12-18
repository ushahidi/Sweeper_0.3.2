<?php
namespace Swiftriver\SiLCCInterface;
class ServiceInterface {
    /**
     * Given a valid servive URI and a valid string of text
     * this service wraps the SiLCC service
     *
     * @param string $uri
     * @param string $json
     * @param \Swiftriver\Core\Configuration\ConfigurationHandlers\CoreConfigurationHandler $configuration
     * @return string
     */
    public function InterafceWithService($uri, $text, $configuration) {
        include_once($configuration->ModulesDirectory."/SiSW/ServiceWrapper.php");
        $uri = $uri."?text=".$text;
        $service = new \Swiftriver\Core\Modules\SiSW\ServiceWrapper($uri);
        $jsonFromService = $service->MakeGETRequest();
        return $jsonFromService;
    }
}
?>
