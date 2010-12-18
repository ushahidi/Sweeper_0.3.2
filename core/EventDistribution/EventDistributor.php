<?php
namespace Swiftriver\Core\EventDistribution;
/**
 * Class that handles the distribution of system events to configured
 * event handlers.
 * @author mg[at]swiftly[dot]org
 */
class EventDistributor 
{
    /**
     * Array to hold all the configured Event Handlers
     * @var IEventHandler[]
     */
    private $eventHandlers;
    
    /**
     * The constructor for the EventDistributor class
     */
    public function __construct() 
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        
        $logger->log("Core::EventDistribution::EventDistributor::__construct [Method invoked]", \PEAR_LOG_DEBUG);
        
        $logger->log("Core::EventDistribution::EventDistributor::__construct [START: Adding configured event handlers]", \PEAR_LOG_DEBUG);
        
        $this->eventHandlers = \Swiftriver\Core\Setup::EventDistributionConfiguration()->EventHandlers;
        
        $logger->log("Core::EventDistribution::EventDistributor::__construct [END: Adding configured event handlers]", \PEAR_LOG_DEBUG);

        $logger->log("Core::EventDistribution::EventDistributor::__construct [Method finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Function to Raise system events and distribute them to the configured 
     * Event handlers.
     * 
     * @param GenericEvent $event
     */
    public function RaiseAndDistributeEvent($event) 
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        
        $logger->log("Core::EventDistribution::EventDistributor [Method invoked]", \PEAR_LOG_DEBUG);

        $modulesDirectory = \Swiftriver\Core\Setup::Configuration()->ModulesDirectory;
        
        $configuration = \Swiftriver\Core\Setup::Configuration();

        if(isset($this->eventHandlers) && count($this->eventHandlers) > 0) 
        {
            foreach($this->eventHandlers as $eventHandler) 
            {
                //Get the class name from config
                $className = $eventHandler->className;

                //get the file path from config
                $filePath = $modulesDirectory . $eventHandler->filePath;

                $logger->log("Core::EventDistribution::EventDistributor::RaiseAndDistributeEvent [START: Including event handler: $filePath]", \PEAR_LOG_DEBUG);

                //Include the file
                include_once($filePath);

                $logger->log("Core::EventDistribution::EventDistributor::RaiseAndDistributeEvent [END: Including event handler: $filePath]", \PEAR_LOG_DEBUG);

                $logger->log("Core::EventDistribution::EventDistributor::RaiseAndDistributeEvent [START: Instanciating event handler: $className]", \PEAR_LOG_DEBUG);

                try 
                {
                    //Instanciate the event handlerr
                    $handler = new $className();
                }
                catch (\Exception $e) 
                {
                    $logger->log("Core::EventDistribution::EventDistributor::RaiseAndDistributeEvent [$e]", \PEAR_LOG_ERR);
                    
                    $logger->log("Core::EventDistribution::EventDistributor::RaiseAndDistributeEvent [Unable to run event distribution for event handler: $className]", \PEAR_LOG_ERR);
                    
                    continue;
                }

                $logger->log("Core::EventDistribution::EventDistributor::RaiseAndDistributeEvent [END: Instanciating event handler: $className]", \PEAR_LOG_DEBUG);

                $logger->log("Core::EventDistribution::EventDistributor::RaiseAndDistributeEvent [START: Run event distribution for $className]", \PEAR_LOG_DEBUG);

                try 
                {
                    //Loop throught the events that this handler subscribes to
                    foreach($handler->ReturnEventNamesToHandle() as $eventName) 
                    {
                        //If we have a match run the handle event method
                        if($eventName == $event->name) 
                            $handler->HandleEvent($event, $configuration, $logger);
                    }
                }
                catch (\Exception $e)
                {
                    $logger->log("Core::EventDistribution::EventDistributor::RaiseAndDistributeEvent [$e]", \PEAR_LOG_ERR);
                    $logger->log("Core::EventDistribution::EventDistributor::RaiseAndDistributeEvent [Unable to run event distribution for event handler: $className]", \PEAR_LOG_ERR);
                }

                $logger->log("Core::EventDistribution::EventDistributor::RaiseAndDistributeEvent [END: Run event distribution for $className]", \PEAR_LOG_DEBUG);
            }
        } 
        else
        {
            $logger->log("Core::EventDistribution::EventDistributor::RaiseAndDistributeEvent [No event handlers found to run]", \PEAR_LOG_DEBUG);
        }

        $logger->log("Core::EventDistribution::EventDistributor::RaiseAndDistributeEvent [Method finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Returns all the classes that implement the
     * IEventHandler interface.
     * @return IEventHandler[]
     */
    public function ListAllAvailableEventHandlers()
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::EventDistribution::EventDistributor::ListAllAvailableEventHandlers [Method invoked]", \PEAR_LOG_DEBUG);

        $handlers = array();

        $modulesDirectory = \Swiftriver\Core\Setup::Configuration()->ModulesDirectory;

        $dirItterator = new \RecursiveDirectoryIterator($modulesDirectory);

        $iterator = new \RecursiveIteratorIterator($dirItterator, \RecursiveIteratorIterator::SELF_FIRST);

        foreach($iterator as $file)
        {
            if(!$file->isFile())
                continue;

            $filePath = $file->getPathname();
            
            if(!strpos($filePath, "EventHandler.php"))
                continue;

            try
            {
                include_once($filePath);

                $typeString = "\\Swiftriver\\EventHandlers\\".$file->getFilename();

                $type = str_replace(".php", "", $typeString);

                $object = new $type();
                
                if($object instanceof IEventHandler)
                {
                    $logger->log("Core::EventDistribution::EventDistributor::ListAllAvailableEventHandlers [Adding type $type]", \PEAR_LOG_DEBUG);

                    $object->filePath = str_replace($modulesDirectory, "", $filePath);

                    $object->type = $type;

                    $handlers[] = $object;
                }
            }
            catch(\Exception $e)
            {
                $logger->log("Core::EventDistribution::EventDistributor::ListAllAvailableEventHandlers [error adding type $type]", \PEAR_LOG_DEBUG);

                $logger->log("Core::EventDistribution::EventDistributor::ListAllAvailableEventHandlers [$e]", \PEAR_LOG_ERR);

                continue;
            }
        }

        $logger->log("Core::EventDistribution::EventDistributor::ListAllAvailableEventHandlers [Method finished]", \PEAR_LOG_DEBUG);

        return $handlers;
    }
}
?>