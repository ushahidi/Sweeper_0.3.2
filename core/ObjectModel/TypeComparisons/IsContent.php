<?php
namespace Swiftriver\Core\ObjectModel\TypeComparisons;
/**
 * Implemetation of comparison functionality for the 
 * Content object
 */
class IsContent
{
    /**
     * Given an object, this method will attempt to assertain
     * if it is of type \Swiftriver\Core\ObjectModel\Content
     * 
     * @param object $object
     * @return bool
     */
    public static function CheckType($object)
    {
        $content = new \Swiftriver\Core\ObjectModel\Content();
        foreach($content as $key => $value)
            if(!property_exists($object, $key))
                return false;
        return true;
    }
}
?>
