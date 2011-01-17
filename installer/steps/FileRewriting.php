<?php
class FileRewriting implements IInstallStep
{
    private $passed = false;

    public function GetDescription()
    {
        return "Now we need to do some tidying up to make sure that all the URL ".
		               "re-writing is setup so the Kohana framework can work.";
    }
    public function GetName() 
    {
        return "Tidy up";
    }
    public function RunChecks($postVar) 
    {
        try
        {
            $rewrite = substr($_SERVER["REQUEST_URI"],0,stripos($_SERVER["REQUEST_URI"],'/installer/'));

            $filename = dirname(__FILE__)."/../../web/.htaccess";

            if(!is_writable($filename))
                throw new Exception("The file $filename is not writable");

            /*
             * This operating system command is required on Windows only
             * systems as when you get the code the .htaccess file is
             * hidden by default which means we can't rewrite it.
             * mg[at]swiftly.org - 20110112
             */
            if(strpos(" " . getenv("OS"), "Windows") != 0)
                $return = exec("attrib -h $filename");

            $htaccessFile = file($filename);
            $handle = fopen($filename, "w");
            foreach($htaccessFile as $lineNumber => $line)
            {
                if(strpos(" ".$line, "RewriteBase") != 0)
                {
                    $lineToWrite = ($rewrite == "")
                        ? "RewriteBase /web/ \n"
                        : "RewriteBase $rewrite/web/ \n";
                    fwrite($handle, $lineToWrite);
                } 
                else
                {
                    fwrite($handle, $line);
                }
            }

            $filename = dirname(__FILE__)."/../../web/application/bootstrap.php";
            
            if(!is_writable($filename))
                throw new Exception("The file $filename is not writable");
            
            $bootstrapFile = file($filename);
            $handle = fopen(dirname(__FILE__)."/../../web/application/bootstrap.php", "w");
            foreach($bootstrapFile as $lineNumber => $line)
            {
                if(strpos(" " . $line, "'base_url'") != 0)
                {
                    $lineToWrite = ($rewrite == "")
                        ? "'base_url' => '/web/', \n"
                        : "'base_url' => '" . $rewrite . "/web/', \n";
                    fwrite($handle, $lineToWrite);
                } 
                else
                {
                    fwrite($handle, $line);
                }
            }

            $filename = dirname(__FILE__)."/../../index.php";

            if(!is_writable($filename))
                throw new Exception("The file $filename is not writable");

            $indexFile = file($filename);
            $handle = fopen(dirname(__FILE__)."/../../index.php", "w");
            foreach($indexFile as $lineNumber => $line)
            {
                if(strpos(" " . $line, "header") != 0)
                {
                    $lineToWrite = ($rewrite == "")
                        ? "header(\"Location: /web/\"); \n"
                        : "header(\"Location: $rewrite/web/\"); \n";
                    fwrite($handle, $lineToWrite);
                } 
                else
                {
                    fwrite($handle, $line);
                }
            }

            $this->passed = true;
            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }

    }
    public function Render() 
    {

        return ($this->passed)
		                 ? "<div class='message'>".
		                    "<p>Great! We managed to finish up those tasks.".
		                    "<p>When you click the link below, you'll be taken to the ".
		                    "Sweeper app where you can start curating content!</p>".
		                   "</div>"
		                 : "<div class='message'>".
		                    "<p>Sorry, there seems to have been a few issues with the final ".
		                    "steps. We tried to make changes to the following ".
		                    "files and at least one of them failed. Please ensure that we have ".
		                    "permissions to edit them:</p>".
		                    "<ul>".
		                        "<li>[root]/web/.htaccess</li>".
		                        "<li>[root]/web/application/bootstrap.php</li>".
		                        "<li>[root]/index.php</li>".
		                    "</ul>".
		                   "</div>";
    }
}
?>
