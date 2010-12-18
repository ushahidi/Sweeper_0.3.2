<?php
namespace Swiftriver\Core\Workflows\SourceServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class GetAllSources extends SourceServicesBase
{
    /**
     * List all Channel Processing Jobs in the Data Store
     *
     * @param string $json
     * @return string $json
     */
    public function RunWorkflow($key)
    {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [START: Constructing Repository]", \PEAR_LOG_DEBUG);

        try
        {
            //Construct a new repository
            $repository = new \Swiftriver\Core\DAL\Repositories\SourceRepository();
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [END: Constructing Repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [START: Listing all sources]", \PEAR_LOG_DEBUG);

        try
        {
            //Get all the sources
            $sources = $repository->ListAllSources();
        }
        catch (\Exception $e)
        {
            //get the exception message 
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [END: Listing all sources]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [START: Parsing channel processing jobs to JSON]", \PEAR_LOG_DEBUG);

        try
        {
            //Parse the JSON input
            $json = parent::ParseSourcesToJSON($sources);
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [END: Parsing channel processing jobs to JSON]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::GetAllSources::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        //return the channels as JSON
        return parent::FormatReturn($json);
    }
}
?>
