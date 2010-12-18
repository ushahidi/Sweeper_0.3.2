<?php
namespace Swiftriver\Core\Modules\DataContext\MySql_V2;
class Setup {
    /**
     * @var DataContextConfigurationHandler
     */
    public static $Configuration;

    public function __construct() {
        //TODO: Logging

        self::$Configuration = new DataContextConfigurationHandler(dirname(__FILE__)."/Configuration.xml");
    }
}



//include the rest of this DAL
$dirItterator = new \RecursiveDirectoryIterator(dirname(__FILE__));
$iterator = new \RecursiveIteratorIterator($dirItterator, \RecursiveIteratorIterator::SELF_FIRST);
foreach($iterator as $file) {
    if($file->isFile()) {
        $filePath = $file->getPathname();
        if(strpos($filePath, ".php")) {
            include_once($filePath);
        }
    }
}


$setup = new Setup();
?>
