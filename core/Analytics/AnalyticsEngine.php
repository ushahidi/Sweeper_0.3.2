<?php
namespace Swiftriver\Core\Analytics;
/**
 * @author mg[at]swiftly[dot]com
 */
class AnalyticsEngine
{
    /**
     * Array containing all the implementing class of the
     * IAnalyticsProvider interface that can be harvested
     * from the modules directory and that are sutable for
     * the DataContent used in this instance.
     *
     * @var IAnalyticsProvider[]
     */
    private $analyticsProviders;

    /**
     * Constructor for the Analytics engine that can take
     * an optional modules directory.
     *
     * @param string $modulesDirectory
     */
    public function __construct($modulesDirectory = null)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Analytics::AnalyticsEngine::__construct [Method invoked]", \PEAR_LOG_DEBUG);

        if($modulesDirectory == null)
            $modulesDirectory = \Swiftriver\Core\Setup::Configuration()->ModulesDirectory;

        $modulesDirectory = \rtrim($modulesDirectory, "/") . "/";

        $dataContextType = \Swiftriver\Core\Setup::DALConfiguration()->DataContextType;

        $this->analyticsProviders = array();

        $logger->log("Core::Analytics::AnalyticsEngine::__construct [START: Looking for IAnalyticsProviders]", \PEAR_LOG_DEBUG);

        try
        {
            $handle = \opendir($modulesDirectory);

            while(false !== ($resource = \readdir($handle)))
            {
                if(\strpos($resource, "AnalyticsProvider") === false)
                    continue;

                $dirName = $modulesDirectory . $resource . "/";

                if(!\is_dir($dirName))
                    continue;

                $innerHandle = \opendir($dirName);

                $logger->log("Core::Analytics::AnalyticsEngine::__construct [START: Looking for IAnalyticsProviders in directory $dirName]", \PEAR_LOG_DEBUG);

                while(false !== ($innerResource = \readdir($innerHandle)))
                {
                    $fileName = $dirName . $innerResource;

                    if(!\is_file($fileName))
                        continue;

                    if(\strpos($innerResource, "AnalyticsProvider.php") === false)
                        continue;

                    try
                    {

                        $logger->log("Core::Analytics::AnalyticsEngine::__construct [START: Including the provider $fileName]", \PEAR_LOG_DEBUG);

                        include_once($fileName);

                        $logger->log("Core::Analytics::AnalyticsEngine::__construct [END: Including the provider $fileName]", \PEAR_LOG_DEBUG);

                        $className = \str_replace(".php", "", $innerResource);

                        $type = "\\Swiftriver\\AnalyticsProviders\\" . $className;

                        $logger->log("Core::Analytics::AnalyticsEngine::__construct [START: Instanciating the provider $type]", \PEAR_LOG_DEBUG);

                        $instance = new $type();

                        $logger->log("Core::Analytics::AnalyticsEngine::__construct [END: Instanciating the provider $type]", \PEAR_LOG_DEBUG);

                        if(!($instance instanceof IAnalyticsProvider))
                            continue;

                        if(!\in_array($dataContextType, $instance->DataContentSet()))
                            continue;

                        $logger->log("Core::Analytics::AnalyticsEngine::__construct [START: Adding the provider $type]", \PEAR_LOG_DEBUG);

                        $this->analyticsProviders[] = $instance;

                        $logger->log("Core::Analytics::AnalyticsEngine::__construct [END: Adding the provider $type]", \PEAR_LOG_DEBUG);
                    }
                    catch (\Exception $innerE)
                    {
                        $logger->log("Core::Analytics::AnalyticsEngine::__construct [An exception was thrown]", \PEAR_LOG_ERR);

                        $logger->log("Core::Analytics::AnalyticsEngine::__construct [$innerE]", \PEAR_LOG_ERR);
                    }
                }

                $logger->log("Core::Analytics::AnalyticsEngine::__construct [END: Looking for IAnalyticsProviders in directory $dirName]", \PEAR_LOG_DEBUG);
            }
        }
        catch(\Exception $e)
        {
            $logger->log("Core::Analytics::AnalyticsEngine::__construct [An exception was thrown]", \PEAR_LOG_ERR);

            $logger->log("Core::Analytics::AnalyticsEngine::__construct [$e]", \PEAR_LOG_ERR);
        }

        $logger->log("Core::Analytics::AnalyticsEngine::__construct [END: Looking for IAnalyticsProviders]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Analytics::AnalyticsEngine::__construct [Method finished]", \PEAR_LOG_DEBUG);
    }

    /**
     * Function that takes in an instance of the AnalyticsRequest object
     * and attempts to match this to an instance of the IAnalyticsProvider
     * interface.
     *
     * @param AnalyticsRequest $request
     * @return AnalyticsRequest
     */
    public function RunAnalyticsRequest($request)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::Analytics::AnalyticsEngine::__construct [Method invoked]", \PEAR_LOG_DEBUG);

        if($request == null)
        {
            $logger->log("Core::Analytics::AnalyticsEngine::__construct [The request parameter was null]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Analytics::AnalyticsEngine::__construct [Method finished]", \PEAR_LOG_DEBUG);

            return $request;
        }

        $dataContextType = \Swiftriver\Core\Setup::DALConfiguration()->DataContextType;

        $request->DataContextType = $dataContextType;

        $providerType = $request->RequestType;

        $logger->log("Core::Analytics::AnalyticsEngine::__construct [START: Looking for matching provider]", \PEAR_LOG_DEBUG);

        foreach($this->analyticsProviders as $analyticsProvider)
        {
            if($providerType != $analyticsProvider->ProviderType())
                continue;

            try
            {
                $logger->log("Core::Analytics::AnalyticsEngine::__construct [START: Running provider]", \PEAR_LOG_DEBUG);

                $return = $analyticsProvider->ProvideAnalytics($request);

                $logger->log("Core::Analytics::AnalyticsEngine::__construct [END: Running provider]", \PEAR_LOG_DEBUG);

                $logger->log("Core::Analytics::AnalyticsEngine::__construct [Method finished]", \PEAR_LOG_DEBUG);

                return $return;

            }
            catch(\Exception $e)
            {
                $logger->log("Core::Analytics::AnalyticsEngine::__construct [An exception was thrown]", \PEAR_LOG_ERR);

                $logger->log("Core::Analytics::AnalyticsEngine::__construct [$e]", \PEAR_LOG_ERR);

                $logger->log("Core::Analytics::AnalyticsEngine::__construct [Method finished]", \PEAR_LOG_DEBUG);

                return $request;
            }
        }

        $logger->log("Core::Analytics::AnalyticsEngine::__construct [END: Looking for matching provider - No Provider found for $providerType]", \PEAR_LOG_ERR);

        $logger->log("Core::Analytics::AnalyticsEngine::__construct [Method finished]", \PEAR_LOG_DEBUG);

        return $request;
    }
}
?>
