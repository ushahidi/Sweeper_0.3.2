<?php
namespace Swiftriver\Core\Workflows\ChannelServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class RunNextChannel extends ChannelServicesBase
{
    /**
     * Selects the next due processing job and runs it through the core
     *
     * @return string $json
     */
    public function RunWorkflow($key)
    {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [START: Setting time out]", \PEAR_LOG_DEBUG);
        
        set_time_limit(300);
        
        $timeout = ini_get('max_execution_time');

        $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [END: Setting time out to $timeout]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [START: Constructing Repository]", \PEAR_LOG_DEBUG);

        try
        {
            //Construct a new repository
            $channelRepository = new \Swiftriver\Core\DAL\Repositories\ChannelRepository();
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [END: Constructing Repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [START: Fetching next Channel]", \PEAR_LOG_DEBUG);

        try
        {
            //Get the next due channel processign job
            $channel = $channelRepository->SelectNextDueChannel(time());
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }


        if($channel == null)
        {
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [INFO: No Channel due]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [END: Fetching next Channel]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatMessage("OK");
        }

        $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [END: Fetching next Channel]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [START: Get and parse content]", \PEAR_LOG_DEBUG);

        try
        {
            $SiSPS = new \Swiftriver\Core\Modules\SiSPS\SwiftriverSourceParsingService();
            $rawContent = $SiSPS->FetchContentFromChannel($channel);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);

            try
            {
                $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [START: Mark Channel as in error]", \PEAR_LOG_DEBUG);

                $channel->inprocess = false;
                $channelRepository->SaveChannels(array($channel));
                
                $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [END: Mark Channel as in error]", \PEAR_LOG_DEBUG);
            }
            catch(\Exception $innerE)
            {
                $message = $innerE->getMessage();
                $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
                $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
                $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [This Channel will remain in state - in progress - and will not be run again, manual action must be taken.]", \PEAR_LOG_ERR);
            }

            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

            return parent::FormatErrorMessage("An exception was thrown: $message");
        }


        if(isset($rawContent) && is_array($rawContent) && count($rawContent) > 0)
        {

            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [END: Get and parse content]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [START: Running core processing]", \PEAR_LOG_DEBUG);

            try
            {
                $preProcessor = new \Swiftriver\Core\PreProcessing\PreProcessor();
                $processedContent = $preProcessor->PreProcessContent($rawContent);
            }
            catch (\Exception $e)
            {
                //get the exception message
                $message = $e->getMessage();
                $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
                $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
                $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
                return parent::FormatErrorMessage("An exception was thrown: $message");
            }

            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [END: Running core processing]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [START: Save content to the data store]", \PEAR_LOG_DEBUG);

            try
            {
                $contentRepository = new \Swiftriver\Core\DAL\Repositories\ContentRepository();
                $contentRepository->SaveContent($processedContent);
            }
            catch (\Exception $e)
            {
                //get the exception message
                $message = $e->getMessage();
                $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
                $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
                $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
                return parent::FormatErrorMessage("An exception was thrown: $message");
            }

            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [END: Save content to the data store]", \PEAR_LOG_DEBUG);
        }
        else
        {
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [END: Get and parse content]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [No content found.]", \PEAR_LOG_DEBUG);
        }

        $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [START: Mark channel processing job as complete]", \PEAR_LOG_DEBUG);

        try
        {
            $channel->inprocess = false;
            $channel->lastSuccess = time();
            $channel->nextrun = \strtotime("+1 minute");
            $channelRepository->SaveChannels(array($channel));
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [END: Mark channel processing job as complete]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::ChannelServices::RunNextChannel::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        return parent::FormatMessage("OK");
    }
}
?>
