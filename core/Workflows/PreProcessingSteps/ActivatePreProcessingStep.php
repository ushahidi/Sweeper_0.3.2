<?php
namespace Swiftriver\Core\Workflows\PreProcessingSteps;
/**
 * @author mg[at]swiftly[dot]org
 */
class ActivatePreProcessingStep extends PreProcessingStepsBase
{
    public function RunWorkflow($json, $key)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        try
        {
            //Call the parent to decode the json
            $preProcessingStepName = parent::ParseJsonToPreProcessingStepName($json);
        }
        catch(\Exception $e)
        {
            //Catch and report the exception if one is thrown
            $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [$e]", \PEAR_LOG_ERR);
            return parent::FormatErrorMessage($e);
        }

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [START: Listing all available pre processors]", \PEAR_LOG_DEBUG);

        //Build a new pre processor
        $preProcessor = new \Swiftriver\Core\PreProcessing\PreProcessor();

        //list all the availaibel steps
        $steps = $preProcessor->ListAllAvailablePreProcessingSteps();

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [END: Listing all available pre processors]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [START: Looking for the pre processor to activate]", \PEAR_LOG_DEBUG);

        //Loop throught the steps looking for one with the same name as came from the JOSN
        foreach($steps as $s) 
            if($s->Name() == $preProcessingStepName) 
                $step = $s;

        //If not found, return an error.
        if(!isset($step) || $step == null)
        {
            $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [No pre processor with a name matching $preProcessingStepName was found.]", \PEAR_LOG_DEBUG);
            return parent::FormatErrorMessage("No pre processor matching the name $preProcessingStepName could be found");
        }

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [END: Looking for the pre processor to activate]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [START: Constructing the PreProcessingStep Configuration Entry]", \PEAR_LOG_DEBUG);

        //Extract the required data to build a configuration entry
        $className = $step->type;
        $filePath = $step->filePath;
        $name = $step->Name();

        //Construct a new configuration entry
        $preProcessorStep = new \Swiftriver\Core\ObjectModel\PreProcessingStepEntry($name, $className, $filePath);

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [END: Constructing the PreProcessingStep Configuration Entry]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [START: Adding the pre processor to the configuration]", \PEAR_LOG_DEBUG);

        //Get the currently configured steps
        $config = \Swiftriver\Core\Setup::PreProcessingStepsConfiguration();
        $numberOfPreProcessors = count($config->PreProcessingSteps);

        //See if this step is already in there
        for($i=0; $i<$numberOfPreProcessors; $i++) 
            if($config->PreProcessingSteps[$i]->name == $preProcessorStep->name) 
                $index = $i;

        //Add the step to the configuration framework
        if(isset($index)) 
            $config->PreProcessingSteps[$index] = $preProcessorStep;
        else 
            $config->PreProcessingSteps[] = $preProcessorStep;

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [END: Adding the pre processor to the configuration]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [START: Saving the configuration]", \PEAR_LOG_DEBUG);

        //Save the config to file.
        $config->Save();

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [END: Saving the configuration]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::PreProcessingSteps::ActivatePreProcessingStep::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        parent::FormatMessage("OK");
    }
}
?>