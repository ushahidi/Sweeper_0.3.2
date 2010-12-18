<?php
namespace Swiftriver\Core;
/**
 * @author mg[at]swiftly[dot]org
 */
class Setup
{
    /**
     * Static variable for the core configuration handler
     *
     * @var Configuration\ConfigurationHandlers\CoreConfigurationHandler
     */
    private static $configuration;
    
    /**
     * Static variable for the DAL Config handler
     * 
     * @var Configuration\ConfigurationHandlers\DALConfigurationHandler 
     */
    private static $dalConfiguration;

    /**
     * Static variable for the Pre Processing Config handler
     *
     * @var Configuration\ConfigurationHandlers\PreProcessingStepsConfigurationHandler
     */
    private static $preProcessingStepsConfiguration;

    /**
     * Static variable for the Event Distribution Config handler
     *
     * @var Configuration\ConfigurationHandlers\EventDistributionConfigurationHandler
     */
    private static $eventDistributionConfiguration;

    /**
     * Static variable for the Dynamic module Config handler
     *
     * @var Configuration\ConfigurationHandlers\DynamicModuleConfigurationHandler
     */
    private static $dynamicModuleConfiguration;

    /**
     * Get the shared instance for the logger
     * @return \Log
     */
    public static function GetLogger()
    {
        $log = new \Log("this message is ignored, however not supplying one throws an error :o/");

        $logger = $log->singleton('file', Setup::Configuration()->CachingDirectory."/log.log" , '   ');
        
        if(!self::Configuration()->EnableDebugLogging)
        {
            $mask = \Log::UPTO(\PEAR_LOG_INFO);
            $logger->setMask($mask);
        }

        return $logger;
    }

    /**
     * Static access to the Core Config handler
     *
     * @return Configuration\ConfigurationHandlers\CoreConfigurationHandler
     */
    public static function Configuration()
    {
        if(isset(self::$configuration))
            return self::$configuration;

        self::$configuration = new Configuration\ConfigurationHandlers\CoreConfigurationHandler(dirname(__FILE__)."/Configuration/ConfigurationFiles/CoreConfiguration.xml");

        return self::$configuration;
    }

    /**
     * Static access to the DAL Config handler
     *
     * @return Configuration\ConfigurationHandlers\DALConfigurationHandler
     */
    public static function DALConfiguration()
    {
        if(isset(self::$dalConfiguration))
            return self::$dalConfiguration;

        self::$dalConfiguration = new Configuration\ConfigurationHandlers\DALConfigurationHandler(dirname(__FILE__)."/Configuration/ConfigurationFiles/DALConfiguration.xml");

        return self::$dalConfiguration;
    }

    /**
     * Static access to the Pre Processing Steps config handler
     *
     * @return Configuration\ConfigurationHandlers\PreProcessingStepsConfigurationHandler
     */
    public static function PreProcessingStepsConfiguration()
    {
        if(isset(self::$preProcessingStepsConfiguration))
            return self::$preProcessingStepsConfiguration;

        self::$preProcessingStepsConfiguration = new Configuration\ConfigurationHandlers\PreProcessingStepsConfigurationHandler(dirname(__FILE__)."/Configuration/ConfigurationFiles/PreProcessingStepsConfiguration.xml");

        return self::$preProcessingStepsConfiguration;
    }

    /**
     * Static access to the Event distribution config handler
     *
     * @return Configuration\ConfigurationHandlers\EventDistributionConfigurationHandler
     */
    public static function EventDistributionConfiguration()
    {
        if(isset(self::$eventDistributionConfiguration))
            return self::$eventDistributionConfiguration;

        self::$eventDistributionConfiguration = new Configuration\ConfigurationHandlers\EventDistributionConfigurationHandler(dirname(__FILE__)."/Configuration/ConfigurationFiles/EventDistributionConfiguration.xml");

        return self::$eventDistributionConfiguration;
    }

    /**
     * Static access to the dynamic module config handler
     *
     * @return Configuration\ConfigurationHandlers\DynamicModuleConfigurationHandler
     */
    public static function DynamicModuleConfiguration()
    {
        if(isset(self::$dynamicModuleConfiguration))
            return self::$dynamicModuleConfiguration;

        self::$dynamicModuleConfiguration = new Configuration\ConfigurationHandlers\DynamicModuleConfigurationHandler(dirname(__FILE__)."/Configuration/ConfigurationFiles/DynamicModuleConfiguration.xml");

        return self::$dynamicModuleConfiguration;
    }
}

//include the Loging Framework
include_once("Log.php");

//Include the config framework
include_once(dirname(__FILE__)."/Configuration/ConfigurationHandlers/BaseConfigurationHandler.php");
$dirItterator = new \RecursiveDirectoryIterator(dirname(__FILE__)."/Configuration/ConfigurationHandlers/");
$iterator = new \RecursiveIteratorIterator($dirItterator, \RecursiveIteratorIterator::SELF_FIRST);
foreach($iterator as $file) {
    if($file->isFile()) {
        $filePath = $file->getPathname();
        if(strpos($filePath, ".php")) {
            include_once($filePath);
        }
    }
}


//Include some specific files
include_once(dirname(__FILE__)."/Workflows/WorkflowBase.php");
include_once(dirname(__FILE__)."/Workflows/ContentServices/ContentServicesBase.php");
include_once(dirname(__FILE__)."/Workflows/EventHandlers/EventHandlersBase.php");
include_once(dirname(__FILE__)."/Workflows/ChannelServices/ChannelServicesBase.php");
include_once(dirname(__FILE__)."/Workflows/SourceServices/SourceServicesBase.php");
include_once(dirname(__FILE__)."/Workflows/PreProcessingSteps/PreProcessingStepsBase.php");
include_once(dirname(__FILE__)."/Workflows/Analytics/AnalyticsWorkflowBase.php");
include_once(Setup::Configuration()->ModulesDirectory."/SiSPS/Parsers/IParser.php");

//include everything else
$directories = array(
    dirname(__FILE__)."/Analytics/",
    dirname(__FILE__)."/ObjectModel/",
    dirname(__FILE__)."/DAL/",
    dirname(__FILE__)."/StateTransition/",
    dirname(__FILE__)."/PreProcessing/",
    dirname(__FILE__)."/Workflows/",
    dirname(__FILE__)."/EventDistribution/",
    Setup::Configuration()->ModulesDirectory."/SiSW/",
    Setup::Configuration()->ModulesDirectory."/SiSPS/",
);
foreach($directories as $dir) {
    $dirItterator = new \RecursiveDirectoryIterator($dir);
    $iterator = new \RecursiveIteratorIterator($dirItterator, \RecursiveIteratorIterator::SELF_FIRST);
    foreach($iterator as $file) {
        if($file->isFile()) {
            $filePath = $file->getPathname();
            if(strpos($filePath, ".php")) {
                include_once($filePath);
            }
        }
    }
}

//Include the DAL Data Context Setup file
$relativeDir = Setup::DALConfiguration()->DataContextDirectory;
if(isset($relativeDir) && $relativeDir != "") {
    $directory = Setup::Configuration()->ModulesDirectory.$relativeDir;
    if(file_exists($directory)) {
        //include the setup file - if there is one
        $setupfile = $directory."/Setup.php";
        if(file_exists($setupfile)) {
            include_once($setupfile);
        }
    }
}
?>
