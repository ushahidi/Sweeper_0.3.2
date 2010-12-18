<?php
class Introduction implements IInstallStep
{
    public function GetDescription()
    {
        return "Hi and welcome to my installer. I'm your Swiftriver instance ".
               "and over the next few clicks we'll set up the things I need to ".
               "get working. <br/> If you haven't already, why don't you check out ".
               "the install guide on the swiftly.org website...<br/> ".
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
