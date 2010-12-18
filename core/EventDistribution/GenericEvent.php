<?php
namespace Swiftriver\Core\EventDistribution;
/**
 * Generic event class used to hold the name and event arguments that are
 * to be distributed through the system.
 * @author mg[at]swiftly[dot]org
 */
class GenericEvent
{
    /**
     * The name of the event, event handlers should be listening
     * to raised events and filtering only the ones they want
     * to handle based on this attribute. All
     * the strings returned should be accessed throught the
     * \Swiftriver\Core\EventDistribution\EventEnumeration
     * static enumerator by calling EventEnumeration::[event]
     *
     * @var string
     */
    public $name;

    /**
     * The event arguments, these can be anything held in an
     * associative aray, Event handlers can interpret the data
     * in this array to act on the raising of this event
     *
     * @var Associative Array
     */
    public $arguments;

    /**
     * The constructor for a generic event
     * 
     * @param string $name
     * @param array() $arguments
     */
    public function __construct($name, $arguments)
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }
}
?>
