<?php
namespace Swiftriver\Core\Workflows\ContentServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class MarkContentAsAcurate extends ContentServicesBase
{
    public function RunWorkflow($json, $key)
    {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        try
        {
            //call the parser to get the ID
            $id = parent::ParseJSONToContentID($json);
            $markerId = parent::ParseJSONToMarkerID($json);
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");

        }

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [START: Constructing the repository]", \PEAR_LOG_DEBUG);

        try
        {
            //Get the content repository
            $repository = new \Swiftriver\Core\DAL\Repositories\ContentRepository();
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [END: Constructing the repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [START: Getting the subject content]", \PEAR_LOG_DEBUG);

        try
        {
            //get the content array for the repo
            $contentArray = $repository->GetContent(array($id));

            //try and get the first item
            $content = reset($contentArray);

            //check that its not null
            if(!isset($content) || $content == null) 
                throw new \Exception("No content was returned for the ID: $id");
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [END: Getting the subject content]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [START: Setting the state to acurate]", \PEAR_LOG_DEBUG);

        //Use the state controller to change the state of the the content to acurate
        $content = \Swiftriver\Core\StateTransition\StateController::MarkContentAcurate($content);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [END: Setting the state to acurate]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [START: Increment source score]", \PEAR_LOG_DEBUG);

        //get the source from the content
        $source = $content->source;

        //if the score is null - not yet rated, then set it
        if(!isset($source->score) || $source->score == null) 
            $source->score = 0; //baseline of 0%

        //if the scoure is not already at the maximum
        if($source->score < 99) 
            $source->score = $source->score + 2;

        //set the scource back to the content
        $content->source = $source;

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [END: Increment source score]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [START: Saving the content and source]", \PEAR_LOG_DEBUG);

        try
        {
            //save the content to the repo
            $repository->SaveContent(array($content));
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [END: Saving the content and source]", \PEAR_LOG_DEBUG);

        /*
            This is depricated after 0.5.0

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [START: Recording the transaction]", \PEAR_LOG_DEBUG);

        try
        {
            //get the trust log repo
            $trustLogRepo = new \Swiftriver\Core\DAL\Repositories\TrustLogRepository();

            //get the source id
            $sourceId = $content->source->id;

            //record the new entry
            $trustLogRepo->RecordSourceScoreChange($sourceId, $markerId, 1);
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsAcurate::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [END: Recording the transaction]", \PEAR_LOG_DEBUG);

        */

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [START: Event Distribution]", \PEAR_LOG_DEBUG);

        $event = new \Swiftriver\Core\EventDistribution\GenericEvent(
                \Swiftriver\Core\EventDistribution\EventEnumeration::$MarkContentAsAccurate,
                $content);

        $eventDistributor = new \Swiftriver\Core\EventDistribution\EventDistributor();

        $eventDistributor->RaiseAndDistributeEvent($event);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [END: Event Distribution]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsAcurate::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        $return = json_encode(array(
                        "sourceId" => $content->source->id,
                        "sourceScore" => $content->source->score
                  ));

        return $return;
    }
}
?>