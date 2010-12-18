<?php
namespace Swiftriver\Core\Workflows\ChannelServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class GetAllChannels extends ChannelServicesBase
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
        $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [START: Constructing Repository]", \PEAR_LOG_DEBUG);

        try
        {
            //Construct a new repository
            $repository = new \Swiftriver\Core\DAL\Repositories\ChannelRepository();
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [END: Constructing Repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [START: Listing all Channels]", \PEAR_LOG_DEBUG);

        try
        {
            //Get all the Channels
            $channels = $repository->ListAllChannels();
        }
        catch (\Exception $e)
        {
            //get the exception message 
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

         $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [END: Listing all Channels]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [START: Parsing channel processing jobs to JSON]", \PEAR_LOG_DEBUG);

        try
        {
            //Parse the JSON input
            $json = parent::ParseChannelsToJSON($channels);
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [END: Parsing channel processing jobs to JSON]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::GetAllChannels::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        //return the channels as JSON
        return parent::FormatReturn($json);
    }
}
?>
