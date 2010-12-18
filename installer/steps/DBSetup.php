<?php
class DBSetup implements IInstallStep {
    
    private $firstTime = false;
    private $host = null;
    private $username = null;
    private $password = null;
    private $database = null;
    private $errors = array();

    public function GetDescription() 
    {
        return "In this step I need to take a few details relating to how I am " .
               "going to talk to the database. Before you fill in the form here, " .
               "please make sure you have created an empty database for me on the ".
               "database server!";
    }
    
    public function GetName() 
    {
        return "Database";
    }
    
    public function RunChecks($postVar) 
    {
        $this->firstTime = (count($postVar) < 1);
        
        if($this->firstTime)
            return null;

        if(!key_exists("host", $postVar) || strlen($postVar["host"]) == 0)
            $this->errors[] = "You haven't written the host down.";

        if(!key_exists("username", $postVar) || strlen($postVar["username"]) == 0)
            $this->errors[] = "You haven't written the username down.";

        if(!key_exists("password", $postVar) || strlen($postVar["password"]) == 0)
            $this->errors[] = "You haven't written the password down.";

        if(!key_exists("database", $postVar) || strlen($postVar["database"]) == 0)
            $this->errors[] = "You haven't written the database name down.";

        if(count($this->errors) > 0)
            return false;

        $host = $postVar["host"];
        $username = $postVar["username"];
        $password = $postVar["password"];
        $database = $postVar["database"];

        try
        {
            include_once(dirname(__FILE__)."/../../core/Configuration/ConfigurationHandlers/BaseConfigurationHandler.php");
            include_once(dirname(__FILE__)."/../../core/Modules/DataContext/MySql_V2/DataContextConfigurationHandler.php");
            $configFile = dirname(__FILE__)."/../../core/Modules/DataContext/MySql_V2/Configuration.xml";
            $config = new \Swiftriver\Core\Modules\DataContext\MySql_V2\DataContextConfigurationHandler($configFile);
            $xml = $config->xml;
            $xml->properties->property[0]["value"] = $host;
            $xml->properties->property[1]["value"] = $username;
            $xml->properties->property[2]["value"] = $password;
            $xml->properties->property[3]["value"] = $database;
            $config->xml = $xml;
            $config->Save();
        }
        catch(\Exception $e)
        {
            $this->errors[] = "There was an issue saving the details to the configuration file ".
                              "I use. Please ensure you have completed the permissions step of ".
                              "this installer. If you're interested, the error was:" . $e;
            return false;
        }

        try
        {
            $filename = dirname(__FILE__)."/../../web/modules/riverid/classes/riveridconfig.php";
            $file = file($filename);
            $handle = fopen($filename, "w");
            foreach($file as $lineNumber => $line)
            {
                if(strpos($line, "\$databaseurl") != 0)
                    $lineToWrite = "    public static \$databaseurl = '$host';\n";
                elseif(strpos($line, "\$username") != 0)
                    $lineToWrite = "    public static \$username = '$username';\n";
                elseif(strpos($line, "\$password") != 0)
                    $lineToWrite = "    public static \$password = '$password';\n";
                elseif(strpos($line, "\$database") != 0)
                    $lineToWrite = "    public static \$database = '$database';\n";
                else
                    $lineToWrite = $line;

                fwrite($handle, $lineToWrite);
            }
            fclose($handle);

            $filename = dirname(__FILE__)."/../../web/modules/swiftrivertheming/classes/themingconfig.php";
            $file = file($filename);
            $handle = fopen($filename, "w");
            foreach($file as $lineNumber => $line)
            {
                if(strpos($line, "\$databaseurl") != 0)
                    $lineToWrite = "    public static \$databaseurl = '$host';\n";
                elseif(strpos($line, "\$username") != 0)
                    $lineToWrite = "    public static \$username = '$username';\n";
                elseif(strpos($line, "\$password") != 0)
                    $lineToWrite = "    public static \$password = '$password';\n";
                elseif(strpos($line, "\$database") != 0)
                    $lineToWrite = "    public static \$database = '$database';\n";
                else
                    $lineToWrite = $line;

                fwrite($handle, $lineToWrite);
            }
            fclose($handle);

        }
        catch (\Exception $e)
        {
            $this->errors[] = "There was an issue saving the details to the configuration file ".
                              "I use. Please ensure you have completed the permissions step of ".
                              "this installer. If your interested, the error was:" . $e;
            return false;
        }

        try
        {
            $link = mysql_connect($postVar["host"], $postVar["username"], $postVar["password"]);

            if(!$link)
            {
                $this->errors[] = "I tried to connect to the database using the 'mysql_connect' ".
                                  "function and the details you gave me but it didn't work.";
                return false;
            }

            $dbconnect = mysql_select_db($postVar["database"], $link);

            if(!$dbconnect)
            {
                $this->errors[] = "I connected to the database server ok but when I tried to ".
                                  "select the database you told me about, using 'mysql_select_db', ".
                                  "it didn't work.";
                return false;
            }

            $sqlFile = dirname(__FILE__)."/../../core/Modules/DataContext/MySql_V2/upgrade.sql";
            $sql = file_get_contents($sqlFile);
            $db = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            $db->exec($sql);
        }
        catch(\Exception $e)
        {
            $this->errors[] = "Something went wrong while I was trying to connect to the database. ".
                              "Here are the details: " . $e;
            return false;
        }

        return true;
    }

    public function Render()
    {
        if(!$this->firstTime && count($this->errors) == 0)
        {
            return "<div class='message'>" .
                    "<p>That's great, all that worked out with no problems</p>".
                    "<p>Let's move onto the next step.</p>".
                   "</div>";
        }


        $return = "";

        if(!$this->firstTime && count($this->errors) > 0)
        {
            $return .= "<div class='alert'><ul>";
            foreach($this->errors as $error)
                $return .= "<li>" . $error . "</li>";
            $return .= "</ul></div>";
        }

        $return .= "<form class='database' action='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "' method='post'>".
                    "<div class='form-row'>" .
                        "<label>Database server (normally this is localhost):</label>".
                        "<input type='text' name='host' value='" . $_POST["host"] . "' />".
                    "</div>".
                    "<div class='form-row'>" .
                        "<label>The username that I should use:</label>".
                        "<input type='text' name='username' value='" . $_POST["username"] . "' />".
                    "</div>".
                    "<div class='form-row'>" .
                        "<label>The password that I should use:</label>".
                        "<input type='text' name='password' value='" . $_POST["password"] . "' />".
                    "</div>".
                    "<div class='form-row'>" .
                        "<label>The name of the database that you created for me:</label>".
                        "<input type='text' name='database' value='" . $_POST["database"] . "' />".
                    "</div>".
                    "<div class='form-action'>" .
                        "<input type='submit' value='Go and set these values!' />".
                    "</div>".
                   "</form>";

        return $return;
    }
}
?>