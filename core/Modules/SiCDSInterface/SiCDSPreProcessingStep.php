<?php
namespace Swiftriver\PreProcessingSteps;
include_once(dirname(__FILE__)."/ServiceInterface.php");
include_once(dirname(__FILE__)."/Parser.php");
class SiCDSPreProcessingStep implements \Swiftriver\Core\PreProcessing\IPreProcessingStep {
    /**
     * The short name for this pre processing step, should be no longer
     * than 50 chars
     *
     * @return string
     */
    public function Name() {
        return "SiCDS";
    }

    /**
     * The description of this step
     *
     * @return string
     */
    public function Description() {
        return "This is the impulse turbine for the Swiftriver Content De-Duplication ".
               "server. If activated, all content will be scanned by the SiCDS service and " .
               "attempats will be made to prevent any duplicates from reaching you.";
    }

    /**
     * This method returns an array of the required paramters that
     * are nessesary to run this step.
     *
     * @return \Swiftriver\Core\ObjectModel\ConfigurationElement[]
     */
    public function ReturnRequiredParameters() {
        return array(
            new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "Service Url",
                    "string",
                    "The Url of the cloud or locally hosted instsnce of the SiCDS service"
            ),
            new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "API Key",
                    "string",
                    "The api key you will need to communicate with the SiCDS service"
            ),
        );
    }

    /**
     * Interface method that all PrePorcessing Steps must implement
     *
     * @param \Swiftriver\Core\ObjectModel\Content[] $contentItems
     * @param \Swiftriver\Core\Configuration\ConfigurationHandlers\CoreConfigurationHandler $configuration
     * @param \Log $logger
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    public function Process($contentItems, $configuration, $logger) {
        try {
            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [Method invoked]", \PEAR_LOG_DEBUG);

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [START: Loading module configuration]", \PEAR_LOG_DEBUG);

            $config = \Swiftriver\Core\Setup::DynamicModuleConfiguration()->Configuration;

            if(!key_exists($this->Name(), $config)) {
                $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [The SiCDS Pre Processing Step was called but no configuration exists for this module]", \PEAR_LOG_ERR);
                $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);
                return;
            }

            $config = $config[$this->Name()];

            foreach($this->ReturnRequiredParameters() as $requiredParam) {
                if(!key_exists($requiredParam->name, $config)) {
                    $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [The SiCDS Pre Processing Step was called but all the required configuration properties could not be loaded]", \PEAR_LOG_ERR);
                    $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);
                    return;
                }
            }

            $apiKey = (string) $config["API Key"]->value;

            $serviceUrl = (string) $config["Service Url"]->value;

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [END: Loading module configuration]", \PEAR_LOG_DEBUG);

            $uniqueContentItems = array();

            $parser = new \Swiftriver\SiCDSInterface\Parser();

            $serviceInterface = new \Swiftriver\SiCDSInterface\ServiceInterface();

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [START: Looping through the content items]", \PEAR_LOG_DEBUG);

            foreach($contentItems as $item) {
                try {
                    $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [START: Parsing content item into JSON]", \PEAR_LOG_DEBUG);

                    $jsonForService = $parser->ParseItemToRequestJson($item, $apiKey);

                    $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [END: Parsing content item into JSON]", \PEAR_LOG_DEBUG);

                    $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [START: Calling the SiCDS]", \PEAR_LOG_DEBUG);

                    $jsonFromService = $serviceInterface->InterafceWithService(
                            $serviceUrl,
                            $jsonForService,
                            $configuration
                    );

                    $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [END: Calling the SiCDS]", \PEAR_LOG_DEBUG);

                    if($parser->ContentIsUnique($jsonFromService, $item->id)) {
                        $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [Content with Id: $item->id is unique]", \PEAR_LOG_DEBUG);
                        $uniqueContentItems[] = $item;
                    }
                    else {
                        $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [Content with Id: $item->id a duplicate]", \PEAR_LOG_DEBUG);
                    }
                }
                catch (\Exception $e) {
                    $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [An exception was thrown]", \PEAR_LOG_ERR);
                    $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [$e]", \PEAR_LOG_ERR);
                    $uniqueContentItems[] = $item;
                }
            }

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [END: Looping through the content items]", \PEAR_LOG_DEBUG);

            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);

            return $uniqueContentItems;
        }
        catch (\Exception $e) {
            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [An exception was thrown]", \PEAR_LOG_ERR);
            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [$e]", \PEAR_LOG_ERR);
            $logger->log("Swiftriver::PreProcessingSteps::SiCDSPreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);
            return $contentItems;
        }
    }
}
?>