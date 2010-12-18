<?php
class ReadWriteAccess implements IInstallStep
{
    private $checks = array();

    public function GetName()
    {
        return "Permissions";
    }

    public function GetDescription() 
    {
        return "In this step I have checked that I have the correct ".
               "permissions to alter the folders and files that I need ".
               "to during my installation.";
    }

    public function RunChecks($postVar)
    {
        $htaccessCheck->name = ".htaccess Check";
        $htaccessCheck->result = is_writeable(dirname(__FILE__)."/../../web/.htaccess");
        $htaccessCheck->text = $htaccessCheck->result
                ? "Your .htaccess file is there and writable."
                : "I could not find or write to the .htaccess file in the [root]/web/ ".
                  "directory.";
        $this->checks[] = $htaccessCheck;

        $bootstrapCheck->name = "bootstrap.php Check";
        $bootstrapCheck->result = is_writeable(dirname(__FILE__)."/../../web/application/bootstrap.php");
        $bootstrapCheck->text = $bootstrapCheck->result
                ? "Your bootstrap.php file is there and writable."
                : "I could not find or write to the bootstrap.php file in the ".
                  "[root]/web/application/ directory";
        $this->checks[] = $bootstrapCheck;

        $indexCheck->name = "index.php Check";
        $indexCheck->result = is_writeable(dirname(__FILE__)."/../../index.php");
        $indexCheck->text = $indexCheck->result
                ? "Your index.php file is there and writable."
                : "I could not find or write to the index.php file in the ".
                  "[root] directory";
        $this->checks[] = $indexCheck;

        $directoriesCheck->name = "Directories Check";
        $directoriesCheck->result = is_writable(dirname(__FILE__)."/../../core/Configuration/ConfigurationFiles") &&
                                    is_writable(dirname(__FILE__)."/../../core/Modules") &&
                                    is_writable(dirname(__FILE__)."/../../core/Cache") &&
                                    is_writable(dirname(__FILE__)."/../../web/application/cache") &&
                                    is_writable(dirname(__FILE__)."/../../web/modules");
        $directoriesCheck->text = $directoriesCheck->result
                ? "All the directories I need to write to are ok!"
                : "Thats a shame, one of the following directories is not writable: <br/>".
                  "[root]/core/Configuration/ConfigurationFiles<br />".
                  "[root]/core/Modules<br />".
                  "[root]/core/Cache<br />".
                  "[root]/web/application/cache".
                  "[root]/web/modules";
        $this->checks[] = $directoriesCheck;

        //Check that all the steps passed and if not then return false
        foreach($this->checks as $check)
            if(!$check->result)
                return false;

        //If all the steps passed then return true
        return true;
    }

    public function Render()
    {
        $return = "";
        $return .= "<div class='step-render'><ul>";

        foreach($this->checks as $check)
        {
            $return .= "<li class='" . ($check->result ? "pass" : "fail") . "'>";
            $return .= "<p class='name'>" . $check->name . "</p>";
            $return .= "<p class='result'>" . $check->text . "</p>";
            $return .= "</li>";
        }

        $return .= "</div>";
        return $return;
    }
}
?>
