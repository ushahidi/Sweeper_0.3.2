<?php
namespace Swiftriver\Core\ObjectModel;
/**
 * Class to hold Global Positioning data associated
 * with a content object
 *
 * @author mg[at]swiftly[dot]org
 */
class GisData
{
    /**
     * The longitude
     *
     * @var float
     */
    public $longitude;

    /**
     * The latitude
     *
     * @var float
     */
    public $latitude;

    /**
     * The name of this location
     * 
     * @var string
     */
    public $name;

    /**
     * Constructor for the GisData object
     * 
     * @param float $longitude
     * @param float $latitude
     * @param string $name
     */
    public function __construct($longitude, $latitude, $name = null)
    {
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->name = $name;
    }
}
?>
