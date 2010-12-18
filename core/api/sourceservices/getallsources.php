<?php
header('Content-type: application/json');
//Check for the existance of the unique Swift instance Key
if(!isset($_POST["key"])) {
    //If not found then return a JSON error
    echo '{"error":"The request to this service did not contain the required post data \'key\'"}';
    die();
}
//If all pre-checks are ok, attempt to run the API request
else {
    //include the setup file
    include_once(dirname(__FILE__)."/../../Setup.php");

    //create a new workflow instance
    $workflow = new Swiftriver\Core\Workflows\SourceServices\GetAllSources();

    //Check that the key supplied works with this core instance
    if(!$workflow->CheckKey($_POST["key"])) {
        //If not then return an error in JSON
        echo '{"error":"The key you supplied is not registered with this instance of the Swiftriver Core"}';
        die();
    }

    //If all the key is ok, then run the workflow
    $json = $workflow->RunWorkflow($_POST["key"]);

    //Return the JSON result
    echo $json;
    die();
}
?>