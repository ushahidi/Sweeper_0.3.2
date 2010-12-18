<?php
namespace Swiftriver\UshahidiAPIInterface;
class ServiceInterface {
    /**
     * Given a valid servive URI and a valid string of JSON
     * this service wraps the SiCDS service
     *
     * @param string $uri
     * @param string $json
     * @param \Swiftriver\Core\Configuration\ConfigurationHandlers\CoreConfigurationHandler $configuration
     * @return string
     */
    public function InterafceWithService($uri, $parameters, $configuration) {
        include_once($configuration->ModulesDirectory."/SiSW/ServiceWrapper.php");
        $service = new \Swiftriver\Core\Modules\SiSW\ServiceWrapper($uri);
        $return = $service->MakePOSTRequest($parameters, 10);
        return $return;
    }
}
?>
