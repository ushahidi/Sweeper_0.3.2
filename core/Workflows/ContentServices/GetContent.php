<?php
namespace Swiftriver\Core\Workflows\ContentServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class GetContent extends ContentServicesBase
{
    /**
     * Given a JSON string describing the pagination and state
     * required, this method will return a set of content items
     *
     * @param string $json
     * @return string
     */
    public function RunWorkflow($json, $key)
    {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        $parameters = parent::ParseJSONToLooseParameters($json);

        if(!isset($parameters))
        {
            $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [ERROR: Method ParseJSONToPagedContentByStateParameters returned null]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [ERROR: Getting paged content by state]", \PEAR_LOG_INFO);
            parent::FormatErrorMessage("There was an error in the JSON supplied, please consult the API documentation and try again.");
        }

        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [START: Constructing Content repository]", \PEAR_LOG_DEBUG);

        $repository = new \Swiftriver\Core\DAL\Repositories\ContentRepository();

        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [END: Constructing Content repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [START: Querying repository]", \PEAR_LOG_DEBUG);

        $results = $repository->GetContentList($parameters);

        if(!isset($results) || !is_array($results) || !isset($results["totalCount"]) || !isset($results["contentItems"]) || !is_numeric($results["totalCount"]) || $results["totalCount"] < 1)
        {
            $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [No results were returned from the repository]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [END: Querying repository with supplied parameters]", \PEAR_LOG_DEBUG);
            $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return '{"totalcount":"0"}';
        }

        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [END: Querying repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [START: Parsing content to JSON]", \PEAR_LOG_DEBUG);

        $contentJson = parent::ParseContentToJSON($results["contentItems"]);

        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [END: Parsing content to JSON]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [START: Parsing navigation to JSON]", \PEAR_LOG_DEBUG);

        $navigationJson = (isset($results["navigation"]) && $results["navigation"] != null)
                            ? json_encode($results["navigation"])
                            : "[]";

        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [END: Parsing navigation to JSON]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [START: Constructing return JSON]", \PEAR_LOG_DEBUG);

        $returnJson = '{"totalcount":"'.$results["totalCount"].'","contentitems":'.$contentJson.',"navigation":'.$navigationJson.'}';

        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [END: Constructing return JSON]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ContentServices::GetContent::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        return $returnJson;
    }
}
?>
