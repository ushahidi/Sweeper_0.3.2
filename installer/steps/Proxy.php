<?php
class Proxy implements IInstallStep
{
    private $firsttime = false;
    private $proxy = "";
    private $username = "";
    private $password = "";
    private $errors = array();

    public function GetName()
    {
        return "Proxy Server Setup";
    }

    public function GetDescription()
    {
        return "Do you connect to the internet through a proxy server? If so ".
               "set it up here.";
    }

    public function RunChecks($postVar)
    {
        $this->firsttime = \count($postVar) < 1;

        if($this->firsttime)
            return null;

        if(!key_exists("proxy", $postVar) || strlen($postVar["proxy"]) == 0)
            $this->errors[] = "You haven't written the proxy address down.";

        if(!empty($postVar["username"]) || !empty($postVar["password"]))
        {
            if(empty($postVar["username"]))
                $this->errors[] = "You have written a password without a username.";

            if(empty($postVar["password"]))
                $this->errors[] = "You have written a username without a password.";
        }

        if(count($this->errors) > 0)
            return false;

        $this->proxy = $postVar["proxy"];
        $this->username = empty($postVar["username"]) ? null : $postVar["username"];
        $this->password = empty($postVar["password"]) ? null : $postVar["password"];

        try
        {
            include_once(dirname(__FILE__)."/../../core/Configuration/ConfigurationHandlers/BaseConfigurationHandler.php");
            include_once(dirname(__FILE__)."/../../core/Configuration/ConfigurationHandlers/CoreConfigurationHandler.php");
            $configFile = dirname(__FILE__)."/../../core/Configuration/ConfigurationFiles/CoreConfiguration.xml";
            $config = new Swiftriver\Core\Configuration\ConfigurationHandlers\CoreConfigurationHandler($configFile);
            
            $config->ProxyServer = $this->proxy;
            
            if($this->username != null && $this->password != null)
            {
                $config->ProxyServerUserName = $this->username;
                $config->ProxyServerPassword = $this->password;
            }

            $config->Save();
        }
        catch(\Exception $e)
        {
            $this->errors[] = "There was an issue saving the details to the configuration file. ".
                              "Please ensure you have completed the permissions step of ".
                              "the installer. In case you're interested, the error was:" . $e;
            return false;
        }

        return true;

    }

    public function Render()
    {
        if(!$this->firsttime && count($this->errors) == 0)
        {
            return  "<div class='message'>" .
                        "<p>That's great, all set.</p>".
                        "<p>Let's move on to the next step.</p>".
                    "</div>";
        }

        $return = "";

        if(!$this->firsttime && count($this->errors) > 0)
        {
            $return .= "<div class='alert'><ul>";
            foreach($this->errors as $error)
                $return .= "<li>" . $error . "</li>";
            $return .= "</ul></div>";
        }

        $position = (int) ($_GET["position"] != null ? $_GET["position"] : "0");
        $position++;

        $return .=
            "<form class='database' action='" . $_SERVER["PHP_SELF"] . "?" . $_SERVER["QUERY_STRING"] . "' method='post'>".
                "<div class='form-action skip'>" .
                    "<label>Dont need a Proxy or understand whats on this page? <br />Click this button to skip this step:</label>".
                    "<a href='?position=". $position . "'><img src='assets/images/button-nextstep.png' /></a>".
                "</div>".
                "<div class='form-row'>" .
                    "<label>Otherwise, continue to complete the boxes below. <br/><br/>Please enter the address of the proxy here (including the http:// or tcp:// followed by the address or IP):</label>".
                    "<input type='text' name='proxy' value='" . $_POST["proxy"] . "' />".
                "</div>".
                "<div class='form-row'>" .
                    "<label>Does your proxy require a username? if so add it here, if not then leave blank:</label>".
                    "<input type='text' name='username' value='" . $_POST["username"] . "' />".
                "</div>".
                "<div class='form-row'>" .
                    "<label>Does your proxy require a password? if so add it here, if not then leave blank:</label>".
                    "<input type='text' name='password' value='" . $_POST["password"] . "' />".
                "</div>".
                "<div class='form-action'>" .
                    "<input type='submit' value='Go and set these values!' />".
                "</div>".
            "</form>";

        return $return;
    }
}
?>
