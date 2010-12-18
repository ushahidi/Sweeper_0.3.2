<?php
namespace Swiftriver\Core\Workflows\PreProcessingSteps;
/**
 * @author mg[at]swiftly[dot]org
 */
class ListAllPreProcessingSteps extends PreProcessingStepsBase
{
    public function RunWorkflow($key)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Workflows::PreProcessingSteps::ListAllPreProcessingSteps::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::PreProcessingSteps::ListAllPreProcessingSteps::RunWorkflow [START: Constructing the PreProcessor]", \PEAR_LOG_DEBUG);

        $preProcessor = new \Swiftriver\Core\PreProcessing\PreProcessor();

        $logger->log("Core::Workflows::PreProcessingSteps::ListAllPreProcessingSteps::RunWorkflow [END: Constructing the PreProcessor]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::ListAllPreProcessingSteps::RunWorkflow [START: Listing all preprocessors]", \PEAR_LOG_DEBUG);

        $steps = $preProcessor->ListAllAvailablePreProcessingSteps();

        $logger->log("Core::Workflows::PreProcessingSteps::ListAllPreProcessingSteps::RunWorkflow [END: Listing all preprocessors]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::ListAllPreProcessingSteps::RunWorkflow [START: Finding out which are active]", \PEAR_LOG_DEBUG);

        //Get the currently configured steps
        $config = \Swiftriver\Core\Setup::PreProcessingStepsConfiguration();
        $activeSteps = $config->PreProcessingSteps;

        if($activeSteps != null && is_array($activeSteps) && $steps != null && is_array($steps))
            foreach($activeSteps as $activeStep) 
                foreach($steps as $step) 
                    if($step->Name() == $activeStep->name) 
                        $step->active = true;

        $logger->log("Core::Workflows::PreProcessingSteps::ListAllPreProcessingSteps::RunWorkflow [END: Finding out which are active]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::ListAllPreProcessingSteps::RunWorkflow [START: Encoding results to JSON]", \PEAR_LOG_DEBUG);

        $json = parent::ParseStepsToJson($steps);

        $logger->log("Core::Workflows::PreProcessingSteps::ListAllPreProcessingSteps::RunWorkflow [END: Encoding results to JSON]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::ListAllPreProcessingSteps::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        return parent::FormatReturn($json);
    }
}
?>