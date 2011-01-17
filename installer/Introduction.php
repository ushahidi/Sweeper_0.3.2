<?php
class Introduction implements IInstallStep
{
    public function GetDescription()
    {
        return "Welcome to the installer for SwiftRiver:Sweeper.".
               "If you haven't already, check out the installation guide".
               "on wiki.ushahidi.com.<br/> ".
               "<a href='?position=1'><img src='assets/images/button-letsgetstarted.png' /></a>";
    }

    public function GetName()
    {
        return "Introduction";
    }

    public function RunChecks($postVar)
    {
        return null;
    }

    public function Render()
    {

    }
}
?>
