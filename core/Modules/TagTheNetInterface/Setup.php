<?php
namespace Swiftriver\TagTheNetInterface;
include_once(dirname(__FILE__)."/ContentFromJSONParser.php");
include_once(dirname(__FILE__)."/ServiceInterface.php");
include_once(dirname(__FILE__)."/TextForUrlParser.php");
class Setup {
    public static function Configuration() {
        return array (
            "ServiceUri" => "",
            "SwiftriverCoreDirectory" => dirname(__FILE__)."/../../",
            "SwiftriverModulesDirectory" => dirname(__FILE__)."/../../Modules",
        );
    }
}
?>
