<?php
namespace Swiftriver\Core\ObjectModel;
/**
 * Class to hold a collection of DuplicationIdentificationField
 * objects.
 * 
 * @author mg[at]swiftly[dot]org
 */
class DuplicationIdentificationFieldCollection
{
    /**
     * The collection of DIFs
     * @var DuplicationIdentificationField[]
     */
    public $difs = array();

    /**
     * the name of this collection
     * @var string
     */
    public $name;

    /**
     * Builds a new DIFC with a collection of DIFs
     * @param DuplicationIdentificationField[] $difs
     */
    public function __construct($name, $difs)
    {
        $this->name = $name;
        $this->difs = $difs;
    }
}
?>
