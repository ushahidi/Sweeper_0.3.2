<?php
namespace  Swiftriver\TagTheNetInterface;
class ServiceInterface {
    /**
     * Given a valid servive URI and a valid string of text
     * this service wraps the TagThe.Net taggin service
     *
     * @param string $uri
     * @param string $json
     * @param \Swiftriver\Core\Configuration\ConfigurationHandlers\CoreConfigurationHandler $configuration
     * @return string
     */
    public function InterafceWithService($uri, $text, $configuration) {
        include_once($configuration->ModulesDirectory."/SiSW/ServiceWrapper.php");
        $uri = str_replace("?view=json", "", $uri);
        $uri = $uri."?view=json&text=".$text;
        $service = new \Swiftriver\Core\Modules\SiSW\ServiceWrapper($uri);
        $jsonFromService = $service->MakeGETRequest();
        return $jsonFromService;
    }
}
?>
