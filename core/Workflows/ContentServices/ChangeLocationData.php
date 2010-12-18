<?php
namespace Swiftriver\Core\Workflows\ContentServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class ChangeLocationData extends ContentServicesBase
{
    public function RunWorkflow($json, $key)
    {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::ServiceAPI::ContentServices::ChangeLocationData::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::ServiceAPI::ContentServices::ChangeLocationData::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        try
        {
            $gis = parent::ParseJSONToGIS($json);
            $id = parent::ParseJSONToContentID($json);
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeLocationData::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeLocationData::RunWorkflow [$e]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeLocationData::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::ServiceAPI::ContentServices::ChangeLocationData::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        try
        {
            //Get the content repository
            $repository = new \Swiftriver\Core\DAL\Repositories\ContentRepository();
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeLocationData::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeLocationData::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeLocationData::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::ServiceAPI::ContentServices::ChangeLocationData::RunWorkflow [END: Constructing the repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::ChangeLocationData::RunWorkflow [START: Getting the subject content]", \PEAR_LOG_DEBUG);

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
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeLocationData::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeLocationData::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeLocationData::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::ServiceAPI::ContentServices::ChangeLocationData::RunWorkflow [END: Getting the subject content]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::ChangeLocationData::RunWorkflow [START: Setting the GIS Data]", \PEAR_LOG_DEBUG);

        //Use the state controller to change the state of the the content to acurate
        $content->gisData = $gis;

        $logger->log("Core::ServiceAPI::ContentServices::ChangeLocationData::RunWorkflow [END: Setting the GIS Data]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::ChangeLocationData::RunWorkflow [START: Saving the content and source]", \PEAR_LOG_DEBUG);

        try
        {
            //save the content to the repo
            $repository->SaveContent(array($content));
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeLocationData::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeLocationData::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeLocationData::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::ServiceAPI::ContentServices::ChangeLocationData::RunWorkflow [END: Saving the content and source]", \PEAR_LOG_DEBUG);


        $logger->log("Core::ServiceAPI::ContentServices::ChangeLocationData::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        return $return;
    }
}
?>