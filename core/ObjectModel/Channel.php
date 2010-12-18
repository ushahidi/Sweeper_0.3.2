<?php
namespace Swiftriver\Core\ObjectModel;
/**
 * Channel object
 * @author mg[at]swiftly[dot]org
 */
class Channel
{
    /**
     * The genuine unique ID of this Channel
     * 
     * @var string
     */
    public $id;

    /**
     * The friendly name of this Channel
     *
     * @var string
     */
    public $name;

    /**
     * The type of the Channel - given by the parser
     *
     * @var string
     */
    public $type;

    /**
     * The subtype of the Channel - given by the parser
     *
     * @var string
     */
    public $subType;

    /**
     * Parameters required to get content
     * For example, parameters may be:
     *  array (
     *      "type" -> "email",
     *      "connectionString" -> "someConnectionString"
     *  );
     *
     * @var string[]
     */
    public $parameters = array();

    /**
     * The period in minutes that the Channel should be updated
     *
     * @return int
     */
    public $updatePeriod;

    /**
     * The time this Channel is next due to be run throught
     * the SiSPS
     *
     * @var time
     */
    public $nextrun;

    /**
     * The last time the Channel was run throught
     * the SiSPS - Note this time is not the last
     * sucess just the last run
     *
     * @var time
     */
    public $lastrun;

    /**
     * The last time this Channel was sucessfully run
     * though the SiSPS
     *
     * @var time
     */
    public $lastSuccess;

    /**
     * A boolean indicating if the Channel is
     * currently being processed
     *
     * @var bool
     */
    public $inprocess;

    /**
     * the number of sucessful time this Channel
     * have been run throught the SiSPS
     *
     * @var int
     */
    public $timesrun = 0;

    /**
     * If this job is currently active or not
     * @var bool
     */
    public $active = true;

    /**
     * Shows if the Channel has been deleted by the
     * user - NOTE: we never remove a Channel
     * @var bool
     */
    public $deleted = true;

    /**
     * Value indicating that sources from this channel
     * should recieve top veracity scores on creation
     * @var bool
     */
    public $trusted = false;
}
?>
