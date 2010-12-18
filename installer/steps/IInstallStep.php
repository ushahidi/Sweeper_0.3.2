<?php
interface IInstallStep
{
    public function GetName();

    public function GetDescription();

    public function RunChecks($postVar);

    public function Render();
}
?>
