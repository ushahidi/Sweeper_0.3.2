<?php
namespace Swiftriver\Core\Workflows\ContentServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class ChangeContentState extends ContentServicesBase
{
    public function RunWorkflow($json, $key)
    {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::ServiceAPI::ContentServices::ChangeContentState::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::ServiceAPI::ContentServices::ChangeContentState::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        try
        {
            $newState = parent::ParseJSONToNewContentState($json);
        }
        catch (\Exception $e)
        {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeContentState::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeContentState::RunWorkflow [$e]", \PEAR_LOG_ERR);
            $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ChangeContentState::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::ServiceAPI::ContentServices::ChangeContentState::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        $workflow = null;

        switch ($newState)
        {
            case "accurate" : $workflow = new MarkContentAsAcurate(); break;
            case "chatter" : $workflow = new MarkContentAsChatter(); break;
            case "inaccurate" : $workflow = new MarkContentAsInacurate(); break;
            case "irrelevant" : $workflow = new MarkContentAsIrrelevant(); break;
        }

        $return = ($workflow != null)
            ? $workflow->RunWorkflow($json, $key)
            : parent::FormatErrorMessage("the state '$newState' could not be matched to a workflow.");

        $logger->log("Core::ServiceAPI::ContentServices::ChangeContentState::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        return $return;
    }
}
?>