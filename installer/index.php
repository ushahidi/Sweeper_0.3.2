<?php
//Turn off the error display
ini_set("display_errors", 0);

//get this directory
$thisDirectory = dirname(__FILE__);

//Include the IInstallStep interface
include_once($thisDirectory."/steps/IInstallStep.php");

//Include the introduction step
include_once($thisDirectory."/Introduction.php");

//Loop through the steps directory including all the steps
foreach(new DirectoryIterator($thisDirectory . "/steps") as $fileInfo)
    if(strpos($fileInfo->getFilename(), ".php") !== false)
        include_once($fileInfo->getPathname());

//steps array with the steps to run in it
$steps = array(
    new Introduction(),
    new Environment(),
    new ReadWriteAccess(),
    new DBSetup(),
    new UserCreation(),
    new FileRewriting(),
    new AllowOverrideChecks()
);

//get the current step postion
$position = (int) ($_GET["position"] != null ? $_GET["position"] : "0");

//get the current step
$step = $steps[$position];

//Run all the checks and see if they suceed
$sucess = $step->RunChecks($_POST);
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>SwiftRiver Installer</title>
        <link rel="stylesheet" media="screen" href="assets/styles/master.css" />
    </head>
    <body>
        <div id="head">
            <table id="steps">
                <tr>
                    <?php for($i = 0; $i < count($steps); $i++) : ?>
                        <td class="<?php echo($position == $i ? 'selected' : ''); ?>">
                            <a href="?position=<?php echo($i); ?>">
                                <?php echo(($i+1) . ": " . $steps[$i]->GetName()); ?>
                            </a>
                        </td>
                    <?php endfor; ?>
                </tr>
            </table>
        </div>
        <div id="page">
            <img id="logo-callout" src="assets/images/logo-callout.png" />
            <div id="baloon">
                <div class="top">&nbsp;</div>
                <div class="mid">
                    <h2>Step: <?php echo($step->GetName()); ?></h2>
                    <p class="description"><?php echo($step->GetDescription()); ?></p>
                    <?php if($sucess === true) : ?>
                        <img src="assets/images/sucess-large.png" />
                    <?php elseif ($sucess === false) : ?>
                        <img src="assets/images/fail-large.png" />
                    <?php endif; ?>
                    <?php echo($step->Render()); ?>
                    <?php if($sucess) : ?>
                        <a href="<?php echo(($position + 1 != count($steps)) ? "?position=".($position + 1) : "../index.php"); ?>">
                            <img src="assets/images/button-nextstep.png" />
                        </a>
                    <?php endif; ?>
                </div>
                <div class="bottom">&nbsp;</div>
            </div>
        </div>
    </body>
</html>