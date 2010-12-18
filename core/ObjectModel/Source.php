<?php
namespace Swiftriver\Core\ObjectModel;
/**
 * Source object
 * @author mg[at]swiftly[dot]org
 */
class Source
{
    /**
     * The genuine unique ID of this source
     * @var string
     */
    public $id;

    /**
     * The time() value when the source was
     * first created
     * @var int
     */
    public $date;

    /**
     * The trust score for this source
     * @var int
     */
    public $score;

    /**
     * The friendly name of this source
     * @var string
     */
    public $name;

    /**
     * The email address of the source
     * @var string
     */
    public $email;

    /**
     * The link to the source
     * @var string
     */
    public $link;

    /**
     * application ids
     * @var string[]
     */
    public $applicationIds = array();

    /**
     * application profile images
     * @var string[]
     */
    public $applicationProfileImages = array();

    /**
     * The ID of the parent channel object
     * @var string
     */
    public $parent;

    /**
     * The type of the source - given by the parser
     *
     * @var string
     */
    public $type;

    /**
     * The subtype of the source - given by the parser
     *
     * @var string
     */
    public $subType;

    /**
     * The array of location data
     *
     * @var GisData[]
     */
    public $gisData = array();
}
?>
