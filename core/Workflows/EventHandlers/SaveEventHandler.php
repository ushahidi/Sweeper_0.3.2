<?php
namespace Swiftriver\Core\Workflows\EventHandlers;
/**
 * @author mg[at]swiftly[dot]org
 */
class SaveEventHandler extends EventHandlersBase
{
    public function RunWorkflow($json, $key)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [START: Parsing the input JSON]", \PEAR_LOG_DEBUG);

        try
        {
            $name = parent::ParseJsonToEventHandlerName($json);
            $config = parent::ParseJsonToEventHandlerConfiguration($json);
        }
        catch(\Exception $e)
        {
            $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [An Exception was thrown]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [$e]", \PEAR_LOG_ERR);
            return parent::FormatErrorMessage($e);
        }

        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [END: Parsing the input JSON]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [START: Instanciating the event distributor]", \PEAR_LOG_DEBUG);

        $eventDistributor = new \Swiftriver\Core\EventDistribution\EventDistributor();

        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [END: Instanciating the event distributor]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [START: Listing all event handlers]", \PEAR_LOG_DEBUG);

        $handlers = $eventDistributor->ListAllAvailableEventHandlers();

        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [END: Listing all event handlers]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [START: Looking for an event handler with matching name]", \PEAR_LOG_DEBUG);

        foreach($handlers as $handler)
            if($handler->Name() == $name)
                $thisHandler = $handler;

        if(!isset($thisHandler) || $thisHandler == null)
        {
            $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [No event handler was found matching the name $name]", \PEAR_LOG_DEBUG);
            return parent::FormatErrorMessage("No event handler was found matching the name $name");
        }
        
        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [END: Looking for an event handler with matching name]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [START: Adding all the configured properties to the event handler]", \PEAR_LOG_DEBUG);

        $thisConfig = array();

        foreach($thisHandler->ReturnRequiredParameters() as $param)
        {
            foreach($config as $key => $value)
                if($param->name == $key)
                    $param->value = $value;

            $thisConfig[] = $param;
        }

        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [END: Adding all the configured properties to the event handler]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [START: Saving the configuration]", \PEAR_LOG_DEBUG);

        $configuration = \Swiftriver\Core\Setup::DynamicModuleConfiguration();

        $configuration->Configuration[$name] = $thisConfig;

        $configuration->Save();

        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [END: Saving the configuration]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::EventHandlers::SaveEventHandlers::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        parent::FormatMessage("OK");
    }
}
?>