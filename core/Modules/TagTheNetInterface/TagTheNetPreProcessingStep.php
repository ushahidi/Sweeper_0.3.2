<?php
namespace Swiftriver\PreProcessingSteps;
class TagTheNetPreProcessingStep implements \Swiftriver\Core\PreProcessing\IPreProcessingStep {
    /**
     * Constructor method to include the setup file
     */
    public function __construct() {
        //include the steup file
        include_once(dirname(__FILE__)."/Setup.php");
    }

    /**
     * This method, converts the relevant bits of the Content
     * items to JSON, sends them to the TheThe.net service and
     * using the return JSON, adds tags to the content.
     *
     * @param \Swiftriver\Core\ObjectModel\Content[] $contentItems
     * @param \Swiftriver\Core\Configuration\ConfigurationHandlers\CoreConfigurationHandler $configuration
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    public function Process($contentItems, $configuration, $logger) {
        $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [Method invoked]", \PEAR_LOG_DEBUG);
        
        //if the content is not valid, jsut return it
        if(!isset($contentItems) || !is_array($contentItems) || count($contentItems) < 1) {
            $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [No content supplied]", \PEAR_LOG_DEBUG);
            $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);
            return $contentItems;
        }

        //set up the return array
        $taggedContentItems = array();

        $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [START: Loop through content]", \PEAR_LOG_DEBUG);

        foreach($contentItems as $item) {
            $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [START: Parse text for TagTheNet]", \PEAR_LOG_DEBUG);

            //construct a new Url parser
            $urlParser = new \Swiftriver\TagTheNetInterface\TextForUrlParser($item);

            //get the url formatted text
            $text = $urlParser->GetUrlText();

            $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [END: Parse text for TagTheNet]", \PEAR_LOG_DEBUG);

            $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [START: Call the TagTheNet Service]", \PEAR_LOG_DEBUG);

            try {
                //construct a new service interface
                $service = new \Swiftriver\TagTheNetInterface\ServiceInterface();

                //call the service through the interface
                $json = $service->InterafceWithService("http://tagthe.net/api/", $text, $configuration);
            }
            catch (\Exception $e) {
                $message = $e->getMessage();
                $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [$message]", \PEAR_LOG_ERR);
                $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [Exception throw while calling the service, moving on to next content item]", \PEAR_LOG_DEBUG);
                continue;
            }

            $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [END: Call the TagTheNet Service]", \PEAR_LOG_DEBUG);

            $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [START: Parse the results from the service]", \PEAR_LOG_DEBUG);

            //Construct a new result parser
            $jsonParser = new \Swiftriver\TagTheNetInterface\ContentFromJSONParser($item, $json);

            //get back the tagged content from the parser
            $taggedContent = $jsonParser->GetTaggedContent();

            $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [END: Parse the results from the service]", \PEAR_LOG_DEBUG);

            //Add the content to the return array
            $taggedContentItems[] = $taggedContent;
        }
        
        $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [END: Loop through content]", \PEAR_LOG_DEBUG);
        $logger->log("PreProcessingSteps::TagTheNetPreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);

        return $taggedContentItems;
    }

    public function Description() {
        return "This plugin uses TagThe.net to auto-tag content and to".
               "sort it into semantic categories (who, what, where).";
    }
    public function Name() {
        return "TagThe.Net";
    }

    public function ReturnRequiredParameters() {
        return array();
    }

}
?>
