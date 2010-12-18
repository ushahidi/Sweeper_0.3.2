<?php
namespace Swiftriver\Core\Workflows\Analytics;
/**
 * @author mg[at]swiftly[dot]org
 */
class RunAnalyticsQuery extends AnalyticsWorkflowBase
{
    /**
     * Function that when called with appropriate json will
     * initiate an Analytics call that may return data that
     * can be used by the calling class.
     *
     * @param string $json
     * @param string $key
     * @return string
     */
    public function RunQuery($json, $key)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Workflows::Analaytics::RunAnalyticsQuery::RunQuery [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::Analaytics::RunAnalyticsQuery::RunQuery [START: Parsing input JSON]", \PEAR_LOG_DEBUG);

        try
        {
            $requestType = parent::ParseJSONToRequestType($json);

            $parameters = parent::ParseJSONToRequestParameters($json);

            $request = new \Swiftriver\Core\Analytics\AnalyticsRequest();

            $request->RequestType = $requestType;

            $request->Parameters = $parameters;
        }
        catch(\Exception $e)
        {
            $logger->log("Core::Workflows::Analaytics::RunAnalyticsQuery::RunQuery [An Exception was thrown]", \PEAR_LOG_ERR);

            $logger->log("Core::Workflows::Analaytics::RunAnalyticsQuery::RunQuery [$e]", \PEAR_LOG_ERR);

            $logger->log("Core::Workflows::Analaytics::RunAnalyticsQuery::RunQuery [Method finished]", \PEAR_LOG_INFO);

            return parent::FormatErrorMessage("An exception was thrown: " . $e->getMessage());
        }
        
        $logger->log("Core::Workflows::Analaytics::RunAnalyticsQuery::RunQuery [END: Parsing input JSON]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::Analaytics::RunAnalyticsQuery::RunQuery [START: Running Analytics Query]", \PEAR_LOG_DEBUG);
        
        try
        {
            $analyticsEngine = new \Swiftriver\Core\Analytics\AnalyticsEngine();

            $response = $analyticsEngine->RunAnalyticsRequest($request);

            $return = \json_encode($response->Result);
        }
        catch(\Exception $e)
        {
            $logger->log("Core::Workflows::Analaytics::RunAnalyticsQuery::RunQuery [An Exception was thrown]", \PEAR_LOG_ERR);

            $logger->log("Core::Workflows::Analaytics::RunAnalyticsQuery::RunQuery [$e]", \PEAR_LOG_ERR);

            $logger->log("Core::Workflows::Analaytics::RunAnalyticsQuery::RunQuery [Method finished]", \PEAR_LOG_INFO);

            return parent::FormatErrorMessage("An exception was thrown: " . $e->getMessage());
        }

        $logger->log("Core::Workflows::Analaytics::RunAnalyticsQuery::RunQuery [END: Running Analytics Query]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::Analaytics::RunAnalyticsQuery::RunQuery [Method finished]", \PEAR_LOG_INFO);

        return parent::FormatReturn($return);
    }
}
?>
