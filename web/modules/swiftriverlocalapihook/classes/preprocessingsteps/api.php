<?php
class PreProcessingSteps_API {

    /**
     * The core API key
     * @var string Guid
     */
    private $apiKey;

    /**
     * Constructor method used to include the core setup file
     * before any of the api functions can be called
     */
    public function __construct($apiKey)
    {
        //Localise the api key
        $this->apiKey = $apiKey;

        //include the core one
        include_once(DOCROOT."../core/Setup.php");
    }

    public function list_all_preprocessing_steps()
    {
        //Instanciate the workflow
        $workflow = new \Swiftriver\Core\Workflows\PreProcessingSteps\ListAllPreProcessingSteps();

        //run the workflow
        $json = $workflow->RunWorkflow($this->apiKey);

        //return the json
        return $json;
    }

    public function activate_preprocessing_step($json_encoded_parameters)
    {
        //Instanciate the workflow
        $workflow = new Swiftriver\Core\Workflows\PreProcessingSteps\ActivatePreProcessingStep();

        //run the workflow
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);

        //return the json
        return $json;
    }

    public function deactivate_preprocessing_step($json_encoded_parameters)
    {
        //Instanciate the workflow
        $workflow = new Swiftriver\Core\Workflows\PreProcessingSteps\DeactivatePreProcessingStep();

        //run the workflow
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);

        //return the json
        return $json;
    }

    public function save_preprocessing_step($json_encoded_parameters)
    {
        //Instanciate the workflow
        $workflow = new Swiftriver\Core\Workflows\PreProcessingSteps\SavePreProcessingStep();

        //run the workflow
        $json = $workflow->RunWorkflow($json_encoded_parameters, $this->apiKey);

        //return the json
        return $json;
    }
}
?>
