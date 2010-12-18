<?php
namespace Swiftriver\Core\Workflows\ChannelServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class DeleteChannel extends ChannelServicesBase
{
    /**
     * Removes a Channel from the DAL
     * 
     * @param string $json
     * @return string 
     */
    public function RunWorkflow($json, $key)
    {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

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
            $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [START: Constructing Repository]", \PEAR_LOG_DEBUG);

        try
        {
            //Construct a new repository
            $repository = new \Swiftriver\Core\DAL\Repositories\ChannelRepository();
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [END: Constructing Repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [START: Deleting the Channel]", \PEAR_LOG_DEBUG);

        try
        {
            //Delete the Channel from the data store
            $repository->RemoveChannels(array($id));
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [END: Deletig the Channel]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::DeleteChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        //return an OK messagae
        return parent::FormatMessage("OK");
    }
}
?>
