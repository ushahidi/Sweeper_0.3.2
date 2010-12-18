<?php
namespace Swiftriver\Core\Modules\DataContext\MySql_V1;
class RedBeanController {
    private static $toolbox;
    
    public static function Toolbox() {
        //if not the first time this is called
        if(isset(self::$toolbox))
            return self::$toolbox;

        //else set up the RedBean toolbox
        require_once( \Swiftriver\Core\Setup::Configuration()->ModulesDirectory."/RedBean/redbean.inc.php" );

        //get the url of the url of the db server
        $url = (string) Setup::$Configuration->DataBaseUrl;
        
        //Get the db username
        $username = (string) Setup::$Configuration->UserName;
        
        //get the password
        $password = (string) Setup::$Configuration->Password;
        
        //get the db name
        $database = (string) Setup::$Configuration->Database;
        
        //set the db type
        $typeofdatabase="mysql";
                
        //Assemble a database connection string (DSN)
        $dsn = "$typeofdatabase:host=$url;dbname=$database";

        //Construct a new Red Bean Toolbox, if it has not been set in the mean time
        if(!isset(self::$toolbox))
            self::$toolbox = \RedBean_Setup::kickstartDev($dsn, $username, $password);

        //return it
        return self::$toolbox;
    }

    /**
     *
     * @return \RedBean_OODB
     */
    public static function RedBean() {
        return self::Toolbox()->getRedBean();
    }

    public static function Associate($bean1, $bean2) {
        //Get a new association manager
        $association = new \RedBean_AssociationManager(self::Toolbox());

        //associate the bean
        $association->associate($bean1, $bean2);

	//Add acascaded delete constraint
	\RedBean_Plugin_Constraint::addConstraint($bean1, $bean2);
    }

    public static function GetRelatedBeans($bean, $type) {
        //Get a new association manager
        $association = new \RedBean_AssociationManager(self::Toolbox());

        //return the related beans of type $type
        return $association->related($bean, $type);
    }

    /**
     *
     * @return \RedBean_Plugin_Finder 
     */
    public static function Finder() {
        return new \RedBean_Plugin_Finder();
    }

    /**
     *
     * @return \RedBean_Adapter_DBAdapter
     */
    public static function DataBaseAdapter() {
        return self::Toolbox()->getDatabaseAdapter();
    }
}
?>
