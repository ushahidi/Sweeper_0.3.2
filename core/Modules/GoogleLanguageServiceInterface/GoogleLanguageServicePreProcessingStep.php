<?php
namespace Swiftriver\PreProcessingSteps;
class GoogleLanguageServicePreProcessingStep implements \Swiftriver\Core\PreProcessing\IPreProcessingStep {

    public function __construct() {
        include_once (dirname(__FILE__)."/DetectionAndTranslationWorkflow.php");
        include_once (dirname(__FILE__)."/LanguageDetectionInterface.php");
        include_once (dirname(__FILE__)."/TranslationInterface.php");
    }

    /**
     * Given a collection of content items, this method will firstly ascertain in what language
     * content is presented. If this differs from the base language set up in the $configuration
     * then all text for the content will be translated, the original test will be relegated to
     * the second position of the $content->text collection and the translation into the
     * base language will be placed at position 0 in the $content->text collection.
     * Each piece of content that comes into this method, can expect to be returned with this
     * pattern - ie: the $content->text[0] LanguageSpecificText class being in the base
     * language and (if applicable) the original LanguageSpecificText class begin at
     * $content->text[1]
     *
     * @param \Swiftriver\Core\ObjectModel\Content[] $contentItems
     * @param \Swiftriver\Core\Configuration\ConfigurationHandlers\CoreConfigurationHandler $configuration
     * @param \Log $logger
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    public function Process($contentItems, $configuration, $logger) {
        $logger->log("PreProcessingSteps::GoogleLanguageServicePreProcessingStep::Process [Method invoked]", \PEAR_LOG_DEBUG);

        //if the content is not valid, jsut return it
        if(!isset($contentItems) || !is_array($contentItems) || count($contentItems) < 1) {
            $logger->log("PreProcessingSteps::GoogleLanguageServicePreProcessingStep::Process [No content supplied]", \PEAR_LOG_DEBUG);
            $logger->log("PreProcessingSteps::GoogleLanguageServicePreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);
            return $contentItems;
        }

        //create the return array
        $translatedContent = array();

        //get the base language for this swift instance
        $baseLanguageCode = $configuration->BaseLanguageCode;
        
        //get the referer
        $referer = getenv("SERVER_NAME");

        $logger->log("PreProcessingSteps::GoogleLanguageServicePreProcessingStep::Process [START: Looping through content items]", \PEAR_LOG_DEBUG);

        //Loop throught the content
        foreach($contentItems as $content) {

            $logger->log("PreProcessingSteps::GoogleLanguageServicePreProcessingStep::Process [START: Constructing Workflow]", \PEAR_LOG_DEBUG);

            //create the forwflow
            $workflow = new \Swiftriver\GoogleLanguageServiceInterface\DetectionAndTranslationWorkflow(
                    $content,
                    $referer,
                    $baseLanguageCode);

            $logger->log("PreProcessingSteps::GoogleLanguageServicePreProcessingStep::Process [END: Constructing Workflow]", \PEAR_LOG_DEBUG);

            $logger->log("PreProcessingSteps::GoogleLanguageServicePreProcessingStep::Process [START: Running Workflow for content]", \PEAR_LOG_DEBUG);

            try {
                //run the workflow
                $translatedContent[] = $workflow->RunWorkflow($logger);
            }
            catch (\Exception $e) {
                $logger->log("PreProcessingSteps::GoogleLanguageServicePreProcessingStep::Process [$e]", \PEAR_LOG_ERR);
                $logger->log("PreProcessingSteps::GoogleLanguageServicePreProcessingStep::Process [An exception was throw, moving to the next content item]", \PEAR_LOG_DEBUG);
                $logger->log("PreProcessingSteps::GoogleLanguageServicePreProcessingStep::Process [END: Running Workflow for content]", \PEAR_LOG_DEBUG);
                continue;
            }

            $logger->log("PreProcessingSteps::GoogleLanguageServicePreProcessingStep::Process [END: Running Workflow for content]", \PEAR_LOG_DEBUG);
        }

        $logger->log("PreProcessingSteps::GoogleLanguageServicePreProcessingStep::Process [END: Looping through content items]", \PEAR_LOG_DEBUG);

        $logger->log("PreProcessingSteps::GoogleLanguageServicePreProcessingStep::Process [Method finished]", \PEAR_LOG_DEBUG);

        //return the translated content
        return $translatedContent;
    }

    public function Description() {
        return "This plugin automatically translates your content from any language ".
               "supported by the Google Language Toolkit into the base language".
			   "specified during installation.";
    }
    public function Name() {
        return "Google Language Services";
    }
    public function ReturnRequiredParameters() {
        return array();
    }
}
?>
