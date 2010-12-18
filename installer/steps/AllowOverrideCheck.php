<?php
class AllowOverrideChecks implements IInstallStep
{
    private $passed = false;

    public function GetDescription()
    {
        return "Lets just check that the Kohana URL rewritter can work correctly.";
    }

    public function GetName()
    {
        return "Kohana Check";
    }

    public function RunChecks($postVar)
    {
        $pageURL = 'http';

        if ($_SERVER["HTTPS"] == "on")
            $pageURL .= "s";

        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80")
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        else
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

        $pos = strpos($pageURL, "/installer");
        $newUri = substr($pageURL, 0, $pos);
        $newUri .= "/web/config/loginandregister/login";
        $result = file_get_contents($newUri);

        $this->passed = ($result !== false);

        return $this->passed;
    }

    public function Render()
    {
        return ($this->passed)
                 ? "<div class='message'>".
                    "<p>Excellent, it looks like the Kohana framework is set up ok.</p>".
                    "<p>When you hit the link below, you should be taken to the ".
                    "sweeper app where you can start adding and curating content</p>".
                   "</div>"
                 : "<div class='message'>".
                    "<p>Thats a shame, it seems that there are a few issues with the ".
                    "way that Kohana wants to do its URLs. The best thing to do is ".
                    "check out <strong><a target='_blank' href='http://kerkness.ca/wiki/doku.php?id=removing_the_index.php'>".
                    "this</a></strong> link and pay attention to the ".
                    "<strong>Troubleshooting #2</strong> section. ".
                    "Its also worth noting that the Kohana framework runs best when run from ".
                    "a correctly configured virtual host in Apache. If this is all over ".
                    "your head, look <a target='_blank' href='http://www.devshed.com/c/a/Apache/Configuring-and-Using-Virtual-Hosts-in-Apache/'>".
                    "<strong>here</strong></a></p>".
                   "</div>";

    }
}
?>
