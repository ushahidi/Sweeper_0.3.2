<?php
namespace Swiftriver\Core\PreProcessing;
/**
 * Class that manages the process of passing all content 
 * through the configured stack of pre processors
 * 
 * @author mg[at]swiftly[dot]org
 */
class PreProcessor
{
    /**
     * Array of all configured pre processing steps.
     * @var IPreProcessingStep[]
     */
    private $preProcessingSteps;

    /**
     * Constructor for te PreProcessor
     * @var string|null $modulesDirectory
     */
    public function __construct($modulesDirectory = null)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::PreProcessing::PreProcessor::__construct [Method invoked]", \PEAR_LOG_DEBUG);
        
        $logger->log("Core::PreProcessing::PreProcessor::__construct [START: Adding configured pre processors]", \PEAR_LOG_DEBUG);
        
        $this->preProcessingSteps = \Swiftriver\Core\Setup::PreProcessingStepsConfiguration()->PreProcessingSteps;
        
        $logger->log("Core::PreProcessing::PreProcessor::__construct [END: Adding configured pre processors]", \PEAR_LOG_DEBUG);

        $logger->log("Core::PreProcessing::PreProcessor::__construct [Method finished]", \PEAR_LOG_DEBUG);
    }

    public function PreProcessContent($content)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [Method invoked]", \PEAR_LOG_DEBUG);

        $modulesDirectory = \Swiftriver\Core\Setup::Configuration()->ModulesDirectory;

        $configuration = \Swiftriver\Core\Setup::Configuration();

        if(isset($this->preProcessingSteps) && count($this->preProcessingSteps) > 0)
        {
            foreach($this->preProcessingSteps as $preProcessingStep)
            {
                //Get the class name from config
                $className = $preProcessingStep->className;

                //get the file path from config
                $filePath = $modulesDirectory . $preProcessingStep->filePath;

                $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [START: Including pre processor: $filePath]", \PEAR_LOG_DEBUG);

                //Include the file
                include_once($filePath);

                $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [END: Including pre processor: $filePath]", \PEAR_LOG_DEBUG);

                $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [START: Instanciating pre processor: $className]", \PEAR_LOG_DEBUG);

                try
                {
                    //Instanciate the pre processor
                    $preProcessor = new $className();
                }
                catch (\Exception $e)
                {
                    $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [$e]", \PEAR_LOG_ERR);
                    
                    $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [Unable to run PreProcessing for preprocessor $className]", \PEAR_LOG_ERR);

                    continue;
                }

                $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [END: Instanciating pre processor: $className]", \PEAR_LOG_DEBUG);

                $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [START: Run PreProcessing for $className]", \PEAR_LOG_DEBUG);

                try
                {
                    //Run the preocess method on the pre processor
                    $content = $preProcessor->Process($content, $configuration, $logger);
                }
                catch (\Exception $e)
                {
                    $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [$e]", \PEAR_LOG_ERR);

                    $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [Unable to run PreProcessing for preprocessor $className]", \PEAR_LOG_ERR);
                }

                $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [END: Run PreProcessing for $className]", \PEAR_LOG_DEBUG);
            }
        } 
        else
        {
            $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [No PreProcessing Steps found to run]", \PEAR_LOG_DEBUG);
        }

        $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [Method finished]", \PEAR_LOG_DEBUG);
        
        //Return the content
        return $content;
    }

    /**
     * Returns all the classes that implment the IPreProcessor interface
     *
     * @return IPreProcessingStep[]
     */
    public function ListAllAvailablePreProcessingSteps()
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::PreProcessing::PreProcessor::ListAllAvailablePreProcessingSteps [Method invoked]", \PEAR_LOG_DEBUG);

        //set up the array to hold the steps
        $steps = array();

        //Get the path of the modules directory
        $modulesDirectory = \Swiftriver\Core\Setup::Configuration()->ModulesDirectory;

        //Create a recursive directory ittorator
        $dirItterator = new \RecursiveDirectoryIterator($modulesDirectory);

        $iterator = new \RecursiveIteratorIterator($dirItterator, \RecursiveIteratorIterator::SELF_FIRST);

        //Itterate over all the files in all the directories
        foreach($iterator as $file)
        {
            //If not a file continue
            if(!$file->isFile())
                continue;

            //get the full file path
            $filePath = $file->getPathname();

            //If not a pre processing step then continue
            if(!strpos($filePath, "PreProcessingStep.php"))
                continue;

            try
            {
                //Include the file
                include_once($filePath);

                //create a type string for the pre processing step
                $typeString = "\\Swiftriver\\PreProcessingSteps\\".$file->getFilename();

                //remove the .php extension
                $type = str_replace(".php", "", $typeString);

                //instanciate the pre processing step
                $object = new $type();

                //Check that the object implements the IPreProcessingStep
                if(!($object instanceof IPreProcessingStep))
                    continue;

                $logger->log("Core::PreProcessing::PreProcessor::ListAllAvailablePreProcessingSteps [Adding type $type]", \PEAR_LOG_DEBUG);

                //Add the file name to the step element
                $object->filePath = str_replace($modulesDirectory, "", $filePath);

                //Add the type to the step element
                $object->type = $type;

                //Add the object to the array
                $steps[] = $object;
            }
            catch(\Exception $e)
            {
                $logger->log("Core::PreProcessing::PreProcessor::ListAllAvailablePreProcessingSteps [error adding type $type]", \PEAR_LOG_DEBUG);

                $logger->log("Core::PreProcessing::PreProcessor::ListAllAvailablePreProcessingSteps [$e]", \PEAR_LOG_ERR);

                continue;
            }
        }

        $logger->log("Core::PreProcessing::PreProcessor::ListAllAvailablePreProcessingSteps [Method finished]", \PEAR_LOG_DEBUG);

        return $steps;
    }
}
?>