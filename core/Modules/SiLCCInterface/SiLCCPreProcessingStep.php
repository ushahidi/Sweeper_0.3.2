<?php
namespace Swiftriver\PreProcessingSteps;
include_once (dirname(__FILE__)."/ContentFromJSONParser.php");
include_once (dirname(__FILE__)."/ServiceInterface.php");
include_once (dirname(__FILE__)."/TextForUrlParser.php");

class SiLCCPreProcessingStep implements \Swiftriver\Core\PreProcessing\IPreProcessingStep {

    public function Description(){
        return "This plugin sends all content to the Swift Web Service: " .
               "Swiftriver Language Computational Core (SiLCC). It then attempts " .
               "to apply auto-tag content with relevant keywords.";
    }
    public function Name(){
        return "SiLCC";
    }
    public function Process($contentItems, $configuration, $logger) {
        $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [Method invoked]", \PEAR_LOG_DEBUG);

        //if the content is not valid, jsut return it
        if(!isset($contentItems) || !is_array($contentItems) || count($contentItems) < 1) {
            $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [No content supplied]", \PEAR_LOG_DEBUG);
            $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);
            return $contentItems;
        }

        //set up the return array
        $taggedContentItems = array();

        $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [START: Loop through content]", \PEAR_LOG_DEBUG);

        foreach($contentItems as $item) {
            $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [START: Parse text for SiLCC]", \PEAR_LOG_DEBUG);

            //construct a new Url parser
            $urlParser = new \Swiftriver\SiLCCInterface\TextForUrlParser($item);

            //get the url formatted text
            $text = $urlParser->GetUrlText();

            $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [END: Parse text for SiLCC]", \PEAR_LOG_DEBUG);

            $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [START: Call the SiLCC Service]", \PEAR_LOG_DEBUG);

            try {
                //construct a new service interface
                $service = new \Swiftriver\SiLCCInterface\ServiceInterface();

                //call the service through the interface
                $json = $service->InterafceWithService("http://opensilcc.com/api/tag", $text, $configuration);
            }
            catch (\Exception $e) {
                $message = $e->getMessage();
                $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [$message]", \PEAR_LOG_ERR);
                $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [Exception throw while calling the service, moving on to next content item]", \PEAR_LOG_DEBUG);
                continue;
            }

            $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [END: Call the SiLCC Service]", \PEAR_LOG_DEBUG);

            $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [START: Parse the results from the service]", \PEAR_LOG_DEBUG);

            //Construct a new result parser
            $jsonParser = new \Swiftriver\SiLCCInterface\ContentFromJSONParser($item, $json);

            //get back the tagged content from the parser
            $taggedContent = $jsonParser->GetTaggedContent();

            $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [END: Parse the results from the service]", \PEAR_LOG_DEBUG);

            //Add the content to the return array
            $taggedContentItems[] = $taggedContent;
        }

        $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [END: Loop through content]", \PEAR_LOG_DEBUG);
        $logger->log("PreProcessingSteps::SiLCCPreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);

        return $taggedContentItems;
    }
    public function ReturnRequiredParameters() {
        return array();
    }
}
?>
