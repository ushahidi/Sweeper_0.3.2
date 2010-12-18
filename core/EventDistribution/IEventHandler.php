<?php
namespace Swiftriver\Core\EventDistribution;
/**
 * Interface for all Event Handlers
 * @author mg[at]swiftly[dot]org
 */
interface IEventHandler
{
    /**
     * This method should return the name of the event handler 
     * that you implement. This name should be unique across all
     * event handlers and should be no more that 50 chars long
     * 
     * @return string
     */
    public function Name();

    /**
     * This method should return a description describing what
     * exactly it is that your Event Handler does
     *
     * @return string
     */
    public function Description();

    /**
     * This method returns an array of the required paramters that
     * are nessesary to configure this event handler.
     *
     * @return \Swiftriver\Core\ObjectModel\ConfigurationElement[]
     */
    public function ReturnRequiredParameters();

    /**
     * This method should return the names of the events
     * that your EventHandler wishes to subscribe to. All
     * the strings returned should be accessed throught the
     * \Swiftriver\Core\EventDistribution\EventEnumeration 
     * static enumerator by calling EventEnumeration::[event]
     *
     * @return string[]
     */
    public function ReturnEventNamesToHandle();

    /**
     * Given a GenericEvent object, this method should do
     * something amazing with the data contained in the
     * event arguments.
     *
     * @param GenericEvent $event
     * @param \Swiftriver\Core\Configuration\ConfigurationHandlers\CoreConfigurationHandler $configuration
     * @param \Log $logger
     */
    public function HandleEvent($event, $configuration, $logger);
}
?>
