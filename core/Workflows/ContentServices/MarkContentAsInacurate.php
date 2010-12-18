<?php
namespace Swiftriver\Core\Workflows\ContentServices;
/**
 * @author mg@swiftly
 */
class MarkContentAsInacurate extends ContentServicesBase
{
    public function RunWorkflow($json, $key)
    {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        try
        {
            //call the parser to get the ID
            $id = parent::ParseJSONToContentID($json);
            $markerId = parent::ParseJSONToMarkerID($json);
            $reason = parent::ParseJSONToInacurateReason($json);
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");

        }

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [START: Constructing the repository]", \PEAR_LOG_DEBUG);

        try
        {
            //Get the content repository
            $repository = new \Swiftriver\Core\DAL\Repositories\ContentRepository();
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [END: Constructing the repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [START: Getting the subject content]", \PEAR_LOG_DEBUG);

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
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [END: Getting the subject content]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [START: Setting the state to acurate]", \PEAR_LOG_DEBUG);

        //Use the state controller to change the state of the the content to acurate
        $content = \Swiftriver\Core\StateTransition\StateController::MarkContentInaccurate($content);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [END: Setting the state to acurate]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [START: Increment source score]", \PEAR_LOG_DEBUG);

        //get the source from the content
        $source = $content->source;

        //if the score is null - not yet rated, then set it
        if(!isset($source->score) || $source->score == null) 
            $source->score = 0; //baseline of 0%

        //Set the decrement
        $decrement = ($reason == "falsehood") ? 2 : 1;

        //if the scoure is not already at the maximum
        if($source->score > ($decrement - 1)) 
            $source->score = $source->score - $decrement;

        //set the scource back to the content
        $content->source = $source;

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [END: Increment source score]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [START: Saving the content and source]", \PEAR_LOG_DEBUG);

        try
        {
            //save the content to the repo
            $repository->SaveContent(array($content));
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [END: Saving the content and source]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [START: Recording the transaction]", \PEAR_LOG_DEBUG);

        try
        {
            //get the trust log repo
            $trustLogRepo = new \Swiftriver\Core\DAL\Repositories\TrustLogRepository();

            //get the source id
            $sourceId = $content->source->id;

            //record the new entry
            $trustLogRepo->RecordSourceScoreChange($sourceId, $markerId, -1);
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::MarkContentAsInacurate::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [END: Recording the transaction]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::MarkContentAsInacurate::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        $return = json_encode(array(
                        "sourceId" => $content->source->id,
                        "sourceScore" => $content->source->score
                  ));

        return $return;
    }
}
?>
