<?php
namespace Swiftriver\Core\Modules\DataContext\MySql_V1;
class Setup {
    /**
     * @var MySQLAPIKeyDataContextConfigurationHandler
     */
    public static $Configuration;

    public function __construct() {
        //TODO: Logging

        self::$Configuration = new DataContextConfigurationHandler(dirname(__FILE__)."/Configuration.xml");
	/*
        $url = (string)Setup::$Configuration->DataBaseUrl;
        $username = (string)Setup::$Configuration->UserName;
        $password = (string)Setup::$Configuration->Password;
        //Open a connection to the DB server
        $mysql = mysql_connect($url, $username, $password);

        //Select the databse
        $database = (string)Setup::$Configuration->Database;
        $bool = mysql_select_db($database, $mysql);
        $error = mysql_error($mysql);
        
        //Create the API keys table
        $query = "CREATE TABLE IF NOT EXISTS coreapikeys (apikey VARCHAR( 50 ) NOT NULL) CHARACTER SET utf8 COLLATE utf8_unicode_ci ;";
        $bool = mysql_query($query, $mysql);
        $error = mysql_error($mysql);

        
        //Create the channelprocessingjobs table
        $query = "CREATE TABLE IF NOT EXISTS channelprocessingjobs (
                    id LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
                    type VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
                    parameters LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,
                    updateperiod INT NOT NULL ,
                    nextrun DATETIME NOT NULL,
                    lastrun DATETIME NULL ,
                    lastSuccess DATETIME NULL ,
                    timesrun INT NOT NULL,
                    active TINYINT NOT NULL
                    ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ";
        $bool = mysql_query($query, $mysql);
        $error = mysql_error($mysql);
        mysql_close($mysql);
         * 
         */

        //initiate the redbean framework
        RedBeanController::RedBean();
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
