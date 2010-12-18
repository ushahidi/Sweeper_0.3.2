<?php
namespace Swiftriver\Core\Workflows\EventHandlers;
/**
 * @author mg[at]swiftly[dot]org
 */
class ActivateEventHandler extends EventHandlersBase
{
    public function RunWorkflow($json, $key)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        try
        {
            $name = parent::ParseJsonToEventHandlerName($json);
        }
        catch(\Exception $e)
        {
            $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [$e]", \PEAR_LOG_ERR);
            return parent::FormatErrorMessage($e);
        }

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [START: Instanciating the event distributor]", \PEAR_LOG_DEBUG);

        $eventDistributor = new \Swiftriver\Core\EventDistribution\EventDistributor();

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [END: Instanciating the event distributor]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [START: Getting all the availabel event handlers]", \PEAR_LOG_DEBUG);

        $handlers = $eventDistributor->ListAllAvailableEventHandlers();

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [END: Getting all the availabel event handlers]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [START: Looking for a handler with the matching name]", \PEAR_LOG_DEBUG);

        foreach($handlers as $handler)
            if($handler->Name() == $name)
                $thisHandler = $handler;

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [END: Looking for a handler with the matching name]", \PEAR_LOG_DEBUG);

        if(!isset($thisHandler) || $thisHandler == null)
            return parent::FormatErrorMessage("No event handler found matching the name $name");

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [START: Constructing the configuration entry]", \PEAR_LOG_DEBUG);

        $className = $thisHandler->type;

        $filePath = $thisHandler->filePath;

        $configEntry = new \Swiftriver\Core\ObjectModel\EventHandlerEntry($name, $className, $filePath);

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [END: Constructing the configuration entry]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [START: Extracting the configured event handlers from the config system]", \PEAR_LOG_DEBUG);

        $config = \Swiftriver\Core\Setup::EventDistributionConfiguration();

        $configuredEventHandlers = $config->EventHandlers;

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [END: Extracting the configured event handlers from the config system]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [START: Looking for an existing config entry for this handler]", \PEAR_LOG_DEBUG);

        for($i=0; $i<count($configuredEventHandlers); $i++)
            if($configuredEventHandlers[$i]->name == $name)
                $index = $i;

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [END: Looking for an existing config entry for this handler]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [START: Adding a configuration entry for this event handler]", \PEAR_LOG_DEBUG);

        if(isset($index))
            $configuredEventHandlers[$index] = $configEntry;
        else 
            $configuredEventHandlers[] = $configEntry;
        

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [END: Adding a configuration entry for this event handler]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [START: Saving the configuration]", \PEAR_LOG_DEBUG);

        $config->EventHandlers = $configuredEventHandlers;

        $config->Save();

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [END: Saving the configuration]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::ActivateEventHandlers::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        return parent::FormatMessage("OK");
    }
}
?>