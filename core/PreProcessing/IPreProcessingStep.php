<?php
namespace Swiftriver\Core\PreProcessing;
interface IPreProcessingStep {
    /**
     * Interface method that all PrePorcessing Steps must implement
     * 
     * @param \Swiftriver\Core\ObjectModel\Content[] $contentItems
     * @param \Swiftriver\Core\Configuration\ConfigurationHandlers\CoreConfigurationHandler $configuration
     * @param \Log $logger
     * @return \Swiftriver\Core\ObjectModel\Content[]
     */
    public function Process($contentItems, $configuration, $logger);

    /**
     * The short name for this pre processing step, should be no longer
     * than 50 chars
     *
     * @return string
     */
    public function Name();

    /**
     * The description of this step
     *
     * @return string
     */
    public function Description();

    /**
     * This method returns an array of the required paramters that
     * are nessesary to run this step.
     *
     * @return \Swiftriver\Core\ObjectModel\ConfigurationElement[]
     */
    public function ReturnRequiredParameters();
}
?>
