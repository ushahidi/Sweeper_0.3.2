<?php
namespace Swiftriver\Core\Workflows\Analytics;
/**
 * @author mg[at]swiftly[dot]org
 */
class AnalyticsWorkflowBase extends \Swiftriver\Core\Workflows\WorkflowBase
{
    public function ParseJSONToRequestType($json)
    {
        if(!isset($json) || $json == null)
            throw new \InvalidArgumentException ("The json supplied was null");

        $object = \json_decode($json);

        if($object == null)
            throw new \InvalidArgumentException ("The json did not decode correctly");

        if(!\property_exists($object, "RequestType"))
            throw new \InvalidArgumentException ("The json did not contain the required property 'RequestType'");

        $requestType = $object->RequestType;

        if($requestType == null || !\is_string($requestType))
            throw new \InvalidArgumentException ("The RequestType was not a valid string");

        return $requestType;
    }

    public function ParseJSONToRequestParameters($json)
    {
        if(!isset($json) || $json == null)
            throw new \InvalidArgumentException ("The json supplied was null");

        $array = \json_decode($json, true);

        if($array == null)
            throw new \InvalidArgumentException ("The json did not decode correctly");

        return (\key_exists("Parameters", $array) && \is_array($array["Parameters"]))
            ? $array["Parameters"]
            : array();
    }
}
?>
