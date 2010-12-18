<?php
namespace Swiftriver\Core\Workflows\ContentServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class UpdateContentTagging extends ContentServicesBase
{
    public function RunWorkflow($json, $key)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [START: Parsing JSON input]", \PEAR_LOG_DEBUG);

        try
        {
            $contentId = $this->ParseJSONToContentID($json);

            $tagsRemoved = $this->ParseJSONToTags($json, "tagsRemoved");

            $tagsAdded = $this->ParseJSONToTags($json, "tagsAdded");

            if(\count($tagsRemoved) < 1 && \count($tagsAdded) < 1)
                throw new \InvalidArgumentException ("No tagsRemoved or tagsAdded were supplied");
        }
        catch(\Exception $e)
        {
            $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [An exception was thrown:]", \PEAR_LOG_ERR);

            $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [$e]", \PEAR_LOG_ERR);

            $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

            $message = $e->getMessage();

            return parent::FormatErrorMessage("An exception was thrown: $message)");
        }

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [END: Parsing JSON input]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [START: Constructing the repository]", \PEAR_LOG_DEBUG);

        try
        {
            //Get the content repository
            $repository = new \Swiftriver\Core\DAL\Repositories\ContentRepository();
        }
        catch (\Exception $e)
        {
            $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);

            $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [$e]", \PEAR_LOG_ERR);

            $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

            $message = $e->getMessage();

            return parent::FormatErrorMessage("An exception was thrown: $message)");
        }

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [END: Constructing the repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [START: Getting the subject content]", \PEAR_LOG_DEBUG);

        try
        {
            //get the content array for the repo
            $contentArray = $repository->GetContent(array($contentId));

            //try and get the first item
            $content = reset($contentArray);

            //check that its not null
            if(!isset($content) || $content == null)
                throw new \Exception("No content was returned for the ID: $contentId");
        }
        catch (\Exception $e)
        {
            $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);

            $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [$e]", \PEAR_LOG_ERR);

            $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

            $message = $e->getMessage();

            return parent::FormatErrorMessage("An exception was thrown: $message)");
        }

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [END: Getting the subject content]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [START: Making Tagging Changes]", \PEAR_LOG_DEBUG);

        $originalTags = $content->tags;

        $content->tags = array();

        foreach($originalTags as $originalTag)
        {
            $shouldBeRemoved = false;

            foreach($tagsRemoved as $tagRemoved)
                if($tagRemoved->text == $originalTag->text)
                    $shouldBeRemoved = true;

            if(!$shouldBeRemoved)
                $content->tags[] = $originalTag;
        }

        foreach($tagsAdded as $tag)
            $content->tags[] = $tag;

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [END: Making Tagging Changes]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [START: Saving Content]", \PEAR_LOG_DEBUG);

        try
        {
            $repository->SaveContent(array($content));
        }
        catch(\Exception $e)
        {
            $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);

            $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [$e]", \PEAR_LOG_ERR);

            $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

            $message = $e->getMessage();

            return parent::FormatErrorMessage("An exception was thrown: $message)");
        }

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [END: Saving Content]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [START: Event Distribution]", \PEAR_LOG_DEBUG);

        $eventArguments = array(
            "originalTags" => $originalTags,
            "tagsAdded" => $tagsAdded,
            "tagsRemoved" => $tagsRemoved);

        $event = new \Swiftriver\Core\EventDistribution\GenericEvent(
            \Swiftriver\Core\EventDistribution\EventEnumeration::$UpdateContentTagging,
            $eventArguments);

        $eventDistributor = new \Swiftriver\Core\EventDistribution\EventDistributor();

        $eventDistributor->RaiseAndDistributeEvent($event);

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [END: Event Distribution]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::UpdateContentTagging::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        return parent::FormatMessage("OK");
    }
}
?>
