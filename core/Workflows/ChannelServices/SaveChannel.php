<?php
namespace Swiftriver\Core\Workflows\ChannelServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class SaveChannel extends ChannelServicesBase
{
    /**
     * Adds the pre processing job to the DAL
     *
     * @param string $json
     * @return string $json
     */
    public function RunWorkflow($json, $key)
    {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        try
        {
            //Parse the JSON input
            $channel = parent::ParseJSONToChannel($json);
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [START: Constructing Repository]", \PEAR_LOG_DEBUG);

        try
        {
            //Construct a new repository
            $repository = new \Swiftriver\Core\DAL\Repositories\ChannelRepository();
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [END: Constructing Repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [START: Saving Channel]", \PEAR_LOG_DEBUG);

        try
        {
            //Add the Channel to the repository
            $repository->SaveChannels(array($channel));
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [END: Saving Channel]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::SaveChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        //return an OK messagae
        return parent::FormatMessage("OK");
    }
}
?>
