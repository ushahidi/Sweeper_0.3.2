<?php
namespace Swiftriver\Core\ObjectModel\AnalyticsDataSets;
/**
 * @author mg[at]swiftly[dot]org
 */
class SourceActivityOverTimeDataSet
{
    /**
     * @var SourceActivityOverTimeSourceInstance[]
     */
    public $Sources;
}

class SourceActivityOverTimeSourceInstance
{
    /**
     * @var \Swiftriver\Core\ObjectModel\Source
     */
    public $Source;

    /**
     * @var SourceActivityOverTimeRow
     */
    public $Data;
}

class SourceActivityOverTimeRow
{
    public $StartTime;

    public $EndTime;

    public $NewInstances;
}
?>
