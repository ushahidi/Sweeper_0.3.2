<?php
namespace Swiftriver\Core\Workflows\SourceServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class GetSource extends SourceServicesBase
{
    /**
     * Gets a source in the Data Store whos
     * id is supplied in $json parameter
     *
     * @param string $json
     * @return string $json
     */
    public function RunWorkflow($json, $key)
    {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        //try to parse the id from the JSON
        try
        {
            //get the ID from the JSON
            $id = parent::ParseJSONToId($json);
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);


        $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [START: Constructing Repository]", \PEAR_LOG_DEBUG);

        try
        {
            //Construct a new repository
            $repository = new \Swiftriver\Core\DAL\Repositories\SourceRepository();
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [END: Constructing Repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [START: Getting the source]", \PEAR_LOG_DEBUG);

        try
        {
            //Get all the channel processing jobs
            $source = reset($repository->GetSourcesById(array($id)));
        }
        catch (\Exception $e)
        {
            //get the exception message 
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [END: Getting the processing job]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [START: Parsing source to JSON]", \PEAR_LOG_DEBUG);

        try
        {
            //Parse the JSON input
            $json = json_encode($source);
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [END: Parsing source to JSON]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::GetSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        //return the channels as JSON
        return parent::FormatReturn($json);
    }
}
?>
