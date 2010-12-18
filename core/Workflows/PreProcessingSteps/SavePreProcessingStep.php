<?php
namespace Swiftriver\Core\Workflows\PreProcessingSteps;
/**
 * @author mg[at]swiftly[dot]org
 */
class SavePreProcessingStep extends PreProcessingStepsBase
{
    public function RunWorkflow($json, $key)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        try
        {
            //Call the parent to decode the json
            $preProcessingStepName = parent::ParseJsonToPreProcessingStepName($json);
            $configuration = parent::ParseJsonToPreProcessingStepConfiguration($json);
        }
        catch(\Exception $e)
        {
            //Catch and report the exception if one is thrown
            $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [$e]", \PEAR_LOG_ERR);
            return parent::FormatErrorMessage($e);
        }

        $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [START: Listing all available pre processors]", \PEAR_LOG_DEBUG);

        //Build a new pre processor
        $preProcessor = new \Swiftriver\Core\PreProcessing\PreProcessor();

        //list all the availaibel steps
        $steps = $preProcessor->ListAllAvailablePreProcessingSteps();

        $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [END: Listing all available pre processors]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [START: Looking for the pre processor to activate]", \PEAR_LOG_DEBUG);

        //Loop throught the steps looking for one with the same name as came from the JOSN
        foreach($steps as $s)
            if($s->Name() == $preProcessingStepName)
                $step = $s;

        //If not found, return an error.
        if(!isset($step) || $step == null)
        {
            $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [No pre processor with a name matching $preProcessingStepName was found.]", \PEAR_LOG_DEBUG);
            return parent::FormatErrorMessage("No pre processor matching the name $preProcessingStepName could be found");
        }

        $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [END: Looking for the pre processor to activate]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [START: Collecting Configuration properties for Pre Processing Step]", \PEAR_LOG_DEBUG);

        $thisConfig = array();

        foreach($step->ReturnRequiredParameters() as $param)
        {
            foreach($configuration as $key => $value) 
                if($param->name == $key) 
                    $param->value = $value;

            $thisConfig[] = $param;
        }

        $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [END: Collecting Configuration properties for Pre Processing Step]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [START: Saving configuration properties for Pre Processing Step]", \PEAR_LOG_DEBUG);

        $config = \Swiftriver\Core\Setup::DynamicModuleConfiguration();

        $config->Configuration[$preProcessingStepName] = $thisConfig;

        $config->Save();

        $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [START: Saving configuration properties for Pre Processing Step]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::SavePreProcessingStep::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        parent::FormatMessage("OK");
    }
}
?>