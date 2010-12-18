<?php
namespace Swiftriver\Core\Analytics;
/**
 * @author mg[at]swiftly[dot]org
 */
interface IAnalyticsProvider
{
    /**
     * Function that should return the name of the
     * given AnalyticsProvider.
     * 
     * @return string
     */
    public function ProviderType();

    /**
     * Function that when implemented by a derived
     * class should return an object that can be
     * json encoded and returned to the UI to
     * provide analytical information about the
     * underlying data.
     *
     * @param AnalyticsRequest $parameters
     * @return AnalyticsRequest 
     */
    public function ProvideAnalytics($request);

    /**
     * Function that returns an array containing the
     * fully qualified types of the data content's
     * that the deriving Analytics Provider can work
     * against
     *
     * @return string[]
     */
    public function DataContentSet();
}
?>
