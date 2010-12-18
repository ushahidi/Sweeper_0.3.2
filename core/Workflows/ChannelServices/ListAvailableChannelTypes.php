<?php
namespace Swiftriver\Core\Workflows\ChannelServices;
/**
 * @author mg[at]swiftly[dot]org
 */
class ListAvailableChannelTypes extends ChannelServicesBase
{
    /**
     * List all the Available types of Channels that can be configured in
     * the core
     *
     * @param string $key
     * @return string $json
     */
    public function RunWorkflow($key)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableChannelTypes::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);
        
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableChannelTypes::RunWorkflow [START: Constructing SiSPS]", \PEAR_LOG_DEBUG);
        
        $service = new \Swiftriver\Core\Modules\SiSPS\SwiftriverSourceParsingService();
        
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableChannelTypes::RunWorkflow [END: Constructing SiSPS]", \PEAR_LOG_DEBUG);
        
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableChannelTypes::RunWorkflow [START: Getting the list of available parsers]", \PEAR_LOG_DEBUG);
        
        $parsers = $service->ListAvailableParsers();
        
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableChannelTypes::RunWorkflow [END: Getting the list of available parsers]", \PEAR_LOG_DEBUG);
        
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableChannelTypes::RunWorkflow [START: Parsing to return JSON]", \PEAR_LOG_DEBUG);
        
        $json = parent::ParseParsersToJSON($parsers);
        
        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableChannelTypes::RunWorkflow [END: Parsing to return JSON]", \PEAR_LOG_DEBUG);

        $logger->log("Core::ServiceAPI::ChannelProcessingJobs::ListAvailableChannelTypes::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        return parent::FormatReturn($json);
    }
}
?>
