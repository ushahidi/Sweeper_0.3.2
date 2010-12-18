<?php
class FileRewriting implements IInstallStep
{
    private $passed = false;

    public function GetDescription()
    {
        return "Now I need to do some tidying up and make sure that all the URL ".
               "re-writing is setup so the Kohana framework, can work.";
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
                    "<p>Thats great, I managed to carry out all the final bits ".
                    "and pieces that I needed to do to finish off.</p>".
                    "<p>Lets move on to the final checks.</p>".
                   "</div>"
                 : "<div class='message'>".
                    "<p>Sorry, there seems to have been a few issues with the final ".
                    "tidy up. Basically, I tried to make changes to the following ".
                    "files and at least one of them failed. Can you check that I have ".
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
