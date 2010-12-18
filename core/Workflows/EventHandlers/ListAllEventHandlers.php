<?php
namespace Swiftriver\Core\Workflows\EventHandlers;
/**
 * @author mg[at]swiftly[dot]org
 */
class ListAllEventHandlers extends EventHandlersBase
{
    public function RunWorkflow($key)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Workflows::EventHandlers::ListAllEventHandlers::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::EventHandlers::ListAllEventHandlers::RunWorkflow [START: Constructing the Event Distributor]", \PEAR_LOG_DEBUG);

        $eventDistributer = new \Swiftriver\Core\EventDistribution\EventDistributor();

        $logger->log("Core::Workflows::EventHandlers::ListAllEventHandlers::RunWorkflow [END: Constructing the Event Distributor]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::ListAllEventHandlers::RunWorkflow [START: Getting a full list of Event Handlers from the event distributor]", \PEAR_LOG_DEBUG);

        $handlers = $eventDistributer->ListAllAvailableEventHandlers();

        $logger->log("Core::Workflows::EventHandlers::ListAllEventHandlers::RunWorkflow [END: Getting a full list of Event Handlers from the event distributor]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::ListAllEventHandlers::RunWorkflow [START: Finding out which event handlers are active]", \PEAR_LOG_DEBUG);

        $config = \Swiftriver\Core\Setup::EventDistributionConfiguration();

        $activeHandlers = $config->EventHandlers;

        if($activeHandlers != null && is_array($activeHandlers) && $handlers != null && is_array($handlers))
            foreach($activeHandlers as $activeHandler)
                foreach($handlers as $handler)
                    if($handler->Name() == $activeHandler->name)
                        $handler->active = true;

        $logger->log("Core::Workflows::EventHandlers::ListAllEventHandlers::RunWorkflow [END: Finding out which event handlers are active]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::ListAllEventHandlers::RunWorkflow [START: Parsing the handlers to JSON]", \PEAR_LOG_DEBUG);

        $json = parent::ParseHandlersToJson($handlers);

        $logger->log("Core::Workflows::EventHandlers::ListAllEventHandlers::RunWorkflow [END: Parsing the handlers to JSON]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::ListAllEventHandlers::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        return parent::FormatReturn($json);
    }
}
?>