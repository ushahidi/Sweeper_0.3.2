<?php
namespace Swiftriver\Core\Workflows\ChannelServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class DeactivateChannel extends ChannelServicesBase
{
    /**
     * Deactivates a Channel based on the Channel ID
     * encode in the JSON param
     *
     * @param string $json
     * @return string $json
     */
    public function RunWorkflow($json, $key)
    {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

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
            $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [START: Constructing Repository]", \PEAR_LOG_DEBUG);

        try
        {
            //Construct a new repository
            $repository = new \Swiftriver\Core\DAL\Repositories\ChannelRepository();
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [END: Constructing Repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [START: Getting the Channel from the repository]", \PEAR_LOG_DEBUG);

        try
        {
            //Get the channel from the repo
            $channel = reset($repository->GetChannelsById(array($id)));
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [END: Getting the Channel from the repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [START: Marking Channel as inactive and saving to the repository]", \PEAR_LOG_DEBUG);

        try
        {
            //set the active flag to true
            $channel->active = false;

            //save the channel back to the repo
            $repository->SaveChannels(array($channel));
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [END: Marking Channel as inactive and saving to the repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::DeactivateChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        //return an OK messagae
        return parent::FormatMessage("OK");
    }
}
?>