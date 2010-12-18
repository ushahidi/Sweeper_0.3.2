<?php
namespace Swiftriver\Core\DAL\DataContextInterfaces;
interface ISourceDataContext {
    /**
     * Given the IDs of Sources, this method
     * gets them from the underlying data store
     *
     * @param string[] $ids
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public static function GetSourcesById($ids);

    /**
     * Lists all the current Source in the core
     * @return \Swiftriver\Core\ObjectModel\Source[]
     */
    public static function ListAllSources();
}
?>
